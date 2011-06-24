<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User\Group;

/**
 * @Acl(action="manage")
 */
class Admin_UserGroupController extends Zend_Controller_Action
{
    private $repository;

    public function init()
    {
        camp_load_translation_strings('user_types');

        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\User\Group');
    }

    public function indexAction()
    {
        $this->view->groups = $this->repository->findAll();

        $this->_helper->sidebar(array(
            'label' => getGS('Add new user type'),
            'controller' => 'user-group',
            'action' => 'add',
        ));
    }

    public function addAction()
    {
        $form = $this->getForm()->setMethod('post')->setAction('');
        $group = new Group;

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            try {
                $this->repository->save($group, $form->getValues());
                $this->_helper->entity->getManager()->flush();

                $this->_helper->flashMessenger->addMessage(getGS('User type added.'));
                $this->_helper->redirector('index');
            } catch (Exception $e) {
                $form->getElement('name')->addError(getGS('That type name already exists, please choose a different name.'));
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        $form = $this->getForm();
        $group = $this->_helper->entity('Newscoop\Entity\User\Group', 'group');

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost()) && $form->name != $group->getName()) {
            try {
                $this->repository->save($group, $form->getValues());
                $this->_helper->entity->flushManager();
                $this->_helper->flashMessenger->addMessage(getGS('User type saved.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(getGS('That type name already exists, please choose a different name.'));
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
        $groupId = $this->getRequest()->getParam('group', 0);
        $group = $this->repository->find($groupId);
        $users = $group->getUsers();
        
        if (count($users) == 0) {
            $this->repository->delete($groupId);
            $this->_helper->flashMessenger->addMessage(getGS('User type deleted.'));
        } else {
            $this->_helper->flashMessenger->addMessage(getGS('Can not delete a user type with assigned users.'));
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
        $form = new Zend_Form;

        $form->addElement('text', 'name', array(
            'label' => getGS('Name'),
            'required' => true,
        ));

        $form->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
            'ignore' => true,
        ));

        return $form;
    }
}
