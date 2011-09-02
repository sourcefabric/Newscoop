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
                $this->_helper->flashMessenger(array('error', "User with given username exists."));
                $this->_helper->redirector('index', 'index', 'default');
            }

            $this->_helper->service('email')->sendConfirmationToken($user);
            $this->_helper->redirector('after');
        }

        $this->view->form = $form;
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

        $token = $this->_getParam('token', false);
        if (!$token) {
            $this->_helper->flashMessenger(array('error', "No token provided"));
            $this->_helper->redirector('index', 'index', 'default');
        }

        if (!$this->tokenService->checkToken($user, $token, 'email.confirm')) {
            $this->_helper->flashMessenger(array('error', "Invalid token"));
            $this->_helper->redirector('index', 'index', 'default');
        }

        // @todo set user active
        $this->service->setActive($user);
    }

    public function initAction()
    {
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

    public function generateUsername()
    {
        return $this->service->generateUsername($this->_getParam('first_name'), $this->_getParam('last_name'));
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
