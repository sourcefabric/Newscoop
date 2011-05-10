<?php

/**
 * Pluplad view helper
 */
class Admin_View_Helper_Plupload extends Zend_View_Helper_Abstract
{
    /**
     * Render Plupload script
     *
     * @param string $form
     * @return void
     */
    public function plupload($form = '', array $config = array())
    {
        $this->view->plupload = (object) array_merge(array(
            'form' => $form,
            'url' => $this->view->url(array(
                'plupload' => 1,
            )),
        ), $config);

        echo $this->view->render('plupload.phtml');
    }
}
