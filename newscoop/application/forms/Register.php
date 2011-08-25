<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Form_Register extends Zend_Form
{
    public function init()
    {
        $this->setMethod('POST');

        $this->addElement('text', 'first_name', array(
            'label' => 'First Name',
            'filters' => array(
                'stringTrim',
            ),
        ));

        $this->addElement('text', 'last_name', array(
            'label' => 'Last Name',
            'filters' => array(
                'stringTrim',
            ),
        ));

        $this->addElement('text', 'email', array(
            'label' => 'E-mail Address',
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                'emailAddress',
            ),
        ));

        $this->addElement('text', 'password', array(
            'label' => 'Password',
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('stringLength', false, array(6, 80)),
            ),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Sign Up',
            'ignore' => true,
        ));
    }
}
