<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Form_Confirm extends Zend_Form
{
    public function init()
    {
        $this->setMethod('POST');

        $this->addElement('hidden', 'first_name');
        $this->addElement('hidden', 'last_name');

        $this->addElement('text', 'email', array(
            'label' => 'Email',
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                'emailAddress',
            ),
        ));

        $this->addElement('text', 'password_change', array(
            'label' => 'Password',
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('stringLength', false, array(6, 80)),
            ),
        ));

        $this->addElement('text', 'username', array(
            'label' => 'Username',
            'required' => true,
        ));

        $this->addElement('textarea', 'terms_of_services', array(
            'label' => 'Terms of service',
            'ignore' => true,
            'rows' => 8,
            'cols' => 60,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Register',
            'ignore' => true,
        ));
    }
}
