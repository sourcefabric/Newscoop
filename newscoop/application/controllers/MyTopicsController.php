<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class MyTopicsController extends Zend_Controller_Action
{
    /** @var Newscoop\Entity\User */
    private $user;

    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->user = $this->_helper->service('user')->getCurrentUser();
    }

    public function preDispatch()
    {
        if (!$this->user && $this->getRequest()->getActionName() == 'index') {
            $this->_forward('auth');
        }
    }

    public function indexAction()
    {
        $this->view->user = new MetaUser($this->user);
    }

    public function authAction()
    {
    }
}
