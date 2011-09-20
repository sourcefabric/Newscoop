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
        $this->addElement('text', 'email', array(
            'label' => 'Email*:',
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                'emailAddress',
            ),
        ));

        $this->addElement('textarea', 'terms_of_use_text', array(
            'label' => 'Terms of Use:',
            'ignore' => true,
            'columns' => 60,
            'rows' => 5,
            'value' => 'Terms of use sample',
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Continue',
            'ignore' => true,
        ));
    }
}
