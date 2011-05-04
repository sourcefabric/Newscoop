<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User\Ip,
    Newscoop\Entity\User\Subscriber;

/**
 * @Acl(resource="subscription", action="manage")
 */
class Admin_SubscriptionIpController extends Zend_Controller_Action
{
    public function init()
    {
        camp_load_translation_strings('api');
        camp_load_translation_strings('users');
        camp_load_translation_strings('user_subscriptions');
    }

    public function indexAction()
    {
        $subscriber = $this->_helper->entity('Newscoop\Entity\User\Subscriber', 'user');

        $this->view->subscriber = $subscriber;
        $this->view->actions = array(
            array(
                'label' => getGS('Add new IP address'),
                'module' => 'admin',
                'controller' => 'subscription-ip',
                'action' => 'add',
                'reset_params' => false,
            ),
        );
    }

    public function addAction()
    {
        $subscriber = $this->_helper->entity('Newscoop\Entity\User\Subscriber', 'user');

        $form = $this->createForm();
        $form->setAction('')->setMethod('post');

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $ip = new Ip;
            $repository = $this->_helper->entity->getRepository($ip);

            try {
                $repository->save($ip, $subscriber, $form->getValues());
                $this->_helper->entity->flushManager();

                $this->_helper->flashMessenger(getGS('IP Address $1', getGS('saved')));
                $this->_helper->redirector('edit', 'subscriber', 'admin', array(
                    'user' => $subscriber->getId(),
                ));
            } catch (PDOException $e) {
                $form->getElement('ip')->addError(getGS('IP Address added allready'));
            }
        }

        $this->view->form = $form;
    }

    public function deleteAction()
    {
        $subscriber = $this->_helper->entity(new Subscriber, 'user');
        $repository = $this->_helper->entity->getRepository(new Ip);
        $ip = $this->_getParam('ip', '');

        $repository->delete($ip, $subscriber);
        $this->_helper->entity->flushManager();

        $this->_helper->flashMessenger(getGS('IP Address $1', getGS('deleted')));
        $this->_helper->redirector('edit', 'subscriber', 'admin', array(
            'user' => $subscriber->getId(),
        ));
    }

    public function createForm()
    {
        $form = new Zend_Form;

        $form->addElement('text', 'ip', array(
            'label' => getGS('Start IP'),
            'required' => true,
            'validators' => array(
                array('notEmpty', true, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => getGS("Value is required and can't be empty"),
                    ),
                )),
                array('ip', true, array(
                    'messages' => array(
                        Zend_Validate_Ip::NOT_IP_ADDRESS => getGS("'%value%' is not a valid IP Address"),
                    ),
                )),
            ),
        ));

        $form->addElement('text', 'number', array(
            'label' => getGS('Number of addresses'),
            'required' => true,
            'validators' => array(
                array('notEmpty', true, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => getGS("Value is required and can't be empty"),
                    ),
                )),
                array('greaterThan', true, array(
                    0,
                    'messages' => array(
                        Zend_Validate_GreaterThan::NOT_GREATER => getGS("'%value%' must be greater than '%min%'"),
                    ),
                )),
            ),
        ));

        $form->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
        ));

        return $form;
    }
}
