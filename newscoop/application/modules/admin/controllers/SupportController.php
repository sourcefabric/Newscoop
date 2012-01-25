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
        $this->view->stats = $this->_helper->service('stat')->getAll();
        
        // saving them here to retrieve later, because these are not evailable when run in cli
        SystemPref::set('support_stats_server', $this->view->stats['server']);
        SystemPref::set('support_stats_ip_address', $this->view->stats['ipAddress']);
        SystemPref::set('support_stats_ram_total', $this->view->stats['ramTotal']);
        
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            
            SystemPref::set('support_set', 1);
            
            SystemPref::set('support_send', $values['agree']);
            
            $this->_helper->flashMessenger(getGS('Support settings saved.'));
            $this->_helper->redirector('..');
        }
        else {
            $this->view->form = $this->getForm();
            $this->view->first = false;
            if (!SystemPref::get('support_set')) {
                $this->view->first = true;
            }
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
            'label' => getGS('Cancel'),
            'class' => 'submit',
            'style' => 'font-size: 10px;',
            'onClick' => 'disagree();'
        ));
        
        return $form;
    }
}

