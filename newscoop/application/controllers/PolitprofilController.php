<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class PolitprofilController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        if ($this->_helper->service('user')->getCurrentUser() !== null) {
            $this->_helper->redirector('index', 'dashboard');
        }
    }

    public function indexAction()
    {
        $this->view->role = $this->_helper->config('role')->politician;
    }
}
