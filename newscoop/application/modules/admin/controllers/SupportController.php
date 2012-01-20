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
        
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            
            SystemPref::set('support_set', 1);
            
            SystemPref::set('support_send', $values['send_support_feedback']);
            SystemPref::set('support_promote', $values['promote']);
            if ($values['promote'] == '1') {
                SystemPref::set('support_promote_name', $values['promote_name']);
                SystemPref::set('support_promote_description', $values['promote_description']);
                SystemPref::set('support_promote_country', $values['promote_country']);
                SystemPref::set('support_promote_city', $values['promote_city']);
                SystemPref::set('support_promote_phone', $values['promote_phone']);
                SystemPref::set('support_promote_email', $values['promote_email']);
            }
            
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
        /*
        $supportPromote = (SystemPref::get('support_promote')) ? SystemPref::get('support_promote') : 0;
        $supportPromoteName = (SystemPref::get('support_promote_name')) ? SystemPref::get('support_promote_name') : '';
        $supportPromoteDescription = (SystemPref::get('support_promote_description')) ? SystemPref::get('support_promote_description') : '';
        $supportPromoteCountry = (SystemPref::get('support_promote_country')) ? SystemPref::get('support_promote_country') : '';
        $supportPromoteCity = (SystemPref::get('support_promote_city')) ? SystemPref::get('support_promote_city') : '';
        $supportPromotePhone = (SystemPref::get('support_promote_phone')) ? SystemPref::get('support_promote_phone') : '';
        $supportPromoteEmail = (SystemPref::get('support_promote_email')) ? SystemPref::get('support_promote_email') : '';
        */
        
        $form = new Zend_Form;

        $form->addElement('checkbox', 'send_support_feedback', array(
            //'onChange' => 'fixPromote();fixSubmit();',
            'onChange' => 'fixSubmit();',
            'value' => $supportSend
        ));
        /*
        $form->addElement('checkbox', 'promote', array(
            'onChange' => 'fixPromoteDetails();',
            'value' => $supportPromote
        ));
        
        $form->addElement('text', 'promote_name', array(
            'label' => getGS('Website name'),
            'value' => $supportPromoteName
        ));
        $form->addElement('textarea', 'promote_description', array(
            'label' => getGS('Description'),
            'class' => 'textAreaFix',
            'value' => $supportPromoteDescription
        ));
        $form->addElement('text', 'promote_country', array(
            'label' => getGS('Country'),
            'value' => $supportPromoteCountry
        ));
        $form->addElement('text', 'promote_city', array(
            'label' => getGS('City'),
            'value' => $supportPromoteCity
        ));
        $form->addElement('text', 'promote_phone', array(
            'label' => getGS('Phone').' ('.getGS('Will not be published. For verification purposes only').')',
            'value' => $supportPromotePhone
        ));
        $form->addElement('text', 'promote_email', array(
            'label' => getGS('E-mail').' ('.getGS('Will not be published. For verification purposes only').')',
            'value' => $supportPromoteEmail
        ));
        */
        $form->addElement('checkbox', 'agree_policy', array(
            'onChange' => 'fixSubmit();',
            'value' => 0
        ));
        
        $form->addElement('submit', 'save', array(
            'label' => getGS('Save')
        ));
        
        return $form;
    }
}

