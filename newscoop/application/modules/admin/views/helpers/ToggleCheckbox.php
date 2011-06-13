<?php
/**
 * Render actions view helper
 */
class Admin_View_Helper_ToggleCheckbox extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @param array $actions
     * @return void
     */
    public function toggleCheckbox()
    {
        return $this->view->render('toggle-checkbox.phtml');
    }
}
