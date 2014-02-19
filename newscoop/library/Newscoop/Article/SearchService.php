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

/**
 * Search Service
 */
class SearchService implements ServiceInterface
{
    /**
     * @var Newscoop\Webcode\Mapper
     */
    private $webcoder;

    /**
     * @var Newscoop\Image\RenditionService
     */
    private $renditionService;

    /**
     * @var Newscoop\Article\LinkService
     */
    private $linkService;

    /**
     * @var array
     */
    private $config = array(
        'type' => array(), // TODO: Extrend with indexable article types
        'rendition' => null,
        'blogs' => array('blog', 'bloginfo'),
    );

    /**
     * @var array
     */
    private $switches = array(
        'print',
    );

    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

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
    )
    {
        $this->webcoder = $webcoder;
        $this->renditionService = $renditionService;
        $this->linkService = $linkService;
        //$this->config = array_merge($this->config, $config);
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
            //&& in_array($article->getType(), $this->config['type'])
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

        $doc = array(
            'id' => $this->getDocumentId($article),
            'title' => $article->getTitle(),
            'type' => in_array($article->getType(), $this->config['blogs']) ? 'blog' : $article->getType(),
            'published' => gmdate(self::DATE_FORMAT, $article->getPublishDate()->getTimestamp()),
            'updated' => gmdate(self::DATE_FORMAT, $article->getDate()->getTimestamp()),
            'author' => array_map(function($author) {
                return $author->getFullName();
            }, (is_array($article->getArticleAuthors())) ? $article->getArticleAuthors() : array()),
            'webcode' => $this->webcoder->getArticleWebcode($article),
            'image' => $image ? $image['src'] : null,
            'link' => $this->linkService->getLink($article),
            'section' => $this->linkService->getSectionShortName($article),
            'section_name' => ($article->getSection()) ? $article->getSection()->getName() : null,
            'section_id' => $article->getSectionId(),
            'keyword' => explode(',', $article->getKeywords()),
            'topic' => array_values($article->getTopicNames()),
            'switches' => $this->getArticleSwitches($article),
        );

        // TODO: Extend this via class, instead of in core code

        switch ($article->getType()) {
            case 'blog':
            case 'news':
                $doc['lead'] = strip_tags($article->getData('lede'));
                $doc['content'] = strip_tags($article->getData('body'));
                $doc['title_short'] = strip_tags($article->getData('short_name'));
                break;

            case 'dossier':
                $doc['lead'] = strip_tags($article->getData('teaser'));
                $doc['content'] = strip_tags($article->getData('lede') . "\n" . $article->getData('history'));
                $doc['title'] = array(
                    'value' => $doc['title'],
                    'boost' => 1.5,
                );
                break;

            case 'newswire':
                $doc['lead'] = strip_tags($article->getData('DataLead'));
                $doc['content'] = strip_tags($article->getData('DataContent'));
                $doc['lead_short'] = strip_tags($article->getData('NewsLineText'));
                $doc['dateline'] = strip_tags($article->getData('Location'));
                break;

            case 'link':
                $doc['link_url'] = $article->getData('link_url');
                $doc['link_description'] = strip_tags($article->getData('link_description'));
                break;

            case 'event':
                $doc['event_organizer'] = $article->getData('organizer');
                $doc['event_town'] = $article->getData('town');
                $doc['link_url'] = $article->getData('web');

                $date = $this->getArticleDateTime($article);
                if ($date !== null) {
                    $doc['event_date'] = $date->getStartDate()->format('d.m.Y');
                    $doc['event_time'] = $date->getStartTime()->format('H:i');
                }
                break;

            case 'bloginfo':
                $doc['lead'] = strip_tags($article->getData('motto'));
                $doc['content'] = strip_tags($article->getData('infolong'));
                break;
        }

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
    private function getArticleDatetime($article)
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
    private function getArticleSwitches($article)
    {
        $switches = array();

        foreach ($this->switches as $switch) {
            try {
                if ($article->getData($switch)) {
                    $switches[] = $switch;
                }
            } catch (\Exception $e) {
                // @noop
            }
        }

    }
}
