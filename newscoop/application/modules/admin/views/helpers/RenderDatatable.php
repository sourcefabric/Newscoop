<?php

/**
 * Render Datatable view helper
 */
class Admin_View_Helper_RenderDatatable extends Zend_View_Helper_Abstract
{
    /**
     * Render datatable
     *
     * @param array|NULL $cols
     * @return void
     */
    public function renderDatatable(array $cols = NULL)
    {
        if ($cols !== NULL) {
            $this->view->cols = $cols;
        }

        echo $this->view->render('datatable.phtml');
    }
}
