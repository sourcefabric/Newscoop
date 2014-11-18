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
        $translator = Zend_Registry::get('container')->getService('translator');
        $form = $this->_helper->form('profile');
        $form->setMethod('POST');
        $form->setDefaults((array) $this->user->getView());
        $form->username->setRequired(false);
        $form->removeElement('username');
        $username = $this->user->getUsername();
        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();
            $values['username'] = $username;
            try {
                if (!empty($values['image'])) {
                    $imageInfo = array_pop($form->image->getFileInfo());
                    $values['image'] = $this->_helper->service('image')->save($imageInfo);
                }
                //TODO add event to subscribe for newsletter
                $this->service->save($values, $this->user);
                $this->_helper->flashMessenger->addMessage($translator->trans('Profile saved.', array(), 'users'));
                $this->_helper->redirector('index');
            } catch (\InvalidArgumentException $e) {
                switch ($e->getMessage()) {
                    case 'username_conflict':
                        $form->username->addError($translator->trans("User with given username exists."));
                        break;

                    default:
                        $form->image->addError($e->getMessage());
                        break;
                }
            }
        }

        $this->view->user = new MetaUser($this->user);
        $this->view->form = $form;
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

    public function updateTopicsAction()
    {
        try {
            $this->_helper->service('user.topic')->updateTopics($this->user, $this->_getParam('topics', array()));
            $this->view->status = 1;
        } catch (Exception $e) {
            $this->view->status = 0;
            $this->view->message = $e->getMessage();
        }
    }
}
