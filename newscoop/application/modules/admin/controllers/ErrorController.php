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
        $params = $this->getRequest()->getParams();
        $this->view->message = $params[0];
    }
}

