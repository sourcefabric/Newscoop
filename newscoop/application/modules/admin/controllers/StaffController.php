<?php

use Newscoop\Entity\User\Staff;

class Admin_StaffController extends Zend_Controller_Action
{
    private $repository;

    private $form;

    public function preDispatch()
    {
        $user = $this->_getParam('user', 0);
        $auth = Zend_Auth::getInstance();
        if ($user != $auth->getIdentity()) { // check if user != current
            $this->_helper->acl->check('user', 'manage');
        }
    }

    public function init()
    {
        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\User\Staff');

        $this->form = new Admin_Form_Staff($this->_helper->acl->isAllowed('user', 'manage'));
        $this->form->setAction('')->setMethod('post');

        if ($this->_helper->acl->isAllowed('user', 'manage')) { // set form user groups
            $groups = array();
            $groupRepository = $this->_helper->entity->getRepository('Newscoop\Entity\User\Group');
            foreach ($groupRepository->findAll() as $group) {
                $groups[$group->getId()] = $group->getName();
            }
            $this->form->getElement('groups')->setMultioptions($groups);
        }

        // set form countries
        $countries = array();
        foreach (Country::GetCountries(1) as $country) {
            $countries[$country->getCode()] = $country->getName();
        }
        $this->form->getElement('country')->setMultioptions($countries);
    }

    public function indexAction()
    {
        $this->_forward('table');
    }

    public function addAction()
    {
        $this->_helper->acl->check('user', 'manage');

        $staff = new Staff();
        $this->handleForm($this->form, $staff);

        $this->view->form = $this->form;
        $this->view->user = $staff;
    }

    public function editAction()
    {
        $staff = $this->_helper->entity->get(new Staff, 'user');
        $this->form->setDefaultsFromEntity($staff);
        $this->handleForm($this->form, $staff);

        $this->view->form = $this->form;
        $this->view->user = $staff;

        if ($this->_helper->acl->isAllowed('user', 'manage')) {
            $this->view->actions = array(
                $this->view->url(array(
                    'action' => 'edit-access',
                    'user' => $staff->getId()
                )) => getGS('Edit permissions'),
            );
        }
    }

    public function editAccessAction()
    {
        $this->_helper->acl->check('user', 'manage');

        $staff = $this->_helper->entity->get(new Staff, 'user');
        $this->view->user = $staff;
    }

    public function deleteAction()
    {
        $this->_helper->acl->check('user', 'delete');

        $staff = $this->_helper->entity->get(new Staff, 'user');
        $this->repository->delete($staff);

        $this->_helper->entity->getManager()->flush();

        $this->_helper->flashMessenger(getGS('Staff member deleted.'));
        $this->_helper->redirector->gotoSimple('index');
    }

    public function tableAction()
    {
        $this->_helper->acl->check('user', 'manage');

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
            $this->_helper->redirector->gotoSimple('edit', 'staff', 'admin', array(
                'user' => $staff->getId(),
            ));
        }
    }
}
