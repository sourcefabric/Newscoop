<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User\Subscriber;

/**
 * @Acl(action="manage")
 */
class Admin_SubscriberController extends Zend_Controller_Action
{
    private $repository;

    public function init()
    {
        camp_load_translation_strings('api');
        camp_load_translation_strings('users');
        camp_load_translation_strings('user_subscriptions');

        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\User\Subscriber');
        $this->form = new Admin_Form_Subscriber;
        $this->form->setAction('')->setMethod('post');

        // set form countries
        $countries = array('' => getGS('Select country'));
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
        try {
            $subscriber = new Subscriber;
            $this->handleForm($this->form, $subscriber);
        } catch (InvalidArgumentException $e) {
            $field = $e->getMessage();
            $this->form->getElement($field)->addError(getGS("That $1 already exists, please choose a different $2.", $field, $field));
        } catch (PDOException $e) {
            $this->form->getElement('username')->addError(getGS('That user name already exists, please choose a different login name.'));
        }

        $this->view->form = $this->form;
    }

    public function editAction()
    {
        $subscriber = $this->_helper->entity->get(new Subscriber, 'user');
        $this->form->setDefaultsFromEntity($subscriber);

        try {
            $this->handleForm($this->form, $subscriber);
        } catch (InvalidArgumentException $e) {
            $field = $e->getMessage();
            $this->form->getElement($field)->addError(getGS("That $1 already exists, please choose a different $2.", $field, $field));
        }

        $this->_helper->sidebar(array(
            'label' => getGS('Subscriptions'),
            'controller' => 'subscription',
            'action' => 'index',
            'user' => $subscriber->getId(),
            'next' => 'subscriber:edit',
        ));

        $this->_helper->sidebar(array(
            'label' => getGS('Subscription IP Addresses'),
            'controller' => 'subscription-ip',
            'action' => 'index',
            'user' => $subscriber->getId(),
            'next' => 'subscriber:edit',
        ));

        $this->view->form = $this->form;
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
            'subscription' => getGS('Subscriptions'),
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

            $subsLink = sprintf('<a href="%s" class="edit" title="%s">%s</a>',
                $view->url(array(
                    'controller' => 'subscription',
                    'user' => $user->getId(),
                    'format' => NULL,
                )),
                getGS('Edit subscriptions'),
                getGS('Subscriptions')
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
                $subsLink,
                $user->getTimeCreated()->format('Y-m-d H:i:s'),
                $deleteLink,
            );
        });

        $table->dispatch();

        $this->view->actions = array(
            array(
                'label' => getGS('Add new subscriber'),
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
        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $this->repository->save($subscriber, $form->getValues());
            $this->_helper->entity->getManager()->flush();

            $this->_helper->flashMessenger(getGS('Subscriber saved.'));
            $this->_helper->redirector->gotoSimple('edit', 'subscriber', 'admin', array(
                'user' => $subscriber->getId(),
            ));
        }
    }
}
