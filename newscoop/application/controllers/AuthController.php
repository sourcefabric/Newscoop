<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class AuthController extends Zend_Controller_Action
{
    /** @var Zend_Auth */
    private $auth;

    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->auth = Zend_Auth::getInstance();
    }

    public function indexAction()
    {
        if ($this->auth->hasIdentity()) {
            $this->_helper->redirector('index', 'index');
        }

        $form = new Application_Form_Login();

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();
            $adapter = $this->_helper->service('auth.adapter');
            $adapter->setEmail($values['email'])->setPassword($values['password']);
            $result = $this->auth->authenticate($adapter);

            if ($result->getCode() == Zend_Auth_Result::SUCCESS) {
                $this->_helper->redirector('index', 'dashboard');
            } else {
                $form->addError($this->view->translate("Invalid credentials"));
            }
        }

        $this->view->form = $form;
    }

    public function logoutAction()
    {
        if ($this->auth->hasIdentity()) {
            $this->auth->clearIdentity();
        }

        $url = $this->_request->getParam('url');
        if (!is_null($url)) {
            $this->_redirect($url);
        }

        $this->_helper->redirector->gotoUrl('?t=' . time());
    }

    public function socialAction()
    {
        $config = array(
		    'base_url' => $this->view->serverUrl($this->view->url(array('action' => 'socialendpoint'))), 
		    'debug_mode' => false,
		    'providers' => array(
			    'Facebook' => array(
				    'enabled' => true,
                    'keys'    => array(
                        'id' => SystemPref::Get('facebook_appid'),
                        'secret' => SystemPref::Get('facebook_appsecret'),
                    ), 
                ),
            ),
        );

        try {
            $hauth = new Hybrid_Auth($config);
            $adapter = $hauth->authenticate($this->_getParam('provider'));
            $userData = $adapter->getUserProfile();

            $socialAdapter = $this->_helper->service('auth.adapter.social');
            $socialAdapter->setProvider($adapter->id)->setProviderUserId($userData->identifier);
            $result = $this->auth->authenticate($socialAdapter);

            if ($result->getCode() !== Zend_Auth_Result::SUCCESS) {
                $user = $this->_helper->service('user')->findBy(array('email' => $userData->email));
                if (empty($user))  {
                    $user = $this->_helper->service('user')->createPending($userData->email, $userData->firstName, $userData->lastName);
                } else {
                    $user = array_pop($user);
                }

                $this->_helper->service('auth.adapter.social')->addIdentity($user, $adapter->id, $userData->identifier);
                $this->auth->authenticate($socialAdapter);
            } else {
                $user = $this->_helper->service('user')->getCurrentUser();
            }

            if ($user->isPending()) {
                $this->_forward('confirm', 'register', 'default');
            } else {
                $this->_helper->redirector('index', 'dashboard');
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage(), $e->getTraceAsString());
            exit;
        }
    }

    public function socialendpointAction()
    {
        Hybrid_Endpoint::process();
        exit;
    }

    public function passwordRestoreAction()
    {
        $form = new Application_Form_PasswordRestore();

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $user = $this->_helper->service('user')->findOneBy(array(
                'email' => $form->email->getValue(),
            ));

            if (!empty($user) && $user->isActive()) {
                $this->_helper->service('email')->sendPasswordRestoreToken($user);
                $this->_helper->flashMessenger($this->view->translate("E-mail with instructions was sent to given email address."));
                $this->_helper->redirector('password-restore-after', 'auth');
            } else if (empty($user)) {
                $form->email->addError($this->view->translate("Given email not found."));
            }
        }

        $this->view->form = $form;
    }

    public function passwordRestoreAfterAction()
    {
    }

    public function passwordRestoreFinishAction()
    {
        $user = $this->_helper->service('user')->find($this->_getParam('user'));
        if (empty($user)) {
            $this->_helper->flashMessenger(array('error', $this->view->translate('User not found.')));
            $this->_helper->redirector('index', 'index', 'default');
        }

        if (!$user->isActive()) {
            $this->_helper->flashMessenger(array('error', $this->view->translate('User is not active user.')));
            $this->_helper->redirector('index', 'index', 'default');
        }

        $token = $this->_getParam('token', false);
        if (!$token) {
            $this->_helper->flashMessenger(array('error', $this->view->translate('No token provided.')));
            $this->_helper->redirector('index', 'index', 'default');
        }

        if (!$this->_helper->service('user.token')->checkToken($user, $token, 'password.restore')) {
            $this->_helper->flashMessenger(array('error', $this->view->translate('Invalid token.')));
            $this->_helper->redirector('index', 'index', 'default');
        }

        $form = new Application_Form_PasswordRestorePassword();
        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $this->_helper->service('user')->save($form->getValues(), $user);
            $this->_helper->service('user.token')->invalidateTokens($user, 'password.restore');
            if (!$this->auth->hasIdentity()) { // log in
                $adapter = $this->_helper->service('auth.adapter');
                $adapter->setEmail($user->getEmail())->setPassword($form->password->getValue());
                $this->auth->authenticate($adapter);
                $this->_helper->redirector('index', 'dashboard');
            } else {
                $this->_helper->flashMessenger($this->view->translate("Password changed"));
                $this->_helper->redirector('index', 'auth');
            }
        }

        $this->view->form = $form;
    }
}
