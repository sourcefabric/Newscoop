<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;
use Newscoop\Entity\User\Group;

/**
 * @Acl(action="manage")
 */
class Admin_UserGroupController extends Zend_Controller_Action
{
    private $repository;

    public function init()
    {
        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\User\Group');
    }

    public function indexAction()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->view->groups = $this->repository->findAll();

        $this->_helper->sidebar(array(
            'label' => $translator->trans('Add new user type'),
            'controller' => 'user-group',
            'action' => 'add',
        ));
    }

    public function addAction()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');
        $form = $this->getForm()->setMethod('post')->setAction('');
        $group = new Group;

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            try {
                $this->repository->save($group, $form->getValues());
                $this->_helper->entity->getManager()->flush();

                $this->_helper->flashMessenger->addMessage($translator->trans('User type added.', array(), 'user_types'));
                $this->_helper->redirector('index');
            } catch (Exception $e) {
                $form->getElement('name')->addError($translator->trans('That type name already exists, please choose a different name.', array(), 'user_types'));
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');
        $form = $this->getForm();
        $group = $this->_helper->entity('Newscoop\Entity\User\Group', 'group');

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost()) && $form->name != $group->getName()) {
            try {
                $this->repository->save($group, $form->getValues());
                $this->_helper->entity->flushManager();
                $this->_helper->flashMessenger->addMessage($translator->trans('User type saved.', array(), 'user_types'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger($translator->trans('That type name already exists, please choose a different name.', array(), 'user_types'));
            }
        }

        $this->_helper->redirector('edit-access', 'user-group', 'admin', array(
            'group' => $group->getId(),
        ));
    }

    public function editAccessAction()
    {
        $group = $this->_helper->entity(new Group, 'group');
        $this->view->group = $group;

        $form = $this->getForm();
        $form->setMethod('post');

        $form->setAction($this->view->url(array(
            'action' => 'edit',
        )));

        $form->setDefaults(array(
            'name' => $group->getName(),
        ));

        $this->view->form = $form;

        $this->_helper->actionStack('edit', 'acl', 'admin', array(
            'role' => $group->getRoleId(),
            'group' => $group->getId(),
        ));
    }

    public function deleteAction()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');
        $groupId = $this->getRequest()->getParam('group', 0);
        $group = $this->repository->find($groupId);
        $users = $group->getUsers();

        if (count($users) == 0) {
            $this->repository->delete($groupId);
            $this->_helper->flashMessenger->addMessage($translator->trans('User type deleted.', array(), 'user_types'));
        } else {
            $this->_helper->flashMessenger->addMessage($translator->trans('Can not delete a user type with assigned users.', array(), 'user_types'));
        }
        $this->_helper->redirector('index');
    }

    /**
     * Get group form
     *
     * @return Zend_Form
     */
    private function getForm()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');
        $form = new Zend_Form;

        $form->addElement('text', 'name', array(
            'label' => $translator->trans('Name'),
            'required' => true,
        ));

        $form->addElement('submit', 'submit', array(
            'label' => $translator->trans('Save'),
            'ignore' => true,
        ));

        return $form;
    }
}
