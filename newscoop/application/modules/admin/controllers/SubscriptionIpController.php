<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;
use Newscoop\Entity\User\Ip;
use Newscoop\Entity\User\Subscriber;

/**
 * @Acl(resource="subscription", action="manage")
 */
class Admin_SubscriptionIpController extends Zend_Controller_Action
{
    public function init(){}


    public function indexAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $subscriber = $this->_helper->entity('Newscoop\Entity\User\Subscriber', 'user');

        $this->view->subscriber = $subscriber;
        $this->view->actions = array(
            array(
                'label' => $translator->trans('Add new IP address', array(), 'user_subscriptions'),
                'module' => 'admin',
                'controller' => 'subscription-ip',
                'action' => 'add',
                'reset_params' => false,
                'class' => 'add',
            ),
        );
    }

    public function addAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $subscriber = $this->_helper->entity('Newscoop\Entity\User\Subscriber', 'user');

        $form = $this->createForm();
        $form->setAction('')->setMethod('post');

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $ip = new Ip;
            $repository = $this->_helper->entity->getRepository($ip);

            try {
                $repository->save($ip, $subscriber, $form->getValues());
                $this->_helper->entity->flushManager();

                $this->_helper->flashMessenger($translator->trans('IP Address $1', array('$1' => $translator->trans('saved', array(), 'user_subscriptions')), 'user_subscriptions'));
                $this->_helper->redirector('edit', 'subscriber', 'admin', array(
                    'user' => $subscriber->getId(),
                ));
            } catch (PDOException $e) {
                $form->getElement('ip')->addError($translator->trans('IP Address added allready', array(), 'user_subscriptions'));
            }
        }

        $this->view->form = $form;
    }

    public function deleteAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $subscriber = $this->_helper->entity(new Subscriber, 'user');
        $repository = $this->_helper->entity->getRepository(new Ip);
        $ip = $this->_getParam('ip', '');

        $repository->delete($ip, $subscriber);
        $this->_helper->entity->flushManager();

        $this->_helper->flashMessenger($translator->trans('IP Address $1', array('$1' => $translator->trans('deleted', array(), 'user_subscriptions')), 'user_subscriptions'));
        $this->_helper->redirector('edit', 'subscriber', 'admin', array(
            'user' => $subscriber->getId(),
        ));
    }

    public function createForm()
    {
        $form = new Zend_Form;

        $translator = \Zend_Registry::get('container')->getService('translator');
        $form->addElement('text', 'ip', array(
            'label' => $translator->trans('Start IP', array(), 'user_subscriptions'),
            'required' => true,
            'validators' => array(
                array('notEmpty', true, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => $translator->trans("Value is required and cant be empty", array(), 'user_subscriptions'),
                    ),
                )),
                array('ip', true, array(
                    'messages' => array(
                        Zend_Validate_Ip::NOT_IP_ADDRESS => $translator->trans("%value% is not a valid IP Address", array(), 'user_subscriptions'),
                    ),
                )),
            ),
        ));

        $form->addElement('text', 'number', array(
            'label' => $translator->trans('Number of addresses', array(), 'user_subscriptions'),
            'required' => true,
            'validators' => array(
                array('notEmpty', true, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => $translator->trans("Value is required and cant be empty", array(), 'user_subscriptions'),
                    ),
                )),
                array('greaterThan', true, array(
                    0,
                    'messages' => array(
                        Zend_Validate_GreaterThan::NOT_GREATER => $translator->trans("%value% must be greater than %min%", array(), 'user_subscriptions'),
                    ),
                )),
            ),
        ));

        $form->addElement('submit', 'submit', array(
            'label' => $translator->trans('Save'),
        ));

        return $form;
    }
}
