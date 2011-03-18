<?php

/**
 * Render Filter view helper
 */
class Admin_View_Helper_RenderFilter extends Zend_View_Helper_Abstract
{
    /**
     * Render filter
     *
     * @param Zend_Form $filter
     * @return void
     */
    public function renderFilter(Zend_Form $filter)
    {
        $filter->setAttrib('class', 'filter');
        $this->view->filter = $filter;
        echo $this->view->render('filter.phtml');
    }
}
