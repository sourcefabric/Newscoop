<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;

/**
 */
class UserController extends Zend_Controller_Action
{
    /** @var Newscoop\Services\ListUserService */
    private $service;

    public function init()
    {
        $this->service = $this->_helper->service('user.list');
    }

    public function indexAction()
    {
        $users = $this->service->getPublicUsers();
        //$users = $this->service->findUsersLastNameInRange(array('t', 'b'));
        //$users = $this->service->findUsersBySearch("min tor");

        $this->view->users = array();
        foreach ($users as $user) {
            $this->view->users[] = new MetaUser($user);
        }
    }

    public function profileAction()
    {
        $username = $this->_getParam('username', false);
        if (!$username) {
            $this->_helper->flashMessenger(array('error', "User '$username' not found"));
            $this->_helper->redirector('index');
        }

        $user = $this->service->findOneBy(array(
            'username' => $username,
        ));

        if (!$user || !$user->isActive() || !$user->isPublic()) {
            $this->_helper->flashMessenger(array('error', "User '$username' not found"));
            $this->_helper->redirector('index');
        }

        $this->view->user = new MetaUser($user);
        $this->view->profile = $this->getProfile($user);
    }

    private function getProfile(User $user)
    {
        $profile = array();
        $form = new Application_Form_Profile();
        foreach ($form->getSubForm('attributes') as $field) {
            $profile[$field->getLabel()] = $user->getAttribute($field->getName());
        }

        return $profile;
    }
}
