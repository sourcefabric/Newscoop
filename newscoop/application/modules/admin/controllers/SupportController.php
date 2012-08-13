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

    public function init()
    {
        camp_load_translation_strings('support');
    }

    public function indexAction()
    {
        $this->view->stats = $this->_helper->service('stat')->getAll();
        
        // saving them here to retrieve later, because these are not available when run in cli
        SystemPref::set('support_stats_server', $this->view->stats['server']);
        SystemPref::set('support_stats_ip_address', $this->view->stats['ipAddress']);
        SystemPref::set('support_stats_ram_total', $this->view->stats['ramTotal']);
        
        $values = $this->getRequest()->getPost();
        
        if (isset($values['support_send'])) {
            SystemPref::set('stat_ask_time', time());
            SystemPref::set('support_send', $values['support_send']);
            
            $this->_helper->flashMessenger(getGS('Support settings saved.'));
            
            if ($values['redirect'] == 1) {
                $this->_helper->redirector('index', '');
            }
        }
        
        $this->view->support_send = SystemPref::get('support_send');
        $this->view->redirect = 0;
    }
    
    public function popupAction()
    {
        $this->_helper->layout->setLayout('iframe');
        
        $this->view->stats = $this->_helper->service('stat')->getAll();
        
        // saving them here to retrieve later, because these are not available when run in cli
        SystemPref::set('support_stats_server', $this->view->stats['server']);
        SystemPref::set('support_stats_ip_address', $this->view->stats['ipAddress']);
        SystemPref::set('support_stats_ram_total', $this->view->stats['ramTotal']);
        
        $this->view->support_send = SystemPref::get('support_send');
        $this->view->redirect = 1;
        
        $this->render('index');
    }
    
    public function closeAction()
    {
        $this->_helper->layout->setLayout('iframe');
        
        $_SESSION['statDisplayed'] = 1;
    }
}

