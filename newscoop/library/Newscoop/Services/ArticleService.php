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
use Newscoop\Entity\Language;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Manage requested article
 */
class ArticleService
{
    /**
     * Entity Manager
     * @var EntityManager
     */
    private $em;

    /**
     * Article object
     * @var Article
     */
    private $article;

    /**
     * Article metadata.
     * @var array
     */
    private $articleMetadata = array();

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
     * @param  Request $request Request object
     * @return Article $article Article entity object
     */
    public function articleResolver(Request $request)
    {
        // get the article information from the URL
        // explode the information and use it to fetch the article
        $uri_explode = explode('/', $request->server->get('REQUEST_URI'));

        // if key 4 does not exist, it's probably not an article
        if (array_key_exists(4, $uri_explode)) {
            $articleInfo['id'] = $uri_explode[4];
            $articleInfo['lang'] = $uri_explode[1];
            $articleInfo['section'] = $uri_explode[3];

            $article = $this->em->getRepository('Newscoop\Entity\Article')
                                ->getArticle($articleInfo['id'], $articleInfo['lang'])
                                ->getOneOrNullResult();

            if (!is_null($article)) {
                // fill the article meta data
                $this->articleMetadata['id']            = $article->getId();
                $this->articleMetadata['name']          = $article->getName();
                $this->articleMetadata['issue']         = $article->getIssue()->getName();
                $this->articleMetadata['issue_id']      = $article->getIssueId();
                $this->articleMetadata['section']       = $article->getSection()->getName();
                $this->articleMetadata['section_id']    = $article->getSectionId();
                $this->articleMetadata['language_code'] = $article->getLanguageCode();
                $this->articleMetadata['language_id']   = $article->getLanguageId();

                // add the meta data to the request
                $request->attributes->set('_newscoop_article_metadata', $this->articleMetadata);

                // return the article
                return $article;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}