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
        $subscriber = new Subscriber;

        $this->handleForm($this->form, $subscriber);

        $this->view->form = $this->form;
        $this->view->user = $subscriber;
    }

    public function editAction()
    {
        $subscriber = $this->_helper->entity->get(new Subscriber, 'user');
        $this->form->setDefaultsFromEntity($subscriber);

        $this->handleForm($this->form, $subscriber);

        $this->view->form = $this->form;
        $this->view->user = $subscriber;
    }

    public function deleteAction()
    {
        $subscriber = $this->_helper->entity->get(new Subscriber, 'user');
        $this->repository->delete($subscriber);

        $this->_helper->entity->getManager()->flush();

        $this->_helper->flashMessenger(getGS('Subscriber deleted.'));
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
                getGS('Edit subscriber $1', $user->getName()),
                $user->getName()
            );

            $deleteLink = sprintf('<a href="%s" class="delete confirm" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'delete',
                    'user' => $user->getId(),
                    'format' => NULL,
                )),
               getGS('Delete subscriber $1', $user->getName()),
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

    private function handleForm(Zend_Form $form, Subscriber $subscriber)
    {
        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $this->repository->save($subscriber, $form->getValues());
            $this->_helper->entity->getManager()->flush();

            $this->_helper->flashMessenger(getGS('Subscriber saved.'));
            $this->_helper->redirector->gotoSimple('index');
        }
    }
}
