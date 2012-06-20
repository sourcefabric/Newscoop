<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Api_TopicsController extends Zend_Controller_Action
{
    const LANGUAGE = 5;

    const ARTICLE_TOPICS = 412233;
    
    /** @var Zend_Controller_Request_Http */
    private $request;
    
    private $url;

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
        
        if (isset($parameters['username']) && isset($parameters['password'])) {
            $user = $this->_helper->service('user')->findOneBy(array('username' => $parameters['username']));
            if ($user->checkPassword($parameters['password'])) {
                $topicsTemp = $this->_helper->service('user.topic')->getTopics($user);
                $topics = array();
                foreach ($topicsTemp as $item) {
                    $topics[] = new Topic($item->getTopicId());
                }
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
     * @return json
     */
    public function subscribeAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('list', 'json')->initContext();
        
        $response = array();
        
        $parameters = $this->request->getParams();
        
        if (isset($parameters['username']) && isset($parameters['password']) && isset($parameters['topic_id'])) {
            $user = $this->_helper->service('user')->findOneBy(array('username' => $parameters['username']));
            if ($user->checkPassword($parameters['password'])) {
                $topic = $this->_helper->service('user.topic')->findTopic($parameters['topic_id']);
                $this->_helper->service('user.topic')->followTopic($user, $topic);
                $response = 1;
            }
            else {
                $response = 0;
            }
        }
        else {
            $response = 0;
        }
        
        $this->_helper->json($response);
    }
}
