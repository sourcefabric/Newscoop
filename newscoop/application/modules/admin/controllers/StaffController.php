<?php

use Newscoop\Entity\User\Staff;

class Admin_StaffController extends Zend_Controller_Action
{
    private $repository;

    private $form;

    public function init()
    {
        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\User\Staff');

        $this->form = new Admin_Form_Staff;
        $this->form->setAction('')->setMethod('post');

        // set form user groups
        $groups = array();
        $groupRepository = $this->_helper->entity->getRepository('Newscoop\Entity\User\Group');
        foreach ($groupRepository->findAll() as $group) {
            $groups[$group->getId()] = $group->getName();
        }
        $this->form->getElement('groups')->setMultioptions($groups);
    }

    public function indexAction()
    {
        $this->_forward('table');
    }

    public function addAction()
    {
        $staff = new Staff();

        $this->handleForm($this->form, $staff);

        $this->view->form = $this->form;
        $this->view->staff = $staff;
    }

    public function editAction()
    {
        $staff = $this->_helper->entity->get(new Staff, 'user');
        $this->form->setDefaultsFromEntity($staff);

        $this->handleForm($this->form, $staff);

        $this->view->form = $this->form;
        $this->view->staff = $staff;
    }

    public function deleteAction()
    {
        $staff = $this->_helper->entity->get(new Staff, 'user');
        $this->repository->delete($staff);

        $this->_helper->entity->getManager()->flush();

        $this->_helper->flashMessenger(getGS('Staff member deleted.'));
        $this->_helper->redirector->gotoSimple('index');
    }

    public function tableAction()
    {
        $table = $this->getHelper('datatable');

        $table->setEntity('Newscoop\Entity\User\Staff');

        $table->setCols(array(
            'name' => getGS('Full Name'),
            'username' => getGS('Accout Name'),
            'email' => getGS('E-Mail'),
            'timeCreated' => getGS('Creation Date'),
            getGS('Delete'),
        ));

        $view = $this->view;
        $table->setHandle(function(Staff $staff) use ($view) {
            $editLink = sprintf('<a href="%s" class="edit" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'edit',
                    'user' => $staff->getId(),
                    'format' => NULL,
                )),
                getGS('Edit staff member $1', $staff->getName()),
                $staff->getName()
            );

            $deleteLink = sprintf('<a href="%s" class="delete confirm" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'delete',
                    'user' => $staff->getId(),
                    'format' => NULL,
                )),
                getGS('Delete staff member $1', $staff->getName()),
                getGS('Delete')
            );

            return array(
                $editLink,
                $staff->getUsername(),
                $staff->getEmail(),
                $staff->getTimeCreated()->format('Y-m-d H:i:s'),
                $deleteLink,
            );
        });

        $table->dispatch();
    }

    private function handleForm(Zend_Form $form, Staff $staff)
    {
        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $this->repository->save($staff, $form->getValues());
            $this->_helper->entity->getManager()->flush();

            $this->_helper->flashMessenger(getGS('Staff member saved.'));
            $this->_helper->redirector->gotoSimple('index');
        }
    }
}
