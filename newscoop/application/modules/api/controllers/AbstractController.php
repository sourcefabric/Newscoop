<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
abstract class AbstractController extends Zend_Controller_Action
{
    const DIGITAL_UPGRADE = '_digital_upgrade';

    const DATE_FORMAT = 'Y-m-d H:i:s';

    const SECRET_PARAM = '_secret';

    const CLIENT_DEFAULT = 'iphone';
    const VERSION_DEFAULT = '1.0';

    const FRONT_SIDE = 'front';
    const BACK_SIDE = 'back';

    const IMAGE_STANDARD_RENDITION = 'rubrikenseite';

    const IMAGE_STANDARD_WIDTH = 105;
    const IMAGE_STANDARD_HEIGHT = 70;
    const IMAGE_RETINA_FACTOR = 2;

    /** @var int */
    private $rank = 1;

    /** @var array */
    private $fields = array(
        'teaser' => array(
            'newswire' => 'DataLead',
            'blog' => 'lede',
        ),
    );

    /** @var array */
    private $clientSize = array();

    /**
     * Get user by username and password params
     *
     * @return Newscoop\Entity\User
     */
    protected function getUser()
    {
        $username = $this->_getParam('username');
        $password = $this->_getParam('password');
        if (empty($username) || empty($password)) {
            $this->sendError('Invalid credentials.', 401);
        }

        $user = $this->_helper->service('auth.adapter')->findByCredentials($username, $password);
        if (empty($user)) {
            $this->sendError('Invalid credentials.', 401);
        }

        return $user;
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
     * Assert request is secure
     *
     * @return void
     */
    protected function assertIsSecure()
    {
        if (APPLICATION_ENV === 'development') {
            return;
        }

        if (!$this->getRequest()->isSecure()) {
            $this->sendError('Secure connection required.');
        }
    }

    /**
     * Assert request is post
     *
     * @return void
     */
    protected function assertIsPost()
    {
        if (!$this->getRequest()->isPost()) {
            $this->sendError('POST required.');
        }
    }

    /**
     * Get client and version params
     *
     * @param bool $onlyParams
     * @return string
     */
    protected function getClientVersionParams($onlyParams = true)
    {
        return sprintf('%sclient=%s&version=%s', $onlyParams ? '?' : '&', $this->_getParam('client', 'ipad'), $this->_getParam('version', '1.0'));
    }

    /**
     * Assert that user is subscriber and can consume premium content
     *
     * @return void
     */
    protected function assertIsSubscriber()
    {
        $options = $this->getInvokeArg('bootstrap')->getOption('offline');
        if (isset($options['secret']) && $this->_getParam(self::SECRET_PARAM) === $options['secret']) {
            return;
        }

        if ($this->_getParam('receipt_data') && $this->_getParam('device_id')) {
            if ($this->_helper->service('mobile.purchase')->isValid($this->_getParam('receipt_data'))) {
                return;
            }
        }

        if ($this->_getParam('username') && $this->_getParam('password')) {
            $user = $this->getUser();
            if ($this->_helper->service('mobile.free_upgrade')->hasIssue($user, $this->_helper->service('mobile.issue')->getCurrentIssueId())) {
                return;
            }

            if ($this->_helper->service('user_subscription')->isActiveSubscriber($user)) {
                return;
            }
        }

        $this->sendError('Unauthorized.', 401);
    }

    /**
     * Get topic api url
     *
     * @param Newscoop\Entity\Topic $topic
     * @return string
     */
    protected function getTopicUrl($topic)
    {
        return $this->view->serverUrl($this->view->url(array(
            'module' => 'api',
            'controller' => 'articles',
            'action' => 'list',
        ), 'default') . $this->getApiQueryString(array(
            'topic_id' => $topic->getTopicId(),
        )));
    }

    /**
     * Get article api url
     *
     * @param Newscoop\Entity\Article $article
     * @param string $side
     * @return string
     */
    protected function getArticleUrl($article, $side = self::FRONT_SIDE)
    {
        return $this->view->serverUrl($this->view->url(array(
            'module' => 'api',
            'controller' => 'articles',
            'action' => 'item',
        ), 'default') . $this->getApiQueryString(array(
            'article_id' => $article->getNumber(),
            'side' => $side,
        )));
    }

    /**
     * Get article comments api url
     *
     * @param mixed $article
     * @return string
     */
    protected function getCommentsUrl($article)
    {
        return $this->view->serverUrl($this->view->url(array(
            'module' => 'api',
            'controller' => 'comments',
            'action' => 'list',
        ), 'default') . $this->getApiQueryString(array(
            'article_id' => $article->getNumber(),
        )));
    }

    /**
     * Get api query string
     *
     * @param array $params
     * @return string
     */
    protected function getApiQueryString(array $params = array())
    {
        $params = array_filter(array_merge($params, array(
            'client' => $this->_getParam('client'),
            'version' => $this->_getParam('version'),
        )));

        return empty($params) ? '' : '?' . implode('&', array_map(function($key) use ($params) {
            return sprintf('%s=%s', $key, $params[$key]);
        }, array_keys($params)));
    }

    /**
     * Get article dateline
     *
     * @param Newscoop\Entity\Article $article
     * @return string
     */
    protected function getDateline($article)
    {
        try {
            $dateline = $article->getData('dateline');
            return !empty($dateline) ? $dateline : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get article website url
     *
     * @param Newscoop\Entity\Article $article
     * @return string
     */
    protected function getWebsiteUrl($article)
    {
        return $this->_helper->service('article.link')->getLink($article);
    }

    /**
     * Get topics
     *
     * @param Newscoop\Entity\Article $article
     * @return array
     */
    protected function getTopics($article)
    {
        $topics = array();
        foreach ($article->getTopicNames() as $id => $name) {
            $topics[] = array(
                'topic_id' => $id,
                'topic_name' => $name,
            );
        }

        return $topics;
    }

    /**
     * Get article image
     *
     * @param Article $article
     * @return string $thumbnail
     */
    protected function getImage($article, $rendition)
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
     * Get article image url
     *
     * @param mixed $article
     * @param string $rendition
     * @param int $width
     * @param int $height
     * @return string
     */
    protected function getImageUrl($article, $rendition = self::IMAGE_STANDARD_RENDITION)
    {
        $image = $this->getImage($article, $rendition);
        if (empty($image)) {
            return null;
        }

        $imageUrl = $this->view->url(array(
            'src' => $this->getHelper('service')->getService('image')->getSrc(basename($image->src), $this->getClientWidth(), $this->getClientHeight(), 'crop'),
        ), 'image', false, false);

        return $this->view->serverUrl($imageUrl);
    }

    /**
     * Get comments count
     *
     * @param mixed Article
     * @param bool $recommended
     * @return int
     */
    protected function getCommentsCount($article, $recommended = false)
    {
        $constraints = array('thread' => $article->getNumber());

        if ($recommended) {
            $constraints['recommended'] = 1;
        }

        return Zend_Registry::get('container')->getService('comment')->countBy($constraints);
    }

    /**
     * Format article for api
     *
     * @param mixed $article
     * @return array
     */
    protected function formatArticle($article)
    {
        $bodyField = 'body';
        $teaserField = 'teaser';
        $datelineField = 'dateline';
        switch($article->getType()) {
            case 'newswire':
                $bodyField = 'DataContent';
                $teaserField = 'DataLead';
                break;

            case 'blog':
                $teaserField = 'lede';
                $datelineField = 'short_name';
                break;
        }

        $data = array(
            'article_id' => $article->getNumber(),
            'url' => $this->getArticleUrl($article),
            'backside_url' => $this->getArticleUrl($article, 'back'),
            'dateline' => $this->getDateline($article),
            'short_name' => $this->getShortname($article),
            'published' => $this->formatDate($article->getPublished()),
            'rank' => $this->rank++,
            'website_url' => $this->getWebsiteUrl($article),
            'image_url' => $this->getImageUrl($article),
            'comment_count' => $this->getCommentsCount($article),
            'recommended_comment_count' => $this->getCommentsCount($article, true),
            'comment_url' => $this->getCommentsUrl($article),
            'topics' => $this->getTopics($article),
        );

        if (!$this->isIphoneClient()) {
            $data['teaser'] = $this->getTeaser($article);
        }

        return $data;
    }

    /**
     * Format datetime
     *
     * @param DateTime $date
     * @return string
     */
    protected function formatDate(\DateTime $date)
    {
        $date->setTimezone(new DateTimeZone('Europe/Berlin'));
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Get teaser
     *
     * @param Newscoop\Entity\Article $article
     * @return string
     */
    protected function getTeaser($article)
    {
        try {
            $field = isset($this->fields['teaser'][$article->getType()])
                ? $this->fields['teaser'][$article->getType()]
                : 'teaser';
            return preg_replace('/(<p>|<p [^>]*>|<\\/p>)/', '', trim($article->getData($field)));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Test if client is iphone
     *
     * @return bool
     */
    protected function isIphoneClient()
    {
        return strpos($this->_getParam('client', 'iphone'), 'iphone') !== false;
    }

    /**
     * Get client width
     *
     * @return int
     */
    protected function getClientWidth()
    {
        if (empty($this->clientSize)) {
            $this->initClientSize();
        }

        return $this->clientSize['width'];
    }

    /**
     * Get client height
     *
     * @return int
     */
    protected function getClientHeight()
    {
        if (empty($this->clientSize)) {
            $this->initClientSize();
        }

        return $this->clientSize['height'];
    }

    /**
     * Init client size
     *
     * @return void
     */
    private function initClientSize()
    {
        $this->clientSize = array(
            'width' => self::IMAGE_STANDARD_WIDTH,
            'height' => self::IMAGE_STANDARD_HEIGHT,
        );

        if ($this->isRetinaClient()) {
            $this->clientSize['width'] *= self::IMAGE_RETINA_FACTOR;
            $this->clientSize['height'] *= self::IMAGE_RETINA_FACTOR;
        }
    }

    /**
     * Test if client is retina
     *
     * @return bool
     */
    private function isRetinaClient()
    {
        return strpos($this->_getParam('client', self::CLIENT_DEFAULT), 'retina') !== false;
    }

    /**
     * Get article shortname
     *
     * @param Newscoop\Entity\Article $article
     * @return string
     */
    protected function getShortname($article)
    {
        try {
            $shortname = $article->getData('short_name');
            return !empty($shortname) ? $shortname : $article->getTitle();
        } catch (\Exception $e) {
            return $article->getTitle();
        }
    }
}
