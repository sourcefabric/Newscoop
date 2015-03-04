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
        $this->addElement('hidden', '_target_path', array(
            'value' => $_SERVER['REQUEST_URI'],
        ));

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

        $this->addElement('submit', 'submit', array(
            'label' => 'Sign In',
            'ignore' => true,
        ));
    }
}
