<?php

/**
 * Base user controller
 */
abstract class Admin_UserController extends Zend_Controller_Action
{
    /** @var Newscoop\Entity\User */
    protected $entity;

    /** @var Doctrine\ORM\EntityRepository */
    protected $repository;

    /** @var Zend_Form */
    protected $form;

    public function indexAction()
    {
        $this->_forward('table');
    }

    /**
     * Add user
     */
    public function addAction()
    {
        $this->form->getElement('country')->setMultioptions($this->getCountries());

        $this->form->setAction('')
            ->setMethod('post')
            ->setDefaults(array(
                'employer_type' => 'Other',
            ));

        if ($this->getRequest()->isPost() && $this->form->isValid($_POST)) {
            $this->repository->save($this->entity, $this->form->getValues());
            $this->_helper->em->flush();

            $this->_helper->flashMessenger(getGS('User saved.'));
            $this->_helper->redirector->gotoSimple('index');
        }

        $this->view->form = $form;
    }

    /**
     * Edit staff
     */
    public function editAction()
    {
        $staff = $this->getStaff();

        $form = new Admin_Form_EditUser();
        $form->getElement('roles')->setMultioptions($this->getRoles());
        $form->getElement('country')->setMultioptions($this->getCountries());
        $form->setAction('')
            ->setMethod('post')
            ->setDefaultsFromEntity($staff);

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $this->repository->save($staff, $form->getValues());
            $this->_helper->em->flush();

            $this->_helper->flashMessenger(getGS('Staff member saved.'));
            $this->_helper->redirector->gotoSimple('index');
        }

        $this->view->form = $form;
        $this->view->staff = $staff;
    }

    /**
     * Delete user
     */
    public function deleteAction()
    {
        $staff = $this->getStaff();
        $this->repository->delete($staff);

        $this->_helper->em->flush();

        $this->_helper->flashMessenger(getGS('Staff member deleted.'));
        $this->_helper->redirector->gotoSimple('index');
    }

    /**
     * List users
     */
    final public function tableAction()
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

    /**
     * Get staff entity by param
     *
     * @return Newscoop\Entity\User\Staff
     */
    private function getStaff()
    {
        $staff = $this->repository->find($this->getRequest()->getParam('user', 0));
        if (!$staff) {
            $this->_helper->flashMessenger(getGS('Staff member not found'));
            $this->_forward('index');
        }

        return $staff;
    }

    /**
     * Get available countries
     *
     * @return array
     */
    protected function getCountries()
    {
        static $countries = NULL;
        if ($countries === NULL) {
            $countries = array();
            foreach (Country::GetCountries(1) as $country) {
                $countries[$country->getCode()] = $country->getName();
            }
        }

        return $countries;
    }
}
