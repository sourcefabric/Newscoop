<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Application_Form_Login extends Zend_Form
{
    public function init()
    {
        $this->addElement('text', 'email', array(
            'label' => 'Email',
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
        ));

        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
        ));

        $this->addElement('checkbox', 'remember_me', array(
            'label' => 'Remember Me',
            'required' => false,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Sign In',
            'ignore' => true,
        ));
    }
}
