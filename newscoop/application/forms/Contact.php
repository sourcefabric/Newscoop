<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Application_Form_Contact extends Zend_Form
{
    public function init()
    {
        $this->addElement('text', 'first_name', array(
            'label' => 'First Name',
            'filters' => array('stringTrim'),
        ));

        $this->addElement('text', 'last_name', array(
            'label' => 'Last Name',
            'filters' => array('stringTrim'),
        ));

        $this->addElement('text', 'email', array(
            'label' => 'Email',
            'required' => true,
            'filters' => array('stringTrim'),
        ));

        $this->addElement('select', 'subject', array(
            'label' => 'Subject',
            'multiOptions' => array(
                'test',
            ),
        ));

        $this->addElement('textarea', 'message', array(
            'label' => 'Message',
            'required' => true,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Send',
            'ignore' => true,
        ));
    }
}
