<?php
/**
 * @package   Newscoop
 * @author    Mischa Gorinskat <mischa.gorinskat@gsourcfabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Article;

use Doctrine\ORM\EntityManager;
use Newscoop\Search\ServiceInterface;
use Newscoop\Search\DocumentInterface;
use Newscoop\WebcodeFacade;
use Newscoop\Image\RenditionService;
use Newscoop\Article\LinkService;
use Newscoop\Entity\Article;

/**
 * Search Service
 */
class SearchService implements ServiceInterface
{
    /**
     * @var Newscoop\Webcode\Mapper
     */
    protected $webcoder;

    /**
     * @var Newscoop\Image\RenditionService
     */
    protected $renditionService;

    /**
     * @var Newscoop\Article\LinkService
     */
    protected $linkService;

    /**
     * @var array
     */
    protected $config = array(
        'rendition' => null
    );

    /**
     * @var array
     */
    protected $switches = array(
        'print',
    );

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param Newscoop\Webcode\Mapper $webcoder
     * @param Newscoop\Image\RenditionService $renditionService
     * @param Newscoop\Article\LinkService $linkService
     * @param array $config
     */
    public function __construct(
        WebcodeFacade $webcoder,
        RenditionService $renditionService,
        LinkService $linkService,
        EntityManager $em,
        $config = null
    )
    {
        $this->webcoder = $webcoder;
        $this->renditionService = $renditionService;
        $this->linkService = $linkService;
        $this->em = $em;
        if ($config !== null &&  is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * Return type for this search service
     *
     * @return string identifier
     */
    public function getType()
    {
        return 'article';
    }

    /**
     * Test if article is indexed
     *
     * @param Newscoop\Entity\Article $article
     * @return bool
     */
    public function isIndexed(DocumentInterface $article)
    {
        return $article->getIndexed() !== null;
    }

    /**
     * Test if article can be indexed
     *
     * @param Newscoop\Entity\Article $article
     * @return bool
     */
    public function isIndexable(DocumentInterface $article)
    {
        return $article->isPublished()
            && in_array($article->getType(), $this->config['type'])
            && $article->getLanguageId() > 0
            && $article->getSectionId() > 0;
    }

    /**
     * Get document representation for article
     *
     * @param Newscoop\Entity\Article $article
     * @return array
     */
    public function getDocument(DocumentInterface $article)
    {
        $image = $this->renditionService->getArticleRenditionImage($article->getNumber(), $this->config['rendition'], 200, 150);

        $webcode = $this->webcoder->getArticleWebcode($article);
        if (strpos($webcode, 0, 1) != '+') {
            $webcode = '+'.$webcode;
        }

        $doc = array(
            'id' => $this->getDocumentId($article),
            'title' => $article->getTitle(),
            'type' => $article->getType(),
            'published' => gmdate(self::DATE_FORMAT, $article->getPublishDate()->getTimestamp()),
            'updated' => gmdate(self::DATE_FORMAT, $article->getDate()->getTimestamp()),
            'author' => array_map(function($author) {
                return $author->getFullName();
            }, (is_array($article->getArticleAuthors())) ? $article->getArticleAuthors() : array()),
            'webcode' => $webcode,
            'image' => $image ? $image['src'] : null,
            'link' => $this->linkService->getLink($article),
            'section' => $this->linkService->getSectionShortName($article),
            'section_name' => ($article->getSection()) ? $article->getSection()->getName() : null,
            'section_id' => $article->getSectionId(),
            'keyword' => explode(',', $article->getKeywords()),
            'topic' => array_values($article->getTopicNames()),
            'switches' => $this->getArticleSwitches($article),
        );

        return array_filter($doc);
    }

    /**
     * Get document id
     *
     * @param Newscoop\Entity\Article $article
     * @return string
     */
    public function getDocumentId(DocumentInterface $article)
    {
        return sprintf('%s-%d-%d', $this->getType(), $article->getNumber(), $article->getLanguageId());
    }

    /**
     * Get event article datetime
     *
     * @param Newscoop\Entity\Article $article
     * @return ArticleDatetime
     */
    public function getArticleDatetime($article)
    {
        return $this->em->getRepository('Newscoop\Entity\ArticleDatetime')->findOneBy(array(
            'articleId' => $article->getNumber(),
            'fieldName' => 'schedule',
        ));
    }

    /**
     * Get article switches
     *
     * @param Newscoop\Entity\Article $article
     * @return array
     */
    public function getArticleSwitches($article)
    {
        $switches = array();

        foreach ($this->switches as $switch) {
            try {
                if ($article->getData($switch)) {
                    $switches[] = $switch;
                }
            } catch (\Exception $e) {/*just ignore if switch don't exists*/}
        }
    }

    public function searchArticles($language, $query = null, $publication = false, $issue = false, $section = false, $onlyPublished = true)
    {
        $keywords = array_diff(explode(',', $query), array(''));

        $webcodeMatches = preg_grep("`^\s*[\+@]`", $keywords);
        if (count($webcodeMatches)) {
            $webcode = ltrim(current($webcodeMatches), '@+');
            $article = $this->webcoder->findArticleByWebcode($webcode);

            if ($article) {
                return array($article);
            }
        }

        $articles = $this->em->getRepository('Newscoop\Entity\Article')
            ->searchArticles(
                $language,
                $keywords,
                $publication,
                $issue,
                $section,
                $onlyPublished
            )
            ->getResult();

        return $articles;
    }
}
