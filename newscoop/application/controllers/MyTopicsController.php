<?php
/**
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
use Newscoop\Exception\AuthenticationException;

/**
 */
class MyTopicsController extends Zend_Controller_Action
{
    /** @var Newscoop\Entity\User */
    private $user;

    public function init()
    {
        $this->_helper->layout->disableLayout();
        try {
            $this->user = $this->_helper->service('user')->getCurrentUser();
        } catch (AuthenticationException $e) {
            $this->user = null;
        }
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
        $this->_helper->redirector('index', 'auth');
    }
}
