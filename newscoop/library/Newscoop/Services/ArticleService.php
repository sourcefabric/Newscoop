<?php
/**
 * @package Newscoop
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\Article;
use Newscoop\Entity\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Manage requested article
 */
class ArticleService
{
    /**
     * Entity Manager
     * @var EntityManager
     */
    protected $em;

    /**
     * Article object
     * @var Article
     */
    protected $article;

    /**
     * Article metadata.
     * @var array
     */
    protected $articleMetadata = array();

    /**
     * Construct Article Service
     * @param EntityManager $em Entity Manager
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Get Article object
     * @return Article Article entity object
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set Article object
     * @param Article $article Article entity object
     */
    public function setArticle(Article $article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article metadata
     * @return array article metadata
     */
    public function getArticleMetadata()
    {
        return $this->articleMetadata;
    }

    /**
     * Resolve article from provided data
     *
     * @param Request $request Request object
     *
     * @return Article $article Article entity object
     */
    public function articleResolver(Request $request)
    {
        // get the article information from the URL
        // explode the information and use it to fetch the article
        $uriExplode = explode('/', $request->server->get('REQUEST_URI'));

        // Articles are allways under newscoop_zendbridge_bridge_index_3 route
        if ($request->attributes->get('_route') != 'newscoop_zendbridge_bridge_index_3') {
            return null;
        }

        // if key 4 does not exist, it's probably not an article
        if (array_key_exists(4, $uriExplode) && $uriExplode[4] !== '') {
            $articleInfo['id'] = $uriExplode[4];
            $articleInfo['lang'] = $uriExplode[1];
            $articleInfo['section'] = $uriExplode[3];

            $query = $this->em->createQuery('SELECT a, p, i, s FROM Newscoop\Entity\Article a LEFT JOIN a.packages p LEFT JOIN a.issue i LEFT JOIN a.section s LEFT JOIN a.language l WHERE a.number = :number AND l.code = :code');
            $article = $query->setParameters(array('number'=> $articleInfo['id'], 'code' => $articleInfo['lang']))
                ->getArrayResult();

            if (!empty($article)) {
                // fill the article meta data
                $this->articleMetadata['id']            = $article[0]['number'];
                $this->articleMetadata['name']          = $article[0]['name'];
                $this->articleMetadata['issue']         = $article[0]['issue']['name'];
                $this->articleMetadata['issue_id']      = $article[0]['issueId'];
                $this->articleMetadata['section']       = $article[0]['section']['name'];
                $this->articleMetadata['section_id']    = $article[0]['sectionId'];
                $this->articleMetadata['language_code'] = $articleInfo['lang'];
                $this->articleMetadata['language_id']   = $article[0]['IdLanguage'];

                // add the meta data to the request
                $request->attributes->set('_newscoop_article_metadata', $this->articleMetadata);

                return true;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Create new article
     *
     * @param string  $articleType
     * @param integer $language
     * @param User    $user
     * @param integer $publication
     * @param array   $attributes
     * @param integer $issue
     * @param integer $section
     *
     * @return Article
     */
    public function createArticle($articleType, $language, $user, $publication, $attributes = array(), $issue = null, $section = null)
    {
        $this->checkForArticleConflicts($attributes['name'], $publication, $issue, $section);

        $article = new Article(
            $this->em->getRepository('Newscoop\Entity\AutoId')->getNextArticleNumber(),
            $language
        );

        if (!$section) {
            $articleOrder = $article->getNumber();
        } else {
            $minArticleOrder = $this->em->getRepository('Newscoop\Entity\Article')
                ->getMinArticleOrder($publication, $issue, $section)
                ->getSingleScalarResult();


            $increment = $minArticleOrder > 0 ? 1 : 2;
            $this->em->getRepository('Newscoop\Entity\Article')
                ->updateArticleOrder($increment, $publication, $issue, $section)
                ->getResult();

            $articleOrder = 1;
        }
        $article->setArticleOrder($articleOrder);
        $article->setPublication($publication);
        $article->setType($articleType);
        $article->setCreator($user);

        $article->setIssueId((!is_null($issue)) ? $issue->getId() : 0);
        $article->setSectionId((!is_null($section)) ? $section->getId() : 0);

        $this->updateArticleMeta($article, $attributes);

        $article->setCommentsLocked(false); //TODO - add this to type
        $article->setWorkflowStatus('N');
        $article->setShortName($article->getNumber());
        $article->setLockTime(null);
        $article->setPublished(new \Datetime());
        $article->setUploaded(new \Datetime());
        $article->setLockUser();
        $article->setPublic(true);
        $article->setIsIndexed('N');

        $this->em->persist($article);
        $this->em->flush();

        $articleData = new \ArticleData($article->getType(), $article->getNumber(), $article->getLanguageId());
        $articleData->create();

        return $article;
    }

    /**
     * Update article
     *
     * @param Article $article
     * @param array   $attributes
     *
     * @return Article
     */
    public function updateArticle($article, $attributes)
    {
        $this->updateArticleMeta($article, $attributes);
        $article->setUpdated(new \DateTime());
        $article->setIsIndexed('N');

        if (array_key_exists('fields', $attributes)) {
            foreach ($attributes['fields'] as $field => $value) {
                $article->setFieldData($field, $value);
            }
        }

        $this->em->flush();

        return $article;
    }

    /**
     * Update Article static properties
     *
     * @param Article $article
     * @param array   $attributes
     *
     * @return Article
     */
    private function updateArticleMeta($article, $attributes)
    {
        $article->setName($attributes['name']);
        $article->setCommentsEnabled($attributes['comments_enabled']);
        $article->setCommentsLocked($attributes['comments_locked']);
        $article->setOnFrontPage($attributes['onFrontPage']);
        $article->setOnSection($attributes['onSection']);
        $article->setKeywords($attributes['keywords']);

        return $article;
    }

    /**
     * Check if combination of article name, publication, issue and section is unique
     *
     * @param string                              $articleTitle
     * @param integer|Newscoop\Entity\Publication $publication
     * @param integer|Newscoop\Entity\Issue       $issue
     * @param integer|Newscoop\Entity\Section     $section
     *
     * @return boolean|Newscoop\Exception\ResourcesConflictException
     */
    private function checkForArticleConflicts($articleTitle, $publication, $issue, $section)
    {
        $conflictingArticles = $this->em->getRepository('Newscoop\Entity\Article')->findBy(array(
            'name' => $articleTitle,
            'publication' => $publication,
            'issue' => $issue,
            'section' => $section
        ));

        if (count($conflictingArticles) > 0) {
            throw new \Newscoop\Exception\ResourcesConflictException(
                "You cannot have two articles in the same section with the same name. The article name you specified is already in use by the article ".$conflictingArticles[0]->getNumber() ." '".$conflictingArticles[0]->getName()."'"
            );
        }

        return true;
    }
}
