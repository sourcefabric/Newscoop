<?php

class Admin_AuthController extends Zend_Controller_Action
{
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
}
