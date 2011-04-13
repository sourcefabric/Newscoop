<?php

use Newscoop\Entity\Subscription,
    Newscoop\Entity\User\Subscriber;

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

        $publications = $this->_helper->entity->getRepository('Newscoop\Entity\Publication')->getSubscriberOptions($user);
        if (empty($publications)) {
            $this->_helper->flashMessenger(getGS('Subscriptions exist for all available publications!'));
            $this->_helper->redirector('index', 'subscription', 'admin', array(
                'user' => $user->getId(),
            ));
        }

        $form = new Admin_Form_Subscription(array(
            'publications' => $publications,
            'languages' => $this->getLanguages(),
        ));

        $form->setMethod('post')->setAction('');

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $subscription = new Subscription;
            $this->_helper->entity->getRepository($subscription)->save($subscription, $user, $form->getValues());
            $this->_helper->entity->flushManager();

            $this->_helper->flashMessenger(getGS('Subscription $1', getGS('created')));
            $this->_helper->redirector('index', 'subscription', 'admin', array(
                'user' => $this->_getParam('user', 0),
            ));
        }

        $this->view->form = $form;
    }

    public function toggleAction()
    {
        $em = $this->_helper->entity->getManager();

        $subscription = $this->_helper->entity->get('Newscoop\Entity\Subscription', 'subscription');
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

        $subscription = $this->_helper->entity->get('Newscoop\Entity\Subscription', 'subscription');
        $em->remove($subscription);
        $em->flush();

        $this->_helper->flashMessenger(getGS('Subscription $1', getGS('removed')));
        $this->_helper->redirector('index', 'subscription', 'admin', array(
            'user' => $this->_getParam('user', 0),
        ));
    }

    /**
     * Get languages
     *
     * @return array
     */
    private function getLanguages()
    {
        $repository = $this->_helper->entity->getRepository('Newscoop\Entity\Publication');
        $publications = $repository->findAll();

        if (empty($publications)) {
            return array();
        }

        $publication = $publications[0];

        $langs = array();
        foreach ($publication->getLanguages() as $lang) {
            $langs[$lang->getId()] = $lang->getName();
        }

        return $langs;
    }
}

