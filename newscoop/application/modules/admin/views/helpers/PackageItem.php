<?php

/**
 * Package item helper
 */
class Admin_View_Helper_PackageItem extends Zend_View_Helper_Abstract
{
    /**
     * Render package item
     *
     * @param Newscoop\Package\Item $item
     * @return string
     */
    public function packageItem(\Newscoop\Package\Item $item)
    {
        $this->view->item = $item;
        return $this->view->render('package/item.phtml');
    }
}
