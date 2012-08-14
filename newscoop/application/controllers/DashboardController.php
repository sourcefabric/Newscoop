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
    const ATTRIBUTE_QUESTIONNAIRE = '_finished_questionnaire';

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

        if ($this->getRequest()->getActionName() === 'index'
            && $this->userIsPolitician()
            && !$this->user->getAttribute(self::ATTRIBUTE_QUESTIONNAIRE)) {
            $this->_helper->redirector('politprofil');
        }
    }

    public function indexAction()
    {
        $form = new Application_Form_Profile();
        $form->setMethod('POST');
        $this->addProfileFields($form);
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
        
        $this->view->form = $form;
        $this->view->user = new MetaUser($this->user);
        $this->view->first_time = $this->_getParam('first', false);
        
        $this->view->user_first_name = $this->user->getFirstName();
        $this->view->user_last_name = $this->user->getLastName();
        $this->view->user_email = $this->user->getEmail();
        
        $userSubscriptionService = $this->_helper->service('user_subscription');
        
        $this->view->subscriptionService = true;
        
        try {
            $userSubscriptionService->testConnection();
        } catch(\Exception $e) {
            $this->view->subscriptionService = false;
            restore_error_handler();
        }

        $this->view->userSubscriptions = array();
        $this->view->userSubscriptionKey = '';
        if ($this->view->subscriptionService == true) {
            $this->view->subscriber = $this->user->getSubscriber();
            if (!$this->view->subscriber) {
                $this->view->subscriber = $userSubscriptionService->fetchSubscriber($this->user);
            }
            
            if ($this->view->subscriber) {
                $userSubscriptionKey = $userSubscriptionService->createKey($this->user);
                $userSubscriptionService->setKey($this->user, $userSubscriptionKey);
                $this->view->userSubscriptions = $userSubscriptionService->fetchSubscriptions($this->user);
                $this->view->userSubscriptionKey = $userSubscriptionKey;
            }
        }

        $this->view->mailChimpForm = $this->getMailChimpForm();
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

    public function politprofilAction()
    {
        if (!$this->userIsPolitician()) {
            $this->_helper->service('user')->addUserGroup($this->user, $this->_helper->config('role')->politician);
        }

        $form = new Zend_Form();
        $this->addProfileFields($form);
        $form->setDefaults(array(
            'attributes' => $this->user->getAttributes(),
        ));

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $this->_helper->service('user')->save($values, $this->user);
            $this->_helper->redirector('questionnaire');
        }

        $this->view->form = $form;
        $this->view->user = new MetaUser($this->user);
    }

    public function questionnaireAction()
    {
        $form = new Zend_Form($this->getThemeFormConfig('questionnaire')->form);
        $form->setDefaults($this->user->getAttributes());


        if (!$this->user->getAttribute(self::ATTRIBUTE_QUESTIONNAIRE)) {
            $this->_helper->service('user')->save(array(
                'attributes' => array(
                    self::ATTRIBUTE_QUESTIONNAIRE => 1,
                ),
            ), $this->user);
        }

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $this->_helper->service('user')->save(array(
                'attributes' => $values,
            ), $this->user);

            $this->_helper->flashMessenger($form->getAttrib('message'));
            $this->_helper->redirector('questionnaire');
        }

        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $this->view->form = $form;
    }

    public function saveNewsletterAction()
    {
        $form = $this->getMailChimpForm();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $this->_helper->service('mailchimp')->subscribe($this->user, $this->getMailChimpListId(), $form->getValues());
        }

        $this->_helper->redirector('index');
    }

    /**
     * Get mailchimp list id
     *
     * @return string
     */
    private function getMailChimpListId()
    {
        $mailchimpOptions = $this->getInvokeArg('bootstrap')->getOption('mailchimp');
        return $mailchimpOptions['list_id'];
    }

    /**
     * Add profile fields to form
     *
     * @param Zend_Form $form
     * @return void
     */
    private function addProfileFields(Zend_Form $form)
    {
        $section = $this->userIsPolitician() ? 'politician' : 'user';
        $form->addSubForm(
            new Zend_Form_SubForm($this->getThemeFormConfig('profile', $section)->profile),
            'attributes');
    }

    /**
     * Get theme form config
     *
     * @param string $form
     * @param string $section
     * @return Zend_Config
     */
    private function getThemeFormConfig($form, $section = null)
    {
        return new Zend_Config_Xml(
            $this->_helper->theme->getFilePath('_forms/' . $form . '.xml'),
            $section);
    }

    /**
     * Test if user is politician
     *
     * @return bool
     */
    private function userIsPolitician()
    {
        return $this->user->hasGroup($this->_helper->config('role')->politician);
    }

    /**
     * Get mailchimp form
     *
     * @return Zend_Form
     */
    private function getMailChimpForm()
    {
        if (!$this->getMailChimpListId()) {
            return null;
        }

        $form = new Zend_Form();
        $listGroups = $this->_helper->service('mailchimp')->getListGroups($this->getMailChimpListId());
        foreach ($listGroups as $group) {
            $form->addElement('multiCheckbox', (string) $group['id'], array(
                'label' => (string) $group['name'],
                'multioptions' => $this->getMailChimpGroupOptions($group),
            ));
        }

        $form->setDefaults($this->_helper->service('mailchimp')
            ->getUserGroups($this->user, $this->getMailChimpListId())
        );

        return $form;
    }

    /**
     * Get mailchimp group options
     *
     * @param array $group
     * @return array
     */
    private function getMailChimpGroupOptions($group)
    {
        $groups = array_map(function($group) {
            return $group['name'];
        }, $group['groups']);

        return array_combine($groups, $groups);
    }
}
