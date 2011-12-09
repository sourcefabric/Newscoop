<?php

/**
 * Render PackageItem
 */
class Admin_View_Helper_RenderPackageItem extends Zend_View_Helper_Abstract
{
    /**
     * Render PackageItem
     *
     * @param Newscoop\News\PackageItem $item
     * @return void
     */
    public function renderPackageItem(\Newscoop\News\PackageItem $item)
    {
        $this->view->item = $item;
        echo $this->view->render('package-item.phtml');
    }
}
