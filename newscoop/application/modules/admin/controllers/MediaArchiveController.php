<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * @Acl(ignore=True)
 */
class Admin_MediaArchiveController extends Zend_Controller_Action
{
    const LIMIT = 8;

    public function init()
    {
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');
    }

    public function listImagesAction()
    {
        $this->_helper->layout->disableLayout();

        $page = $this->_getParam('page', 1);
        $count = $this->_helper->service('image')->getCountBy(array());
        $paginator = Zend_Paginator::factory($count);
        $paginator->setItemCountPerPage(self::LIMIT);
        $paginator->setCurrentPageNumber($page);
        $paginator->setView($this->view);
        $paginator->setDefaultScrollingStyle('Sliding');

        $this->view->paginator = $paginator;
        $this->view->images = $this->_helper->service('image')->findBy(array(), array('id' => 'desc'), self::LIMIT, ($paginator->getCurrentPageNumber() - 1) * self::LIMIT);
    }
}
