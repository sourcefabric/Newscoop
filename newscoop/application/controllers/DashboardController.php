<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;
use Newscoop\Topic\SaveUserTopicsCommand;

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

        if ($this->user->isPending()) {
            $this->_helper->redirector('confirm', 'register');
        }
    }

    public function indexAction()
    {
        $form = $this->_helper->form('profile');
        $form->setMethod('POST');
        $form->setDefaults((array) $this->user->getView());

        $listView = $this->_helper->service('mailchimp.list')->getListView();
        $memberView = $this->_helper->service('mailchimp.list')->getMemberView($this->user->getEmail());
        $this->_helper->newsletter->initForm($form, $listView, $memberView);

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();

            try {
                if (!empty($values['image'])) {
                    $imageInfo = array_pop($form->image->getFileInfo());
                    $values['image'] = $this->_helper->service('image')->save($imageInfo);
                }
                $this->service->save($values, $this->user);
                $this->_helper->service('mailchimp.list')->subscribe($this->user->getEmail(), $values['newsletter']);
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

        $this->view->user = new MetaUser($this->user);
        $this->view->form = $form;
        $this->view->newsletter = $listView;
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

    public function saveTopicsAction()
    {
        $form = new Application_Form_Topics();

        $topics = $this->_helper->service('topic')->getMultiOptions();
        $form->topics->setMultiOptions($topics);
        $form->selected->setMultiOptions($topics);

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $command = new SaveUserTopicsCommand($form->getValues());
            $command->userId = $this->user->getId();
            $this->_helper->service('user.topic')->saveUserTopics($command);
            $this->_helper->json($command->selected);
        }

        $this->getResponse()->setHttpResponseCode(400);
        $this->_helper->json($form->getMessages());
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

    public function addTopicByNameAction()
    {
        if (!$this->_getParam('topic')) {
            $this->_helper->json(array());
            return;
        }

        $topic = $this->_helper->service('em')->getRepository('Newscoop\Entity\Topic')->findOneBy(array(
            'name' => $this->_getParam('topic'),
        ));

        if ($topic !== null) {
            $this->_helper->service('user.topic')->followTopic($this->user, $topic);
        }

        $this->_helper->json(array());
    }
}
