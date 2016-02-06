<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;
use Newscoop\EventDispatcher\Events\GenericEvent;

/**
 */
class RegisterController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->contextSwitch
            ->addActionContext('generate-username', 'json')
            ->addActionContext('check-username', 'json')
            ->addActionContext('check-email', 'json')
            ->addActionContext('pending', 'json')
            ->addActionContext('create-user', 'json')
            ->initContext();

        $this->auth = Zend_Auth::getInstance();
    }

    public function indexAction()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');
        $form = new Application_Form_Register();
        $form->setMethod('POST');

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();
            $users = $this->_helper->service('user')->findBy(array(
                'email' => $values['email'],
            ));

            if (count($users) > 0) {
                $user = array_pop($users);
            } else {
                $publicationService = \Zend_Registry::get('container')->getService('newscoop_newscoop.publication_service');
                $user = $this->_helper->service('user')->createPending($values['email'], null, null, null, $publicationService->getPublication()->getId());
            }

            if (!$user->isPending()) {
                $form->email->addError(sprintf($translator->trans('User with email %s is registered already.', array(), 'users'), $values['email']));
            } else {
                $this->_helper->service('email')->sendConfirmationToken($user);
                $this->_helper->redirector('after');
            }
        }

        $this->view->form = $form;
    }

    public function createUserAction()
    {
        $parameters = $this->getRequest()->getParams();

        $user = $this->_helper->service('user')->findBy(array(
            'email' => $parameters['email'],
        ));

        if ($user) {
            echo '0';
            exit;
        } else {
            $user = $this->_helper->service('user')->createPending($parameters['email'], $parameters['first_name'], $parameters['last_name'], $parameters['subscriber_id']);
            $this->_helper->service('email')->sendConfirmationToken($user);
            echo '1';
            exit;
        }
    }

    /**
     * Empty action for view shown after registration
     */
    public function afterAction()
    {}

    /**
     * Account confirmation action
     */
    public function confirmAction()
    {
        $token = $this->_getParam('token', false);
        $user = $this->getAuthUser($token);
        $form = $this->_helper->form('confirm');
        $form->setMethod('POST');
        $form->setDefaults(array(
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'username' => $this->_helper->service('user')->generateUsername($user->getFirstName(), $user->getLastName())
        ));

        $request = $this->getRequest();

        if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();
            try {
                if (!empty($values['image'])) {
                    $imageInfo = array_pop($form->image->getFileInfo());
                    $values['image'] = $this->_helper->service('image')->save($imageInfo);
                }

                $this->_helper->service('user')->savePending($values, $user);
                $this->_helper->service('dispatcher')->dispatch('user.register', new GenericEvent($this, array(
                    'user' => $user,
                )));
                $this->_helper->service('user.token')->invalidateTokens($user, 'email.confirm');

                // Login user after confirming token
                // Zend
                $adapter = $this->_helper->service('auth.adapter');
                $adapter->setEmail($user->getEmail())->setPassword($values['password']);
                $this->auth->authenticate($adapter);
                // Frontend
                $token = $this->_helper->service('user')->loginUser($user, 'frontend_area');
                $session = \Zend_Registry::get('container')->getService('session');
                $session->set('_security_frontend_area', serialize($token));
                // Oauth
                $OAuthtoken = $this->_helper->service('user')->loginUser($user, 'oauth_authorize');
                $session->set('_security_oauth_authorize', serialize($OAuthtoken));

                // Redirect to target path (if provided)
                if (isset($values['_target_path']) && !empty($values['_target_path'])) {
                    $this->_helper->redirector->gotoUrl($values['_target_path']);
                }

                // redirect to dashboard
                $this->_helper->redirector('index', 'dashboard', 'default', array('first' => 1));
            } catch (InvalidArgumentException $e) {
                $translator = \Zend_Registry::get('container')->getService('translator');
                $form->username->addError($translator->trans('Username is used. Please use another one.', array(), 'users'));
            }
        }

        $this->view->form = $form;
        $this->view->user = new \MetaUser($user);
        $this->view->social = $this->_getParam('social') ?: false;
    }

    public function generateUsernameAction()
    {
        $this->view->username = $this->_helper->service('user')
            ->generateUsername($this->_getParam('first_name'), $this->_getParam('last_name'));
    }

    /**
     * Test if username is available
     */
    public function checkUsernameAction()
    {
        $this->view->status = $this->_helper->service('user')
            ->checkUsername($this->_getParam('username'));
    }

    /**
     * Test if email is available
     */
    public function checkEmailAction()
    {
        $users = $this->_helper->service('user')->findBy(array(
            'email' => $this->_getParam('email'),
        ));

        if (sizeof($users) > 0) {
            $user = array_pop($users);
            if (!$user->isPending()) {
                $this->view->status = false;

                return;
            }
        }

        $this->view->status = true;
    }

    public function pendingAction()
    {
        if ($this->_getParam('email')) {
            $user = $this->_helper->service('user')->findBy(array('email' => $this->_getParam('email')));

            if (!$user) {
                $user = $this->_helper->service('user')->createPending($this->_getParam('email'));
                $this->_helper->service('email')->sendConfirmationToken($user);
                $this->view->result = '1';

                return;
            }
        }

        $this->view->result = '0';
    }

    /**
     * Get user by token
     *
     * @return Newscoop\Entity\User
     */
    private function getAuthUser($token)
    {
        $user = $this->_helper->service('user')->find($this->_getParam('user'));

        if (empty($user)) {
            $this->_helper->flashMessenger(array('error', "User not found"));
            $this->_helper->redirector(null, null, 'default');
        }

        if (!$user->isPending()) {
            $this->_helper->flashMessenger(array('error', "User has been activated"));
            $this->_helper->redirector(null, null, 'default');
        }

        // Validate token
        if (!$token) {
            $this->_helper->flashMessenger(array('error', "No token provided"));
            $this->_helper->redirector(null, null, 'default');
        }

        if (!$this->_helper->service('user.token')->checkToken($user, $token, 'email.confirm')) {
            $this->_helper->flashMessenger(array('error', "Invalid token"));
            $this->_helper->redirector(null, null, 'default');
        }

        return $user;
    }
}
