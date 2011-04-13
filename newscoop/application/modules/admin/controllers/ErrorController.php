<?php

class Admin_ErrorController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function denyAction()
    {
        $this->view->message = $this->getRequest()->getParam('message', '');
    }
}

