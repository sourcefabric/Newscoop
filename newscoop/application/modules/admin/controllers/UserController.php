<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * User controller
 *
 * @Acl(action="manage")
 */
class Admin_UserController extends Zend_Controller_Action
{
    /** @var Newscoop\Services\UserService */
    private $service;

    /**
     */
    public function init()
    {
        camp_load_translation_strings('api');
        camp_load_translation_strings('users');

        $this->service = $this->_helper->service('user');
    }

    public function indexAction()
    {
        $this->view->users = $this->service->findAll();
    }

    public function editAction()
    {
        $userId = $this->_getParam('user', false);
        if (!$userId) {
            $this->_helper->flashMessenger(array('error', getGS('User not specified')));
            $this->_helper->redirector('index');
        }

        $user = $this->service->find($userId);
        if (empty($user)) {
            $this->_helper->flashMessenger(array('error', sprintf(getGS("User with id '%d' not found"), $userId)));
            $this->_helper->redirector('index');
        }

        $this->view->user = $user;
    }
}
