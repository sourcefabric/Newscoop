<?php

/**
 * Render Pager view helper
 */
class Admin_View_Helper_RenderPager extends Zend_View_Helper_Abstract
{
    /**
     * Render paginator
     *
     * @param SimplePager $pager
     * @return void
     */
    public function renderPager(SimplePager $pager)
    {
        $this->view->pager = $pager;
        echo $this->view->render('pager.phtml');
    }
}
