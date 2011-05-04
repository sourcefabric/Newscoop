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
        $this->addElement('text', 'name', array(
            'label' => getGS('Section'),
            'readonly' => true,
        ));

        $this->addElement('text', 'language', array(
            'label' => getGS('Language'),
            'readonly' => true,
        ));

        $this->addElement('text', 'start_date', array(
            'label' => getGS('Start'),
            'required' => true,
            'class' => 'date',
        ));

        $this->addElement('text', 'days', array(
            'label' => getGS('Days'),
            'required' => true,
        ));

        $this->addElement('text', 'paid_days', array(
            'label' => getGS('Paid Days'),
            'required' => true,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
        ));
    }
}
