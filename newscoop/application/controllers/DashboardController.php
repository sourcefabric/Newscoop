<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;

/**
 */
class DashboardController extends Zend_Controller_Action
{
    /** @var Newscoop\Services\UserService */
    private $service;

    /** @var Newscoop\Entity\User */
    private $user;

    public function init()
    {
        $GLOBALS['controller'] = $this;
        $this->_helper->layout->disableLayout();

        $this->service = $this->_helper->service('user');
        $this->user = $this->service->getCurrentUser();

        $this->_helper->contextSwitch()
            ->addActionContext('update-topics', 'json')
            ->initContext();
    }

    public function preDispatch()
    {
        if (empty($this->user)) {
            $this->_helper->redirector('index', 'auth');
        }
    }

    public function indexAction()
    {
        $form = new Application_Form_Profile();
        $form->setMethod('POST');

        $form->setDefaultsFromEntity($this->user);

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();

            try {
                if (!empty($values['image'])) {
                    $imageInfo = array_pop($form->image->getFileInfo());
                    $values['image'] = $this->_helper->service('image')->save($imageInfo);
                }
                $this->service->save($values, $this->user);
                $this->_helper->redirector('index');
            } catch (\InvalidArgumentException $e) {
                switch ($e->getMessage()) {
                    case 'username_conflict':
                        $form->username->addError($this->view->translate("User with given username exists."));
                        break;

                    default:
                        $form->image->addError($e->getMessage());
                        break;
                }
            }
        }
        
        $userSubscriptionService = $this->_helper->service('user_subscription');
        
        $this->view->subscriber = $this->user->getSubscriber();
        if (!$this->view->subscriber) {
            $this->view->subscriber = $userSubscriptionService->fetchSubscriber($this->user);
        }
        
        //$this->view->subscriber = false;
        
        if ($this->view->subscriber) {
            $userSubscriptionKey = $userSubscriptionService->createKey($this->user);
            $userSubscriptionService->setKey($this->user, $userSubscriptionKey);
            $this->view->userSubscriptions = $userSubscriptionService->fetchSubscriptions($this->user);
            $this->view->userSubscriptionKey = $userSubscriptionKey;
        }
        else {
            $this->view->user_first_name = $this->user->getFirstName();
            $this->view->user_last_name = $this->user->getLastName();
            $this->view->user_email = $this->user->getEmail();
        }
                
        $this->view->form = $form;
        $this->view->user = new MetaUser($this->user);
        $this->view->first_time = $this->_getParam('first', false);
    }

    public function updateTopicsAction()
    {
        try {
            $this->_helper->service('user.topic')->updateTopics($this->user, $this->_getParam('topics', array()));
            $this->view->status = '0';
        } catch (Exception $e) {
            $this->view->status = -1;
            $this->view->message = $e->getMessage();
        }
    }

    public function followTopicAction()
    {
        $service = $this->_helper->service('user.topic');
        $topic = $service->findTopic($this->_getParam('topic'));
        if (!$topic) {
            $this->_helper->flashMessenger(array('error', "No topic to follow"));
            $this->_helper->redirector('index', 'index', 'default');
        }

        $service = $this->_helper->service('user.topic');
        $service->followTopic($this->user, $topic);

        $this->_helper->flashMessenger("Topic added to followed");
        $this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
    }
}
