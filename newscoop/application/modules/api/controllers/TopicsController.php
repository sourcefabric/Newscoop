<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Doctrine\Common\Collections\ArrayCollection;

require_once __DIR__ . '/AbstractController.php';

/**
 */
class Api_TopicsController extends AbstractController
{
    const LANGUAGE = 5;

    const ARTICLE_TOPICS = 412233;
    
    /** @var Zend_Controller_Request_Http */
    private $request;
    
    private $url;

    /** @var int */
    private $rank = 1;

    /**
     * Init controller.
     */
    public function init()
    {
        global $Campsite;
        
        $this->_helper->layout->disableLayout();
        $this->request = $this->getRequest();
        $this->url = $Campsite['WEBSITE_URL'];
    }

    /**
     * Default action controller.
     */
    public function indexAction()
    {
        $this->_forward('list');
    }

    /**
     * Return list of topics.
     *
     * @return json
     */
    public function listAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('list', 'json')->initContext();
        
        $response = array();
        
        $parameters = $this->request->getParams();
        
        if ($this->hasAuthInfo()) {
            $user = $this->getUser();
            $topicsTemp = $this->_helper->service('user.topic')->getTopics($user);
            $topics = array();
            foreach ($topicsTemp as $item) {
                $topics[] = new Topic($item->getTopicId());
            }
        } else {
            $topics = ArticleTopic::GetArticleTopics(self::ARTICLE_TOPICS);
        }
        
        foreach ($topics as $topic) {
            $response[] = array(
                'id' => $topic->getTopicId(),
                'name' => $topic->getName(self::LANGUAGE),
                'parent_id' => $topic->getParentId()
            );
        }
        
        $this->_helper->json($response);
    }
    
    /**
     * Subscribe to a topic.
     *
     * @return void
     */
    public function subscribeAction()
    {
        $this->assertIsSecure();
        $this->assertIsPost();

        $this->_helper->service('user.topic')->followTopic($this->getUser(), $this->getTopic());
        $this->_helper->json(array(
            'status' => 200,
        ));
    }

    /**
     * Unsubscribe from a topic.
     *
     * @return void
     */
    public function unsubscribeAction()
    {
        $this->assertIsSecure();
        $this->assertIsPost();

        $this->_helper->service('user.topic')->unfollowTopic($this->getUser(), $this->getTopic());
        $this->_helper->json(array(
            'status' => 200,
        ));
    }

    /**
     * Get user topics
     *
     * @return void
     */
    public function mytopicsAction()
    {
        $user = $this->getUser();
        $articles = new ArrayCollection();
        foreach ($this->_helper->service('user.topic')->getTopics($user) as $topic) {
            foreach ($this->_helper->service('article')->findByTopic($topic, 3) as $article) {
                $articles->add(array_merge($this->formatArticle($article), array(
                    'topic_id' => $topic->getTopicId(),
                    'topic_name' => $topic->getName(),
                    'topic_url' => $this->getTopicUrl($topic),
                )));
            }
        }

        $this->_helper->json($articles->toArray());
    }

    /**
     * Get topic
     *
     * @return Newscoop\Entity\UserTopic
     */
    private function getTopic()
    {
        $topic = $this->_helper->service('user.topic')->findTopic($this->_getParam('topic_id'));
        if (!$topic) {
            $this->sendError('Topic not found.', 404);
        }

        return $topic;
    }
}
