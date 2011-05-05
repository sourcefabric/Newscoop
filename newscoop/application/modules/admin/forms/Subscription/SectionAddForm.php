<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

class Admin_Form_Subscription_SectionAddForm extends Zend_Form
{
    public function init()
    {
        $this->addElement('select', 'language', array(
            'label' => getGS('Language'),
            'multioptions' => array(
                'select' => getGS('Individual languages'),
                'all' => getGS('Regardless of the language'),
            ),
        ));

        $this->addElement('multiselect', 'sections_select', array(
            'label' => getGS('Sections'),
            // multioptions from controller
        ));

        $this->addElement('multiselect', 'sections_all', array(
            'label' => getGS('Sections'),
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

        $this->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
        ));
    }
}
