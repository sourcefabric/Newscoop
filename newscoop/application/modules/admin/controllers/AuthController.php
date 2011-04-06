<?php

class Admin_AuthController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->setLayout('admin_login');
    }

    public function loginAction()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) { // logged in
            $this->_helper->redirector('index', 'index');
        }

        $form = $this->getLoginForm();

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $values = $form->getValues();
            $repository = $this->_helper->entity->getRepository('Newscoop\Entity\User\Staff');
            $adapter = new Newscoop\Auth\Adapter($repository, $values['username'], $values['password']);
            $result = $auth->authenticate($adapter);

            switch ($result->getCode()) {
                case Zend_Auth_Result::SUCCESS:
                    if (isset($_POST['_next']) && $_POST['_next'] == 'post') { // reuse params 
                        $request = $this->getRequest();
                        $this->getRequest()
                            ->setControllerName($request->getParam('controller'))
                            ->setActionName($request->getParam('action'))
                            ->setDispatched(false);
                        $_POST[SecurityToken::SECURITY_TOKEN] = SecurityToken::GetToken();
                        return;
                    }

                    // restore request
                    $this->_helper->redirector
                        ->setPrependBase(false)
                        ->gotoUrl($_SERVER['REQUEST_URI']);
                    break;

                default:
                    $this->view->error = getGS('Invalid credentials. Try again.');
                    break;
            }
        }

        $this->view->form = $form;
    }

    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            Article::UnlockByUser((int) $auth->getIdentity());
            $auth->clearIdentity();
        }

        $this->_helper->FlashMessenger(getGS('You were logged out.'));
        $this->_helper->redirector('index', 'index');
    }

    /**
     * Get login form
     *
     * @return Zend_Form
     */
    private function getLoginForm()
    {
        $form = new Zend_Form;

        $form->setMethod('post');

        $form->addElement('text', 'username', array(
            'required' => true,
            'label' => getGS('Username'),
        ));

        $form->addElement('password', 'password', array(
            'required' => true,
            'label' => getGS('Password'),
        ));

        $form->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => getGS('Login'),
        ));

        foreach ($_POST as $key => $val) {
            if ($form->getElement($key)) {
                continue;
            }

            $form->addElement('hidden', $key, array(
                'value' => $val,
            ));
        }

        return $form;
    }

}

