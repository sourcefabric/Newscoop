<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;

/**
 * @Acl(ignore="1")
 */
class Admin_SupportController extends Zend_Controller_Action
{
    public function init(){}

    public function indexAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
        $this->view->stats = $this->_helper->service('stat')->getAll();

        // saving them here to retrieve later, because these are not available when run in cli
        $preferencesService->set('support_stats_server', $this->view->stats['server']);
        $preferencesService->set('support_stats_ip_address', $this->view->stats['ipAddress']);
        $preferencesService->set('support_stats_ram_total', $this->view->stats['ramTotal']);

        if ($this->getRequest()->isPost() && $this->_getParam('support_send') !== null) {
            $values = $this->getRequest()->getPost();

            try {
                $askTime = new DateTime($values['stat_ask_time']);
            } catch (Exception $e) {
                $askTime = new DateTime('7 days');
            }

            $preferencesService->set('stat_ask_time', $askTime->getTimestamp());
            $preferencesService->set('support_send', $values['support_send']);
            $this->_helper->flashMessenger($translator->trans('Support settings saved.', array(), 'support'));
            if ($this->_getParam('action') === 'popup') {
                $this->_helper->redirector('index', '');
            } else {
                $this->_helper->redirector('index');
            }
        }

        $this->view->support_send = $preferencesService->get('support_send');
    }

    public function popupAction()
    {
        $this->_helper->layout->setLayout('iframe');
        $this->view->action = 'popup';
        $this->_forward('index');
    }

    public function closeAction()
    {
        $_SESSION['statDisplayed'] = 1;
        $this->_helper->json(array());
    }
}
