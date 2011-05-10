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

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            try {
                $group = new Group;
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

    public function editAccessAction()
    {
        $group = $this->_helper->entity(new Group, 'group');
        $this->view->group = $group;

        $this->_helper->actionStack('edit', 'acl', 'admin', array(
            'role' => $group->getRoleId(),
            'group' => $group->getId(),
        ));
    }

    public function deleteAction()
    {
        $this->repository->delete((int) $this->getRequest()->getParam('group', 0));

        $this->_helper->flashMessenger->addMessage(getGS('User type deleted.'));
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
            'label' => getGS('Add'),
            'ignore' => true,
        ));

        return $form;
    }
}
