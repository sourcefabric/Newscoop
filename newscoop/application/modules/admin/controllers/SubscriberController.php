<?php

use Newscoop\Entity\User\Subscriber;

class Admin_SubscriberController extends Zend_Controller_Action
{
    private $repository;

    public function init()
    {
        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\User\Subscriber');
        $this->form = new Admin_Form_Subscriber;
        $this->form->setAction('')->setMethod('post');
    }

    public function indexAction()
    {
        $this->_forward('table');
    }

    public function addAction()
    {
        $subscriber = new Subscriber;

        $this->handleForm($this->form, $subscriber);

        $this->view->form = $this->form;
        $this->view->staff = $staff;
    }

    public function deleteAction()
    {
        $staff = $this->getStaff();
        $this->repository->delete($staff);

        $this->_helper->em->flush();

        $this->_helper->flashMessenger(getGS('Staff member deleted.'));
        $this->_helper->redirector->gotoSimple('index');
    }

    public function tableAction()
    {
        $table = $this->getHelper('datatable');

        $table->setEntity('Newscoop\Entity\User\Subscriber');

        $table->setCols(array(
            'name' => getGS('Full Name'),
            'username' => getGS('Accout Name'),
            'email' => getGS('E-Mail'),
            'timeCreated' => getGS('Creation Date'),
            'delete' => getGS('Delete'),
        ));

        $view = $this->view;
        $table->setHandle(function(Subscriber $user) use ($view) {
            $editLink = sprintf('<a href="%s" class="edit" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'edit',
                    'user' => $user->getId(),
                    'format' => NULL,
                )),
                getGS('Edit staff member $1', $user->getName()),
                $user->getName()
            );

            $deleteLink = sprintf('<a href="%s" class="delete confirm" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'delete',
                    'user' => $user->getId(),
                    'format' => NULL,
                )),
                getGS('Delete staff member $1', $user->getName()),
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
}
