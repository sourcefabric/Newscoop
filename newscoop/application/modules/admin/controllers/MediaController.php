<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;

/**
 * @Acl(ignore=True)
 */
class Admin_MediaController extends Zend_Controller_Action
{
    const LIMIT = 8;

    public function init()
    {
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');

        $this->_helper->contextSwitch
            ->addActionContext('list-slideshows', 'json')
            ->addActionContext('list-images', 'json')
            ->initContext();
    }

    public function listImagesAction()
    {
        $this->_helper->layout->disableLayout();

        $page = $this->_getParam('page', 1);
        $count = $this->_getParam('q', false) ? 0 : $this->_helper->service('image')->getCountBy(array());
        $paginator = Zend_Paginator::factory($count);
        $paginator->setItemCountPerPage(self::LIMIT);
        $paginator->setCurrentPageNumber($page);
        $paginator->setView($this->view);
        $paginator->setDefaultScrollingStyle('Sliding');

        $this->view->paginator = $paginator;

        if ($this->_getParam('q', false)) {
            $this->view->images = $this->_helper->service('image.search')->find($this->_getParam('q'));
        } else {
            $this->view->images = $this->_helper->service('image')->findBy(array(), array('id' => 'desc'), self::LIMIT, ($paginator->getCurrentPageNumber() - 1) * self::LIMIT);
        }
    }

    public function listSlideshowsAction()
    {
        $criteria = array();
        $paginator = Zend_Paginator::factory($this->_helper->service('package')->getCountBy($criteria));
        $paginator->setItemCountPerPage(25);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $paginator->setView($this->view);
        $paginator->setDefaultScrollingStyle('Sliding');
        $this->view->paginator = $paginator;
        $this->view->slideshows = $this->_helper->service('package')->findBy(
            $criteria,
            array('id' => 'desc'),
            $paginator->getCurrentItemCount(),
            ($paginator->getCurrentPageNumber() - 1) * $paginator->getItemCountPerPage()
        );
    }
}
