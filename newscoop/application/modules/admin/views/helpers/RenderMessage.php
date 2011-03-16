<?php

class Admin_View_Helper_RenderMessage extends Zend_View_Helper_Abstract
{
    public function renderMessage($message)
    {
        $this->view->message = (string) $message;
        echo $this->view->render('message.phtml');
    }
}
