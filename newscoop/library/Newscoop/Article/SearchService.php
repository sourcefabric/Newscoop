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
        'rendition' => null,
        'type' => array('all')
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
        EntityManager $em
    ) {
        $this->webcoder = $webcoder;
        $this->renditionService = $renditionService;
        $this->linkService = $linkService;
        $this->em = $em;
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
     * Return sub type for the document
     *
     * @return string identifier
     */
    public function getSubType(DocumentInterface $article)
    {
        return $article->getType();
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
        $image = null;
        $renditions = $this->renditionService->getRenditions();
        if (is_array($renditions) && count($renditions) > 0) {
            $image = $this->renditionService->getArticleRenditionImage($article->getNumber(), key($renditions));
        }

        $webcode = $this->webcoder->getArticleWebcode($article);
        if (strpos($webcode, 0, 1) != '+') {
            $webcode = '+'.$webcode;
        }

        $doc = array(
            'id' => $this->getDocumentId($article),
            'number' => $article->getNumber(),
            'type' => $article->getType(),
            'webcode' => $webcode,
            'title' => $article->getTitle(),
            'updated' => gmdate(self::DATE_FORMAT, $article->getDate()->getTimestamp()),
            'published' => gmdate(self::DATE_FORMAT, $article->getPublishDate()->getTimestamp()),
            'image' => $image ? $image['src'] : null,
            'link' => $this->linkService->getLink($article),

            'language' => $article->getLanguageCode(),
            'language_id' => $article->getLanguageId(),

            'publication_number' => $article->getPublication() ? $article->getPublication()->getId() : null,
            'issue_number' => $article->getIssue() ? $article->getIssue()->getNumber() : null,
            // TODO: check if we can remove one
            'section_number' => $article->getSection() ? $article->getSection()->getNumber() : null,
            'section_id' => $article->getSection() ? $article->getSection()->getNumber() : null,

            'section' => $this->linkService->getSectionShortName($article),
            'section_name' => $article->getSection() ? $article->getSection()->getName() : null,
            'authors' => $article->getArticleAuthors()->map(function ($author) {
                return $author->getView()->name;
            })->toArray(),
            'keywords' => array_filter(explode(',', $article->getKeywords())),
            'topics' => array_values($article->getTopicNames()),
            'switches' => $this->getArticleSwitches($article),
        );

        return array_filter($this->addDataFields($doc, $article));
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

    public function searchArticles($articleSearchCriteria, $onlyPublished = true, $returnQuery = false)
    {
        if ($articleSearchCriteria->query) {
            $keywords = array_diff(explode(',', $articleSearchCriteria->query), array(''));

            $webcodeMatches = preg_grep("`^\s*[\+@]`", $keywords);
            if (count($webcodeMatches)) {
                $webcode = ltrim(current($webcodeMatches), '@+');
                $article = $this->webcoder->findArticleByWebcode($webcode);

                if ($article) {
                    return array($article);
                }
            }
        }

        $articlesQuery = $this->em->getRepository('Newscoop\Entity\Article')
            ->searchArticles(
                $articleSearchCriteria,
                $onlyPublished
            );

        if (!$returnQuery) {
           return $articlesQuery->getResult();
        }

        return $articlesQuery;
    }

    /**
     * Add field properties to document
     *
     * @param  array $doc
     *
     * @return array
     */
    private function addDataFields(array $doc, $article)
    {
        $articleData = new \ArticleData($article->getType(), $article->getNumber(), $article->getLanguageId());
        if (count($articleData->getUserDefinedColumns()) == 0) {
            return $doc;
        }

        $fields = array();
        foreach ($articleData->getUserDefinedColumns() as $column) {
            $doc[$column->getPrintName()] = $articleData->getFieldValue($column->getPrintName());
        }

        return $doc;
    }
}
