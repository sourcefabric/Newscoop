<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Application_Form_Confirm extends Zend_Form
{
    public function init()
    {
        $this->addElement('text', 'first_name', array(
            'label' => 'First Name',
            'required' => true,
            'filters' => array('stringTrim'),
        ));

        $this->addElement('text', 'last_name', array(
            'label' => 'Last Name',
            'required' => true,
            'filters' => array('stringTrim'),
        ));

        $this->addElement('text', 'username', array(
            'label' => 'Username',
            'required' => true,
            'filters' => array('stringTrim'),
        ));

        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'required' => true,
            'filters' => array('stringTrim'),
            'validators' => array(
                array('stringLength', false, array(6, 80)),
            ),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Save',
            'ignore' => true,
        ));
    }
}
