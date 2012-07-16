<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Article;

require_once __DIR__ . '/AbstractController.php';

/**
 */
class Api_HighlightsController extends AbstractController
{
    const LANGUAGE = 5;
    const PUBLICATION = 1;

    const IMAGE_TOP_RENDITION = 'topfront';
    const IMAGE_STANDARD_RENDITION = 'rubrikenseite';
    const IMAGE_TOP_WIDTH = 320;
    const IMAGE_TOP_HEIGHT = 140;
    const IMAGE_STANDARD_WIDTH = 105;
    const IMAGE_STANDARD_HEIGHT = 70;
    const IMAGE_RETINA_FACTOR = 2;

    /** @var Zend_Controller_Request_Http */
    private $request;

    /** @var array */
    private $response = array();

    /**
     * Init controller.
     */
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->request = $this->getRequest();
        $this->params = $this->request->getParams();

        if (empty($this->params['client'])) {
            print Zend_Json::encode(array());
            exit;
        }

        $this->initClient($this->params['client']);
        if (is_null($this->client['type'])) {
            print Zend_Json::encode(array());
            exit;
        }
    }

    /**
     * Default action controller.
     */
    public function indexAction()
    {
        $this->_forward('list');
    }

    /**
     * Return list of articles.
     *
     * @return json
     */
    public function listAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('list', 'json')->initContext();
        $response = array();

        $params = $this->request->getParams();
        if (isset($params['section_id'])) {
            $sectionIds = array((int) $params['section_id']);
        } else {
            $sectionIds = array(6, 7, 8, 9, 10, 11, 25); // @todo config
        }

        $sectionRank = 1;
        foreach ($sectionIds as $sectionId) {
            $playlistRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Playlist');
            $playlist = $playlistRepository->findOneBy(array('id' => $sectionId));
            if ($playlist) {
                $articleArray = $playlistRepository->articles($playlist, null, false, 3);
                $rank = 1;
                foreach ($articleArray as $articleItem) {
                    $articles = $this->_helper->service('article')->findBy(array('number' => $articleItem['articleId']));
                    $article = $articles[0];

                    // gets the article image in the proper size
                    if ($sectionId == 6 && $rank == 1) {
                        $width = $this->isClientRetina() ? self::IMAGE_TOP_WIDTH * self::IMAGE_RETINA_FACTOR : self::IMAGE_TOP_WIDTH;
                        $height = $this->isClientRetina() ? self::IMAGE_TOP_HEIGHT * self::IMAGE_RETINA_FACTOR : self::IMAGE_TOP_HEIGHT;
                        $image = $this->lookforImageUrl($article, self::IMAGE_TOP_RENDITION, $width, $height);
                    } else {
                        $image = $this->lookforImageUrl($article, self::IMAGE_STANDARD_RENDITION, $this->client['image_width'], $this->client['image_height']);
                    }

                    $response = array(
                        'article_id' => (int) $article->getNumber(),
                        'url' => $this->getArticleUrl($article),
                        'backside_url' => $this->getArticleUrl($article, self::BACK_SIDE),
                        'dateline' => $this->getDateline($article),
                        'short_name' => $this->getShortname($article),
                        'published' => $this->formatDate($article->getPublished()),
                        'teaser' => $this->getTeaser($article),
                        'image_url' => $image,
                        'website_url' => $this->_helper->service('article.link')->getLink($article),
                        'comment_count' => $this->getCommentsCount($article),
                        'recommended_comment_count' => $this->getCommentsCount($article, true),
                        'comment_url' => $this->getCommentsUrl($article),
                        'topics' => $this->getTopics($article),
                        'rank' => $rank++,
                        'section_id' => (int) $sectionId,
                        'section_name' => $playlist->getName(),
                        'section_url' => $this->getSectionUrl($sectionId),
                        'section_rank' => $sectionRank,
                    );

                    if ($this->client['type'] == 'iphone') {
                        unset($response['teaser']);
                    }

                    $this->response[] = $response;
                }
                $sectionRank++;
            }
        }

        $this->_helper->json($this->response);
    }

    private function lookforImageUrl(Article $article, $rendition, $width, $height)
    {
        $image = $this->lookforImage($article, $rendition);
        if (empty($image)) {
            return null;
        }

        $imageUrl = $this->view->url(array(
            'src' => $this->getHelper('service')->getService('image')->getSrc(basename($image->src), $width, $height, 'crop'),
        ), 'image', false, false);

        return $this->view->serverUrl($imageUrl);
    }

    /**
     * Return image url
     *
     * @param Article $article
     * @return string $thumbnail
     */
    private function lookforImage(Article $article, $rendition)
    {
        $renditions = Zend_Registry::get('container')->getService('image.rendition')->getRenditions();
        if (!array_key_exists($rendition, $renditions)) {
            return null;
        }

        $articleRenditions = Zend_Registry::get('container')
            ->getService('image.rendition')->getArticleRenditions($article->getId());
        $articleRendition = $articleRenditions[$renditions[$rendition]];

        if ($articleRendition === null) {
            return null;
        }

        $thumbnail = $articleRendition->getRendition()->
            getThumbnail($articleRendition->getImage(), Zend_Registry::get('container')->getService('image'));

        return $thumbnail;
    }

    /**
     * Get article comments api url
     *
     * @param int $section Section (playlist) identifier
     * @return string
     */
    protected function getSectionUrl($section)
    {
        return $this->view->serverUrl($this->view->url(array(
            'module' => 'api',
            'controller' => 'articles',
            'action' => 'list',
        ), 'default') . $this->getApiQueryString(array(
            'section_id' => $section,
        )));
    }

    private function initClient($client)
    {
        $type = null;
        if (strstr($client, 'ipad')) {
            $type = 'ipad';
        } elseif (strstr($client, 'iphone')) {
            $type = 'iphone';
        }

        $this->client = array(
            'name' => $client,
            'type' => $type,
            'image_width' => self::IMAGE_STANDARD_WIDTH,
            'image_height' => self::IMAGE_STANDARD_HEIGHT,
        );

        if ($this->isClientRetina()) {
            $this->client['image_width'] = $this->client['image_width'] * self::IMAGE_RETINA_FACTOR;
            $this->client['image_height'] = $this->client['image_height'] * self::IMAGE_RETINA_FACTOR;
        }
    }

    private function isClientRetina()
    {
        return $this->client['name'] == 'ipad_retina' || $this->client['name'] == 'iphone_retina';
    }
}

