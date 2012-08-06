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
    /** @var Newscoop\Services\UserService */
    private $service;

    /** @var Zend_Session_Namespace */
    private $session;

    /** @var Newscoop\Services\UserTokenService */
    private $tokenService;

    public function init()
    {
        $this->_helper->contextSwitch
            ->addActionContext('generate-username', 'json')
            ->addActionContext('check-username', 'json')
            ->addActionContext('check-email', 'json')
            ->addActionContext('pending', 'json')
            ->addActionContext('create-user', 'json')
            ->initContext();
    }

    public function indexAction()
    {
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
                $user = $this->_helper->service('user')->createPending($values['email']);
            }

            if (!$user->isPending()) {
                $form->email->addError("User with email '$values[email]' is registered already.");
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

    public function afterAction()
    {
    }

    public function confirmAction()
    {
        $user = $this->_helper->service('user')->find($this->_getParam('user'));
        if (empty($user)) {
            $this->_helper->flashMessenger(array('error', "User not found"));
            $this->_helper->redirector('index', 'index', 'default');
        }

        if (!$user->isPending()) {
            $this->_helper->flashMessenger(array('error', "User has been activated"));
            $this->_helper->redirector('index', 'index', 'default');
        }

        $token = $this->_getParam('token', false);
        if (!$token) {
            $this->_helper->flashMessenger(array('error', "No token provided"));
            $this->_helper->redirector('index', 'index', 'default');
        }

        if (!$this->_helper->service('user.token')->checkToken($user, $token, 'email.confirm')) {
            $this->_helper->flashMessenger(array('error', "Invalid token"));
            $this->_helper->redirector('index', 'index', 'default');
        }

        $form = new Application_Form_Confirm();
        $form->setMethod('POST');
        
        $values = array();
        if ($user->getFirstName()) {
            $values['first_name'] = $user->getFirstName();
        }
        if ($user->getLastName()) {
            $values['last_name'] = $user->getLastName();
        }
        $form->populate($values);
        
        $this->view->terms = false;
        if ($user->getFirstName() || $user->getLastName()) {
            $form->addElement('checkbox', 'terms_of_use', array(
                'label' => 'Accepting terms of use',
                'required' => true,
                'validators' => array(
                    array('greaterThan', true, array('min' => 0)),
                ),
                 'errorMessages' => array("Sie können sich nur registrieren, wenn Sie unseren Nutzungsbedingungen zustimmen. Dies geschieht zu Ihrer und unserer Sicherheit. Bitten setzen Sie im entsprechenden Feld ein Häkchen."),
            ));
            
            $this->view->terms = true;
        }

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            try {
                $values = $form->getValues();
                $this->_helper->service('user')->savePending($values, $user);
                $this->_helper->service('user.token')->invalidateTokens($user, 'email.confirm');

                $this->_helper->service('dispatcher')
                    ->notify('user.register', new GenericEvent($this, array(
                        'user' => $user,
                    )));

                $auth = \Zend_Auth::getInstance();
                if ($auth->hasIdentity()) { // show index
                    $this->_helper->flashMessenger('User registered successfully.');
                    $this->_helper->redirector('index', 'index', 'default');
                } else {
                    $adapter = $this->_helper->service('auth.adapter');
                    $adapter->setEmail($user->getEmail())->setPassword($values['password']);
                    $result = $auth->authenticate($adapter);
                    $this->_helper->redirector('index', 'dashboard', 'default', array('first' => 1));
                }
            } catch (\Exception $e) {
                switch ($e->getMessage()) {
                    case 'username_conflict':
                        $form->username->addError('Username is used. Please use another one.');
                        break;

                    default:
                        var_dump($e);
                        exit;
                }
            }
        }

        $this->view->form = $form;
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

    public function socialAction()
    {
        $form = new Application_Form_Social();
        $form->setMethod('POST');

        $userData = $this->_getParam('userData');
        $form->setDefaults(array(
            'first_name' => $userData->profile->firstName,
            'last_name' => $userData->profile->lastName,
            'email' => $userData->profile->email,
        ));

        if (!empty($userData->profile->email)) { // try to find user by email
            $user = $this->_helper->service('user')->findBy(array('email' => $userData->profile->email));
            if (!empty($user)) { // we have user for given email, add him login
                $user = array_pop($user);
                $this->_helper->service('auth.adapter.social')->addIdentity($user, $userData->providerId, $userData->providerUID);
                $adapter = $this->_helper->service('auth.adapter.social');
                $adapter->setProvider($userData->providerId)->setProviderUserId($userData->providerUID);
                Zend_Auth::getInstance()->authenticate($adapter);
                $this->_helper->redirector('index', 'dashboard');
            }
        }

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $user = $this->_helper->service('user')->save($form->getValues() + array('is_public' => 1));
            $this->_helper->service('user')->setActive($user);
            $this->_helper->service('auth.adapter.social')->addIdentity($user, $userData->providerId, $userData->providerUID);
            $adapter = $this->_helper->service('auth.adapter.social');
            $adapter->setProvider($userData->providerId)->setProviderUserId($userData->providerUID);
            Zend_Auth::getInstance()->authenticate($adapter);
            $this->_helper->redirector('index', 'dashboard');
        }

        $this->view->name = $userData->profile->displayName;
        $this->view->form = $form;
    }
    
    public function pendingAction()
    {
        if ($this->_getParam('email')) {
            $user = $this->_helper->service('user')->findBy(array('email' => $this->_getParam('email')));
            
            if ($user) {
                $this->view->result = '0';
            }
            else {
                $user = $this->_helper->service('user')->createPending($this->_getParam('email'));
                $this->_helper->service('email')->sendConfirmationToken($user);
                $this->view->result = '1';
            }
        }
        $this->view->result = '0';
    }
}
