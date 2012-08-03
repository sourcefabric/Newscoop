<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

require_once __DIR__ . '/../../../controllers/OmnitickerController.php';

/**
 */
class Api_OmnitickerController extends OmnitickerController
{
    const TYPE_TWITTER = 'tweet';
    const TYPE_NEWSWIRE = 'newswire';
    const TYPE_LINK = 'link';
    const TYPE_DOSSIER = 'dossier';
    const TYPE_COMMENT = 'comment';
    const TYPE_USER = 'user';
    const TYPE_EVENT = 'event';

    const EN_SECTION_ID = 90;
    const EN_SECTION_NAME = 'Swissinfo';
    const EN_SECTION_TYPE = 'english_news';

    /** @var array */
    private $literalTypes = array(
        self::TYPE_TWITTER,
        self::TYPE_LINK,
        self::TYPE_NEWSWIRE,
        self::TYPE_COMMENT,
        self::TYPE_EVENT,
    );

    /** @var int */
    private $rank = 1;

    /** @var array */
    private $commentStats = array();

    /**
     * @var array
     */
    private $topicTypes = array('newswire', 'news', 'blog', 'dossier');

    /**
     * @var array
     */
    private $fields = array(
        'community' => array(
            'short_title',
            'teaser',
            'link_url',
        ),
        'link' => array(
            'short_title',
            'teaser',
            'link_url',
        ),
        'event' => array(
            'short_title',
            'teaser',
            'link_url',
        ),
        'comment' => array(
            'short_title',
            'teaser',
            'website_url',
        ),
    );

    public function indexAction()
    {
        parent::indexAction();
        $this->commentStats = $this->_helper->service('comment')->getArticleStats(array_filter(array_map(function($doc) {
            return Api_OmnitickerController::getArticleId($doc);
        }, $this->view->result['response']['docs'])));
        $this->getResponse()->setHeader('Expires', $this->getExpires(), true);
        $this->getResponse()->setHeader('Cache-Control', 'public', true);
        $this->getResponse()->setHeader('Pragma', '', true);
        $this->_helper->json(array_map(array($this, 'formatDoc'), (array) $this->view->result['response']['docs']));
    }

    /**
     * Format document for api
     *
     * @param array $doc
     * @return array
     */
    private function formatDoc(array $doc)
    {
        $id = self::getArticleId($doc);
        return $this->filterDoc(array(
            'article_id' => $id,
            'short_title' => $this->formatTitle($doc),
            'teaser' => $this->formatTeaser($doc),
            'url' => $this->formatUrl($id),
            'backside_url' => $this->formatUrl($id, false),
            'link_url' => $this->formatLinkUrl($doc),
            'website_url' => !empty($doc['link']) ? $doc['link'] : null,
            'section_id' => !empty($doc['section_id']) ? (int) $doc['section_id'] : null,
            'section_name' => !empty($doc['section_name']) ? $doc['section_name'] : null,
            'comment_url' => $id ? $this->view->serverUrl($this->view->url(array(
                'module' => 'api',
                'controller' => 'comments',
                'action' => 'list',
            )) . sprintf('?article_id=%d&version=%s', $id, $this->getVersion())) : null,
            'comment_count' => isset($this->commentStats[$id]) ? $this->commentStats[$id]['normal'] : null,
            'recommended_comment_count' => isset($this->commentStats[$id]) ? $this->commentStats[$id]['recommended'] : null,
            'source' => $this->formatType($doc),
            'rank' => $this->rank++,
            'last_modified' => null,
            'published' => $this->formatPublished($doc),
            'topics' => $this->formatTopics($doc),
        ));
    }

    /**
     * Filter out unused fields
     *
     * @param array $doc
     * @return array
     */
    private function filterDoc(array $doc)
    {
        if (isset($this->fields[$doc['source']])) {
            foreach (array_keys($doc) as $key) {
                if (!in_array($key, $this->fields[$doc['source']])
                    && !in_array($key, array('source', 'rank'))) {
                    unset($doc[$key]);
                }
            }
        }

        return $doc;
    }

    /**
     * Format doc url
     *
     * @param int $articleId
     * @param bool $isFront
     * @return string
     */
    private function formatUrl($articleId, $isFront = true)
    {
        if (empty($articleId)) {
            return null;
        }

        return $this->view->serverUrl($this->view->url(array(
            'module' => 'api',
            'controller' => 'articles',
            'action' => 'item',
        )) . sprintf('?article_id=%d&side=%s&client=%s&version=%s', $articleId, $isFront ? 'front' : 'back', $this->getClient(), $this->getVersion()));
    }

    /**
     * Format link url
     *
     * @param array $doc
     * @return string
     */
    private function formatLinkUrl(array $doc)
    {
        if (!empty($doc['link_url'])) {
            return $doc['link_url'];
        }

        if (!empty($doc['tweet'])) {
            $matches = array();
            if (preg_match('#http://t.co/[a-z0-9]+#i', $doc['tweet'], $matches) === 1) {
                return $matches[0];
            }
        }

        switch ($doc['type']) {
            case self::TYPE_USER:
                return $this->view->serverUrl($this->view->url(array(
                    'username' => $doc['user'],
                ), 'user'));
        }
    }

    /**
     * Format published datetime
     *
     * @param array $doc
     * @return string
     */
    private function formatPublished(array $doc)
    {
        $published = new DateTime($doc['published']);
        $published->setTimezone(new DateTimeZone('Europe/Berlin'));
        return $published->format('Y-m-d H:i:s');
    }

    /**
     * Format document title
     *
     * @param array $doc
     * @return string
    */
    private function formatTitle(array $doc)
    {
        switch ($doc['type']) {
            case self::TYPE_TWITTER:
                return $doc['tweet_user_screen_name'];

            case self::TYPE_LINK:
                return $doc['title'];

            case self::TYPE_COMMENT:
                return $doc['subject'];

            case self::TYPE_USER:
                return $doc['user'];

            default:
                return !empty($doc['title']) ? $doc['title'] : null;
        }
    }

    /**
     * Format document teaser
     *
     * @param array $doc
     * @return string
     */
    private function formatTeaser(array $doc)
    {
        switch ($doc['type']) {
            case self::TYPE_TWITTER:
                return $doc['tweet'];

            case self::TYPE_LINK:
                return $doc['link_description'];

            case self::TYPE_COMMENT:
                return $doc['message'];

            case self::TYPE_USER:
                return $doc['bio'];

            case self::TYPE_EVENT:
                return sprintf('%s %s, %s %s %s',
                    $doc['event_organizer'],
                    $doc['event_town'],
                    $doc['event_date'],
                    $doc['event_time'],
                    empty($doc['event_time']) ? '' : 'Uhr');

            default:
                return !empty($doc['lead']) ? $doc['lead'] : null;
        }
    }

    /**
     * Format document type
     *
     * @param array $doc
     * @return string
     */
    private function formatType(array $doc)
    {
        if ($this->isEnglishNews($doc)) {
            return self::EN_SECTION_TYPE;
        } else if (in_array($doc['type'], $this->literalTypes)) {
            return $doc['type'];
        }

        if ($doc['type'] === self::TYPE_USER) {
            return 'community';
        }

        if ($doc['type'] === self::TYPE_BLOG && $this->isSearch()) {
            return 'blogpost';
        }

        if ($doc['type'] === self::TYPE_DOSSIER && $this->isSearch()) {
            return self::TYPE_DOSSIER;
        }

        return $this->isSearch() ? 'article' : 'tageswoche';
    }

    /**
     * Test if is search
     *
     * @return bool
     */
    protected function isSearch()
    {
        return $this->getRequest()->getControllerName() === 'search';
    }

    /**
     * Test if is english news
     *
     * @param array $doc
     * @return bool
     */
    private function isEnglishNews(array $doc)
    {
        return (!empty($doc['section_id']) && $doc['section_id'] == self::EN_SECTION_ID)
            || (!empty($doc['section_name']) && $doc['section_name'] === self::EN_SECTION_NAME);
    }

    /**
     * Build date range query
     *
     * @return string
     */
    protected function buildSolrDateParam()
    {
        if (!$this->_getParam('start_date')) {
            return;
        }

        try {
            $startDate = new DateTime($this->_getParam('start_date'));
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }

        $endDate = $startDate;
        if ($this->_getParam('end_date')) {
            try {
                $endDate = new DateTime($this->_getParam('end_date'));
            } catch (Exception $e) {
                $this->sendError($e->getMessage());
            }
        }

        return sprintf('published:[%s/DAY TO %s+1DAY/DAY]', // it's <,) interval thus +1 day
            $startDate->format('Y-m-d\TH:i:s\Z'),
            $endDate->format('Y-m-d\TH:i:s\Z'));
    }

    /**
     * Build solr params
     *
     * @return array
     */
    protected function buildSolrParams()
    {
        return array_merge(parent::buildSolrParams(), array(
            'rows' => $this->_getParam('start_date') && !$this->_getParam('query_string') ? 200 : 30,
        ));
    }

    /**
     * Send error and exit
     *
     * @param string $body
     * @param int $code
     * @return void
     */
    protected function sendError($body = '', $code = 400)
    {
        $this->getResponse()->setHttpResponseCode($code);
        $this->_helper->json(array(
            'code' => $code,
            'message' => $body,
        ));
    }

    /**
     * Get expires string
     *
     * @return string
     */
    private function getExpires()
    {
        $now = new DateTime();
        $start = new DateTime($this->_getParam('start_date') ?: 'now');
        $expires = new DateInterval($start->format('Y-m-d') === $now->format('Y-m-d') || $start->getTimestamp() > $now->getTimestamp() ? 'PT5M' : 'P300D');
        return $now->add($expires)->format(DateTime::RFC1123);
    }

    /**
     * Get article id
     *
     * @param array $doc
     * @return int
     *
     * static to work within closure
     */
    public static function getArticleId(array $doc)
    {
        return strpos($doc['id'], 'article-') === 0 ? (int) preg_replace('/(^article-)|([0-9]+-$)/', '', $doc['id']) : null;
    }

    /**
     * Get client identifier
     *
     * @return string
     */
    protected function getClient()
    {
        return $this->_getParam('client', 'iphone');
    }

    /**
     * Get api version
     *
     * @return string
     */
    protected function getVersion()
    {
        return sprintf('%.1f', $this->_getParam('version', 1.0));
    }

    /**
     * Format topics
     *
     * @param array $doc
     * @return array
     */
    protected function formatTopics($doc)
    {
        if (!in_array($doc['type'], $this->topicTypes)) {
            return array();
        }

        $ids = explode('-', $doc['id']);
        $article = $this->_helper->service('article')->findOneByNumber($ids[1]);
        if (empty($article)) {
            return array();
        }

        $topics = array();
        foreach ($article->getTopicNames() as $id => $name) {
            $topics[] = array(
                'topic_id' => $id,
                'topic_name' => $name,
            );
        }

        return $topics;
    }
}
