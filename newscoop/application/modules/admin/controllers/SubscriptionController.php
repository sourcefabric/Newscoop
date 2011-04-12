<?php

use Newscoop\Entity\User\Subscriber;

/**
 * @Acl(action="manage")
 */
class Admin_SubscriptionController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $user = $this->_helper->entity->get('Newscoop\Entity\User\Subscriber', 'user');
        $this->view->user = $user;

        $this->view->actions = array(
            array(
                'label' => getGS('Add new subscription'),
                'module' => $this->_getParam('module'),
                'controller' => $this->_getParam('controller'),
                'action' => 'add',
                'resource' => $this->_getParam('controller'),
                'privilege' => 'manage',
                'reset_params' => false,
            ),
        );
    }

    public function addAction()
    {
        $user = $this->_helper->entity->get('Newscoop\Entity\User\Subscriber', 'user');

        $form = $this->getForm($user);
        $form->setMethod('post')->setAction('');

        $this->view->form = $form;
    }

    public function toggleAction()
    {
        $em = $this->_helper->entity->getManager();

        $subscription = $this->_helper->entity->get('Newscoop\Entity\User\Subscription', 'subscription');
        $subscription->setActive(!$subscription->isActive());
        $em->flush();

        $this->_helper->flashMessenger(getGS('Subscription $1', $subscription->isActive() ? getGS('activated') : getGS('deactivated')));
        $this->_helper->redirector('index', 'subscription', 'admin', array(
            'user' => $this->_getParam('user', 0),
        ));
    }

    public function deleteAction()
    {
        $em = $this->_helper->entity->getManager();

        $subscription = $this->_helper->entity->get('Newscoop\Entity\User\Subscription', 'subscription');
        $em->remove($subscription);
        $em->flush();

        $this->_helper->flashMessenger(getGS('Subscription $1', getGS('removed')));
        $this->_helper->redirector('index', 'subscription', 'admin', array(
            'user' => $this->_getParam('user', 0),
        ));
    }

    /**
     * Get form
     *
     * @return Zend_Form
     */
    private function getForm(Subscriber $user)
    {
        $form = new Zend_Form;

        $form->addElement('select', 'publication', array(
            'label' => getGS('Publication'),
            'required' => true,
            'multioptions' => $this->_helper->entity->getRepository('Newscoop\Entity\Publication')->getSubscriberOptions($user),
        ));

        $form->addElement('select', 'language-set', array(
            'label' => getGS('Language'),
            'required' => true,
            'multioptions' => array(
                'select' => getGS('Individual languages'),
                'all' => getGS('Regardless of the language'),
            ),
        ));

        $form->addElement('select', 'languages', array(
            'multioptions' => array(
                'tic' => 'toc',
            ),
        ));

        $form->addElement('submit', 'submit', array(
            'label' => getGS('Add'),
        ));

        return $form;
    }
}

