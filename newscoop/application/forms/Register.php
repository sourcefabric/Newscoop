<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Application_Form_Register extends Zend_Form
{
    public function init()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->addElement('text', 'email', array(
            'label' => $translator->trans('Email', array(), 'users'),
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('emailAddress', true, array('domain' => APPLICATION_ENV !== 'development')),
            ),
        ));

        $this->addElement('checkbox', 'terms_of_use', array(
            'label' => $translator->trans('Accepting terms of use', array(), 'users'),
            'required' => true,
            'validators' => array(
                array('greaterThan', true, array('min' => 0)),
            ),
        ));


        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Continue', array(), 'users'),
            'ignore' => true,
        ));
    }
}
