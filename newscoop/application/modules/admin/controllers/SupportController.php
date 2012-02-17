<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * @Acl(ignore="1")
 */
class Admin_SupportController extends Zend_Controller_Action
{

    public function init()
    {
        
    }

    public function indexAction()
    {
        $this->_helper->layout->setLayout('iframe');
        
        SystemPref::set('stat_ask_time', time());
        
        $this->view->stats = $this->_helper->service('stat')->getAll();
        
        // saving them here to retrieve later, because these are not available when run in cli
        SystemPref::set('support_stats_server', $this->view->stats['server']);
        SystemPref::set('support_stats_ip_address', $this->view->stats['ipAddress']);
        SystemPref::set('support_stats_ram_total', $this->view->stats['ramTotal']);
        
        $this->view->agree = SystemPref::get('support_send');
        
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            
            if ($values['agree']) {
                SystemPref::set('support_send', $values['agree']);
            }
            
            $this->_helper->flashMessenger(getGS('Support settings saved.'));
            echo("<script>parent.$.fancybox.close();</script>");
        }
        else {
            $this->view->form = $this->getForm();
        }
    }
    
    /**
     * Get priority form
     *
     * @return \Zend_Form
     */
    private function getForm()
    {
        $supportSend = (SystemPref::get('support_send')) ? SystemPref::get('support_send') : 0;
        
        $form = new Zend_Form;
        
        $form->addElement('hidden', 'agree', array('value' => 1));

        $form->addElement('submit', 'save', array(
            'label' => getGS('I agree to Sourcefabric\'s privacy policy and approve sending daily statistics'),
            'style' => 'font-size: 10px;',
            'onClick' => 'agree();'
        ));
        
        $form->addElement('submit', 'cancel', array(
            'label' => getGS('Remind me in 1 week'),
            'class' => 'submit',
            'style' => 'font-size: 10px;',
            'onClick' => 'disagree();'
        ));
        
        return $form;
    }
}

