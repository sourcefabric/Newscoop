<?php

/**
 * Render error view helper
 */
class Admin_View_Helper_RenderError extends Zend_View_Helper_Abstract
{
    /**
     * Render error message
     *
     * @return void
     */
    public function renderError($error = NULL)
    {
        if ($error) {
            $this->view->error = $error;
        }

        echo $this->view->render('error.phtml');
    }
}
