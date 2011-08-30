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
        $user = $this->_getParam('user');
        $tokenService = $this->_helper->service('user.token');

        $this->view->user = new MetaUser($user);
        $this->view->token = $tokenService->generateToken($user, 'email.confirm');

        $server = $this->getRequest()->getServer();
        $this->view->publication = $server['SERVER_NAME'];
    }
}
