<?php

/**
 * Render PackageItem Group
 */
class Admin_View_Helper_RenderPackageItemGroup extends Zend_View_Helper_Abstract
{
    /**
     * Render PackageItem Group
     *
     * @param Newscoop\News\Group $group
     * @param Newscoop\News\PackageItem $item
     * @return void
     */
    public function renderPackageItemGroup(\Newscoop\News\Group $group, \Newscoop\News\PackageItem $item)
    {
        $this->view->group = $group;
        $this->view->item = $item;
        echo $this->view->render('package-item-group.phtml');
    }
}
