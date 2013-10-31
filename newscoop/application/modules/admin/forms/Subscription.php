<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Subscription form
 */
class Admin_Form_Subscription extends Zend_Form
{
    public function init()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');

        $this->addElement('select', 'publication', array(
            'label' => $translator->trans('Publication'),
            'required' => true,
        ));

        $this->addElement('select', 'language_set', array(
            'label' => $translator->trans('Language'),
            'multioptions' => array(
                'select' => $translator->trans('Individual languages', array(), 'user_subscriptions'),
                'all' => $translator->trans('Regardless of the language', array(), 'user_subscriptions'),
            ),
        ));

        $this->addElement('multiselect', 'languages', array(
            'required' => isset($_POST['language_set']) && $_POST['language_set'] == 'select', // check only if language_set == select
            'validators' => array(
                array(new Zend_Validate_Callback(function($value, $context) {
                    return $context['language_set'] == 'all' || !empty($value);
                }), true),
            ),
        ));

        $this->getElement('languages')->setAutoInsertNotEmptyValidator(false);

        $this->addElement('select', 'sections', array(
            'label' => $translator->trans('Sections'),
            'multioptions' => array(
                'Y' => $translator->trans('Add sections now', array(), 'user_subscriptions'),
                'N' => $translator->trans('Add sections later', array(), 'user_subscriptions'),
            ),
        ));

        $this->addElement('text', 'start_date', array(
            'label' => $translator->trans('Start', array(), 'user_subscriptions'),
            'required' => true,
            'class' => 'date',
        ));

        $this->addElement('select', 'type', array(
            'label' => $translator->trans('Subscription Type', array(), 'user_subscriptions'),
            'multioptions' => array(
                'PN' => $translator->trans('Paid (confirm payment now)', array(), 'user_subscriptions'),
                'PL' => $translator->trans('Paid (payment will be confirmed later)', array(), 'user_subscriptions'),
                'T' => $translator->trans('Trial'),
            ),
        ));

        $this->addElement('text', 'days', array(
            'label' => $translator->trans('Days', array(), 'user_subscriptions'),
            'required' => true,
            'validators' => array(
                array('greaterThan', false, array(0)),
            ),
        ));

        $this->addElement('checkbox', 'active', array(
            'label' => $translator->trans('Active'),
            'value' => 1,
        ));
    
        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Add'),
        ));
    }
}

