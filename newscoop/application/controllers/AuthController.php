<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

include_once APPLICATION_PATH . '/../hybridauth/hybridauth.php';

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
            $adapter->setUsername($values['username'])->setPassword($values['password']);
            $result = $this->auth->authenticate($adapter);

            if ($result->getCode() == Zend_Auth_Result::SUCCESS) {
                $this->_helper->redirector('index', 'dashboard');
            }

            $this->view->error = $this->view->translate("Invalid credentials");
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

        $this->_helper->redirector('index', 'index');
    }

    public function socialAction()
    {
        $hauth = new Hybrid_Auth();
        if ($hauth->hasError()) {
            var_dump($hauth->getErrorMessage());
            exit;
        }

        if (!$hauth->hasSession()) {
            $params = array(
                'hauth_return_to' => $this->getRequest()->getHttpHost() . $this->getRequest()->getRequestUri(),
            );

            $adapter = $hauth->setup($this->_getParam('provider'), $params);
            $adapter->login();
        } else {
            $adapter = $hauth->wakeup();
            $userData = $adapter->user();

            $adapter = $this->_helper->service('auth.adapter.social');
            $adapter->setProvider($userData->providerId)->setProviderUserId($userData->providerUID);
            $result = $this->auth->authenticate($adapter);

            if ($result->getCode() == Zend_Auth_Result::SUCCESS) {
                $this->_helper->redirector('index', 'dashboard');
            }

            $this->_forward('social', 'register', 'default', array(
                'userData' => $userData,
            ));
        }
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
                $this->_helper->redirector('index', 'index');
            }
        }

        $this->view->form = $form;
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
            $this->_helper->flashMessenger($this->view->translate("Password changed"));
            $this->_helper->redirector('index', 'auth');
        }

        $this->view->form = $form;
    }
}
