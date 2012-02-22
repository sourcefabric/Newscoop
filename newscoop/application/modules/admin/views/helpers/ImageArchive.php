<?php

/**
 * Image archive
 */
class Admin_View_Helper_ImageArchive extends Zend_View_Helper_Abstract
{
    /**
     * Image archive
     *
     * @return string
     */
    public function imageArchive()
    {
        return $this->view->render('image/archive.phtml');
    }
}
