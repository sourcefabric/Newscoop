<?php
/**
 */

/**
 */
class Admin_View_Helper_PrintFormErrors extends Zend_View_Helper_Abstract
{
    /**
     * Print form errors
     *
     * @param Zend_Form $form
     * @return void
     */
    public function printFormErrors(Zend_Form $form, $classes = '')
    {
        $errors = $form->getErrorMessages();
        if (empty($errors)) {
            return;
        }

        echo sprintf('<ul class="form-errors %s">',
            $this->view->escape($classes));

        foreach ($errors as $error) {
            echo sprintf("<li>%s</li>\n", $this->view->escape($error));
        }

        echo "</ul>\n";
    }
}
