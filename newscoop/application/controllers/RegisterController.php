<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;

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
        $GLOBALS['controller'] = $this;
        $this->_helper->layout->disableLayout();

        $this->service = $this->_helper->service('user');
        $this->session = new Zend_Session_Namespace('Form_Register');
        $this->tokenService = $this->_helper->service('user.token');
    }

    public function indexAction()
    {
        $formRegister = new Application_Form_Register();
        $formConfirm = new Application_Form_Confirm();

        $formRegister->setMethod('POST');
        $formConfirm->setMethod('POST');

        $request = $this->getRequest();
        if ($request->isPost()) {

            if (!$request->has('password') && isset($this->session->password)) {
                $request->setPost('password', $this->session->password);
            }

            if ($formRegister->isValid($request->getPost())) { // handle confirm form
                if ($request->has('username') && $formConfirm->isValid($request->getPost())) {
                    $values = $formConfirm->getValues();
                    $values['password'] = empty($values['password_change']) ? $this->session->password : $values['password_change'];
                    $values['is_public'] = true; // public by default
                    try {
                        $user = $this->service->create($values);
                        $this->sendConfirmEmail($user);
                        $this->notifyDispatcher($user);
                        $this->_helper->redirector('index', 'index');
                    } catch (Exception $e) {
                        switch ($e->getMessage()) {
                            case 'username_conflict':
                                $formConfirm->username->addError('Username is used. Please use another one.');
                                break;

                            case 'email_conflict':
                                $formConfirm->email->addError('E-mail is used. Please use another one.');
                                break;

                            default:
                                var_dump($e);
                                exit;
                        }
                    }
                } elseif (!$request->has('username')) { // init confirm form
                    $values = $formRegister->getValues();
                    $this->session->password = $values['password'];
                    $values['username'] = $this->service->generateUsername($values['first_name'], $values['last_name']);
                    $formConfirm->setDefaults($values + array(
                        'terms_of_services' => 'Terms of services text',
                    ));
                }

                $this->view->form = $formConfirm;
                return;
            }
        } else {
            $this->session->password = null;
        }

        $this->view->form = $formRegister;
    }

    public function confirmEmailAction()
    {
        $user = $this->service->find($this->_getParam('user'));
        if (empty($user)) {
            $this->_helper->flashMessenger(array('error', "User not found"));
            $this->_helper->redirector('index', 'index', 'default');
        }

        $token = $this->_getParam('token', false);
        if (!$token) {
            $this->_helper->flashMessenger(array('error', "No token provided"));
            $this->_helper->redirector('index', 'index', 'default');
        }

        if (!$this->tokenService->checkToken($user, $token, 'email.confirm')) {
            $this->_helper->flashMessenger(array('error', "Invalid token"));
            $this->_helper->redirector('index', 'index', 'default');
        }

        $this->service->setActive($user);
        $this->_helper->redirector('index', 'auth', 'default');
    }

    /**
     * Send confirm email
     *
     * @param Newscoop\Entity\User $user
     * @return void
     */
    private function sendConfirmEmail(User $user)
    {
        $email = $this->view->action('confirm', 'email', 'default', array(
            'user' => $user,
        ));

        // @todo send to user email from some valid email
        $mail = new Zend_Mail();
        $mail->setBodyText($email);
        $mail->setFrom('no-reply@localhost');
        $mail->addTo('petr@localhost');
        $mail->setSubject('Confirm e-mail');
        $mail->send();
    }

    /**
     * Notify event dispatcher about new user
     *
     * @param Newscoop\Entity\User $user
     * @return void
     */
    private function notifyDispatcher(User $user)
    {
        $dispatcher = $this->_helper->service('dispatcher');
        $dispatcher->notify(new sfEvent($this, 'user.register', array(
            'user' => $user,
        )));
    }
}
