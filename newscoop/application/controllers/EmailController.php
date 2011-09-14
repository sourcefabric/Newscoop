<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class EmailController extends Zend_Controller_Action
{
    public function init()
    {
        $this->getHelper('viewRenderer')
            ->setView($this->view)
            ->setViewScriptPathSpec(':controller_:action.:suffix')
            ->setViewSuffix('tpl');

        $this->getHelper('layout')
            ->disableLayout();
    }

    public function confirmAction()
    {
        $this->view->user = $this->_getParam('user');
        $this->view->token = $this->_getParam('token');

        $server = $this->getRequest()->getServer();
        $this->view->publication = $server['SERVER_NAME'];
    }
}
