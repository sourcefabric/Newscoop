<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Admin_Form_Ingest extends Zend_Form
{
    /**
     */
    public function init()
    {
        $this->addElement('hash', 'csrf');

        $this->addElement('select', 'type', array(
            'label' => getGS('Type'),
            'required' => true,
            'multioptions' => array(
                'reuters' => 'Thomson Reuters',
            ),
        ));

        $config = new Zend_Form_SubForm();

        $config->addElement('text', 'username', array(
            'label' => getGS('Username'),
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
        ));

        $config->addElement('text', 'password', array(
            'label' => getGS('Password'),
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
        ));

        $this->addSubForm($config, 'config');

        $this->addElement('submit', 'submit', array(
            'label' => getGS('Add'),
            'ignore' => true,
        ));
    }
}
