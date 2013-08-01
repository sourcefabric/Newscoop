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
        camp_load_translation_strings('users');
        $this->addElement('text', 'email', array(
            'label' => getGS('Email'),
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('emailAddress', true, array('domain' => APPLICATION_ENV !== 'development')),
            ),
        ));

        $this->addElement('checkbox', 'terms_of_use', array(
            'label' => getGS('Accepting terms of use'),
            'required' => true,
            'validators' => array(
                array('greaterThan', true, array('min' => 0)),
            ),
        ));


        $this->addElement('submit', 'submit', array(
            'label' => getGS('Continue'),
            'ignore' => true,
        ));
    }
}
