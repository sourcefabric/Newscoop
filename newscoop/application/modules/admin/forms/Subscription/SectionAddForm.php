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
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->addElement('select', 'language', array(
            'label' => $translator->trans('Language'),
            'multioptions' => array(
                'select' => $translator->trans('Individual languages'),
                'all' => $translator->trans('Regardless of the language'),
            ),
        ));

        $this->addElement('multiselect', 'sections_select', array(
            'label' => $translator->trans('Sections'),
            // multioptions from controller
        ));

        $this->addElement('multiselect', 'sections_all', array(
            'label' => $translator->trans('Sections'),
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

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Save'),
        ));
    }
}
