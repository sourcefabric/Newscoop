<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;
use Newscoop\Entity\User\Subscriber;

/**
 * @Acl(action="manage")
 */
class Admin_SubscriberController extends Zend_Controller_Action
{
    private $repository;

    public function init()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\User\Subscriber');
        $this->form = new Admin_Form_Subscriber;
        $this->form->setAction('')->setMethod('post');

        // set form countries
        $countries = array('' => $translator->trans('Select country', array(), 'user_subscriptions'));
        foreach (Country::GetCountries(1) as $country) {
            $countries[$country->getCode()] = $country->getName();
        }

        //$this->form->getElement('country')->setMultioptions($countries);
    }

    public function indexAction()
    {
        $this->_forward('table');
    }

    public function addAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        try {
            $subscriber = new Subscriber;
            $this->handleForm($this->form, $subscriber);
        } catch (InvalidArgumentException $e) {
            $field = $e->getMessage();
            $this->form->getElement($field)->addError($translator->trans("That $1 already exists, please choose a different $2.", array('$1' => $field, '$2' => $field), 'user_subscriptions'));
        } catch (PDOException $e) {
            $this->form->getElement('username')->addError($translator->trans('That user name already exists, please choose a different login name.', array(), 'user_subscriptions'));
        }

        $this->view->form = $this->form;
    }

    public function editAction()
    {
        $subscriber = $this->_helper->entity->get(new Subscriber, 'user');
        $this->form->setDefaultsFromEntity($subscriber);
        $translator = \Zend_Registry::get('container')->getService('translator');

        try {
            $this->handleForm($this->form, $subscriber);
        } catch (InvalidArgumentException $e) {
            $field = $e->getMessage();
            $this->form->getElement($field)->addError($translator->trans("That $1 already exists, please choose a different $2.", array('$1' => $field, '$2' => $field), 'user_subscriptions'));
        }

        $this->_helper->sidebar(array(
            'label' => $translator->trans('Subscriptions'),
            'controller' => 'subscription',
            'action' => 'index',
            'user' => $subscriber->getId(),
            'next' => 'subscriber:edit',
        ));

        $this->_helper->sidebar(array(
            'label' => $translator->trans('Subscription IP Addresses', array(), 'user_subscriptions'),
            'controller' => 'subscription-ip',
            'action' => 'index',
            'user' => $subscriber->getId(),
            'next' => 'subscriber:edit',
        ));

        $this->view->form = $this->form;
    }

    public function deleteAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $subscriber = $this->_helper->entity->get(new Subscriber, 'user');
        $this->repository->delete($subscriber);

        $this->_helper->entity->getManager()->flush();

        $this->_helper->flashMessenger($translator->trans('Subscriber deleted.', array(), 'user_subscriptions'));
        $this->_helper->redirector->gotoSimple('index');
    }

    public function tableAction()
    {
        $table = $this->getHelper('datatable');

        $table->setEntity('Newscoop\Entity\User\Subscriber');

        $translator = \Zend_Registry::get('container')->getService('translator');

        $table->setCols(array(
            'name' => $translator->trans('Full Name', array(), 'user_subscriptions'),
            'username' => $translator->trans('Accout Name', array(), 'user_subscriptions'),
            'email' => $translator->trans('E-Mail', array(), 'user_subscriptions'),
            'subscription' => $translator->trans('Subscriptions'),
            'timeCreated' => $translator->trans('Creation Date', array(), 'user_subscriptions'),
            'delete' => $translator->trans('Delete'),
        ));

        $view = $this->view;
        $table->setHandle(function(Subscriber $user) use ($view) {
            $editLink = sprintf('<a href="%s" class="edit" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'edit',
                    'user' => $user->getId(),
                    'format' => NULL,
                )),
                $translator->trans('Edit subscriber $1', array('$1' => $user->getName()), 'user_subscriptions'),
                $user->getName()
            );

            $subsLink = sprintf('<a href="%s" class="edit" title="%s">%s</a>',
                $view->url(array(
                    'controller' => 'subscription',
                    'user' => $user->getId(),
                    'format' => NULL,
                )),
                $translator->trans('Edit subscriptions', array(), 'user_subscriptions'),
                $translator->trans('Edit subscriptions', array(), 'user_subscriptions')
            );

            $deleteLink = sprintf('<a href="%s" class="delete confirm" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'delete',
                    'user' => $user->getId(),
                    'format' => NULL,
                )),
                $translator->trans('Delete subscriber $1', array('$1' => $user->getName()), 'user_subscriptions'),
                $translator->trans('Delete')
            );

            return array(
                $editLink,
                $user->getUsername(),
                $user->getEmail(),
                $subsLink,
                $user->getTimeCreated()->format('Y-m-d H:i:s'),
                $deleteLink,
            );
        });

        $table->dispatch();

        $this->view->actions = array(
            array(
                'label' => $translator->trans('Add new subscriber'),
                'module' => 'admin',
                'controller' => 'subscriber',
                'action' => 'add',
                'resource' => 'subscriber',
                'privilege' => 'manage',
                'class' => 'add',
            ),
        );
    }

    private function handleForm(Zend_Form $form, Subscriber $subscriber)
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $this->repository->save($subscriber, $form->getValues());
            $this->_helper->entity->getManager()->flush();

            $this->_helper->flashMessenger($translator->trans('Subscriber saved.', array(), 'user_subscriptions'));
            $this->_helper->redirector->gotoSimple('edit', 'subscriber', 'admin', array(
                'user' => $subscriber->getId(),
            ));
        }
    }
}
