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
    private $entities = array();

    public function __construct($point_entities)
    {
        $this->entities = $point_entities;
        parent::__construct();
    }

    public function init()
    {
        //$this->addElement('hash', 'csrf');
        $translator = \Zend_Registry::get('container')->getService('translator');

        foreach ($this->entities as $entry) {

            $this->addElement('text', $entry->getAction(), array(
                'label' => $entry->getName(),
                'value' => $entry->getPoints(),
                'filters' => array(
                    'stringTrim',
                ),
                'validators' => array(
                    'int',
                )
            ));
        }

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Save'),
            'ignore' => TRUE,
        ));

    }
}