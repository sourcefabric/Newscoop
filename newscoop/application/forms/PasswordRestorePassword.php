<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Application_Form_PasswordRestorePassword extends Zend_Form
{
    public function init()
    {
        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'required' => true,
            'filters' => array('stringTrim'),
            'validators' => array(
                array('stringLength', false, array(6, 80)),
            ),
        ));

        $this->addElement('password', 'password_confirm', array(
            'label' => 'Password Confirmation',
            'required' => true,
            'filters' => array('stringTrim'),
            'validators' => array(
                new Zend_Validate_Callback(function ($value, $context) {
                    return $value == $context['password'];
                }),
            ),
            'errorMessages' => array("Password confirmation does not match your password."),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Set password',
            'ignore' => true,
        ));
    }
}
