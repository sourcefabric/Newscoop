<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */


/**
 */
class Admin_Form_UserPoints extends Zend_Form
{
    public function init()
    {
        //$this->addElement('hash', 'csrf');

        $this->addElement('text', 'test', array(
            'label' => getGS("Test Action"),
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                'int',
            )
        ));

        $this->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
            'ignore' => TRUE,
        ));

    }
}