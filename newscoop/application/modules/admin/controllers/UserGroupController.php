<?php

use Newscoop\Entity\User\Group;

class Admin_UserGroupController extends Zend_Controller_Action
{
    private $repository;

    public function init()
    {
        camp_load_translation_strings('user_types');

        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\User\Group');
    }

    public function preDispatch()
    {
        $this->_helper->acl->check('usertype', 'manage');
    }

    public function indexAction()
    {
        $this->view->groups = $this->repository->findAll();
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
                $form->getElement('name')->addError(getGS('Name taken.'));
            }
        }

        $this->view->form = $form;
    }

    public function editAccessAction()
    {
        $group = $this->_helper->entity->get(new Group, 'group');
        $this->view->group = $group;
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
