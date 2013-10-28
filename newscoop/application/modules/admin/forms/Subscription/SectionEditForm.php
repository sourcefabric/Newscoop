<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

class Admin_Form_Subscription_SectionEditForm extends Zend_Form
{
    public function init()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->addElement('text', 'name', array(
            'label' => $translator->trans('Section'),
            'readonly' => true,
        ));

        $this->addElement('text', 'language', array(
            'label' => $translator->trans('Language'),
            'readonly' => true,
        ));

        $this->addElement('text', 'start_date', array(
            'label' => $translator->trans('Start'),
            'required' => true,
            'class' => 'date',
        ));

        $this->addElement('text', 'days', array(
            'label' => $translator->trans('Days'),
            'required' => true,
        ));

        $this->addElement('text', 'paid_days', array(
            'label' => $translator->trans('Paid Days'),
            'required' => true,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Save'),
        ));
    }
}
