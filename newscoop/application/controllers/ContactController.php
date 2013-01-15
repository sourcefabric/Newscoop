<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Contact controller
 */
class ContactController extends Zend_Controller_Action
{
    public function init()
    {
        $this->getHelper('contextSwitch')->addActionContext('send', 'json')->initContext();
    }
    
    public function sendAction()
    {
        $this->_helper->layout->disableLayout();
        $container = \Zend_Registry::get('container');
        $mailer = $container->getService('email');
        $parameters = $this->getRequest()->getParams();
        $errors = array();

        if (empty($errors)) {
            $mailer->send($parameters['subject'], $parameters['message'], array('kontakt@zentralplus.ch'));
            $this->view->response = json_encode(array('status' => true));
        } else {
            $this->view->response = json_encode(array('errors' => $errors));
        }
    }
}
