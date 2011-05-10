<?php

/**
 * Pluplad view helper
 */
class Admin_View_Helper_Plupload extends Zend_View_Helper_Abstract
{
    /**
     * Render Plupload script
     *
     * @param Zend_Form $form
     * @return void
     */
    public function plupload(Zend_Form $form)
    {
        $formId = $form->getAttrib('id');
        if (empty($formId)) {
            $formId = 'plupload-form';
            $form->setAttrib('id', $formId);
        }

        $this->view->plupload = (object) array(
            'formId' => $formId,
        );

        echo $form;
        echo $this->view->render('plupload.phtml');
    }
}
