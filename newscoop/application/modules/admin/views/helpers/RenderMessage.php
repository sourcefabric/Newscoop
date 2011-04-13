<?php

/**
 * Render message view helper
 */
class Admin_View_Helper_RenderMessage extends Zend_View_Helper_Abstract
{
    /**
     * Render message
     *
     * @param string $message
     * @return void
     */
    public function renderMessage($message)
    {
        if (empty($message)) {
            return;
        }

        $this->view->message = (string) $message;
        echo $this->view->render('message.phtml');
    }
}
