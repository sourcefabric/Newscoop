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

    public function init()
    {
        $GLOBALS['controller'] = $this;
        $this->_helper->layout->disableLayout();

        $this->service = $this->_helper->service('user');
        $this->session = new Zend_Session_Namespace('Form_Register');
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
                    $user = $this->service->create($values);
                    $dispatcher = $this->_helper->service('dispatcher');
                    $dispatcher->notify(new sfEvent($this, 'user.register', array(
                        'user' => $user,
                    )));
                    $this->_helper->redirector('index', 'index');
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
}
