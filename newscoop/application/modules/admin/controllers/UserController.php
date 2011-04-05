<?php

use Newscoop\Entity\User;

class Admin_UserController extends Zend_Controller_Action
{
    /** @var Doctrine\ORM\EntityRepository */
    private $userRepository;

    /** @var Doctrine\ORM\EntityRepository */
    private $groupRepository;

    public function init()
    {
        $this->userRepository = $this->_helper->em->getRepository('Newscoop\Entity\User');
        $this->groupRepository = $this->_helper->em->getRepository('Newscoop\Entity\User\Group');
    }

    public function indexAction()
    {
        $this->_forward('table');
    }

    /**
     * Add user
     */
    public function addAction()
    {
        $form = new Admin_Form_AddUser;
        $form->getElement('roles')->setMultioptions($this->getUserRoles());
        $form->setAction('')
            ->setMethod('post')
            ->setDefaults(array(
                'employer_type' => 'Other',
            ));

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $user = new User;
            $this->userRepository->save($user, $form->getValues());
            $this->_helper->em->flush();

            $this->_helper->flashMessenger(getGS('User saved.'));
            $this->_helper->redirector->gotoSimple('index');
        }

        $this->view->form = $form;
    }

    /**
     * Edit user
     */
    public function editAction()
    {
        $user = $this->userRepository->find($this->getRequest()->getParam('user', 0));
        if (!$user) {
            $this->_helper->flashMessenger(getGS('User not found'));
            $this->_forward('index');
        }

        $form = new Admin_Form_EditUser;
        $form->getElement('roles')->setMultioptions($this->getUserRoles());
        $form->setAction('')
            ->setMethod('post')
            ->setDefaultsFromEntity($user);

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $this->userRepository->save($user, $form->getValues());
            $this->_helper->em->flush();

            $this->_helper->flashMessenger(getGS('User saved.'));
            $this->_helper->redirector->gotoSimple('index');
        }

        $this->view->form = $form;
        $this->view->user = $user;
    }

    /**
     * Delete user
     */
    public function deleteAction()
    {
        $user = $this->getRequest()->getParam('user', NULL);
        $this->userRepository->delete($user);

        $this->_helper->em->flush();

        $this->_helper->flashMessenger(getGS('User deleted.'));
        $this->_helper->redirector->gotoSimple('index');
    }

    /**
     * List users
     */
    public function tableAction()
    {
        $table = $this->getHelper('datatable');

        $table->setEntity('Newscoop\Entity\User');

        $table->setCols(array(
            'name' => getGS('Full Name'),
            'username' => getGS('Accout Name'),
            'email' => getGS('E-Mail'),
            'timeCreated' => getGS('Creation Date'),
            getGS('Delete'),
        ));

        $view = $this->view;
        $table->setHandle(function(User $user) use ($view) {
            $editLink = sprintf('<a href="%s" class="edit" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'edit',
                    'user' => $user->getId(),
                    'format' => NULL,
                )),
                getGS('Edit user $1', $user->getName()),
                $user->getName()
            );

            $deleteLink = sprintf('<a href="%s" class="delete confirm" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'delete',
                    'user' => $user->getId(),
                    'format' => NULL,
                )),
                getGS('Delete user $1', $user->getName()),
                getGS('Delete')
            );

            return array(
                $editLink,
                $user->getUsername(),
                $user->getEmail(),
                $user->getTimeCreated()->format('Y-m-d H:i:s'),
                $deleteLink,
            );
        });

        $table->dispatch();
    }

    /**
     * Get user roles
     *
     * @return array
     */
    private function getUserRoles()
    {
        $roles = array();
        foreach ($this->groupRepository->findAll() as $group) {
            $roles[$group->getId()] = $group->getName();
        }

        return $roles;
    }
}
