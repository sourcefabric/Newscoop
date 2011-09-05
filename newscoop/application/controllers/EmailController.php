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
    public function confirmAction()
    {
        $this->view->user = $this->_getParam('user');
        $this->view->token = $this->_getParam('token');

        $server = $this->getRequest()->getServer();
        $this->view->publication = $server['SERVER_NAME'];
    }
}
