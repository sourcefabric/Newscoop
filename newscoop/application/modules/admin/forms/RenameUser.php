<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Admin_Form_RenameUser extends Zend_Form
{
    /**
     */
    public function init()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        
        $this->addElement('hash', 'csrf');

        $this->addElement('text', 'username', array(
            'label' => $translator->trans('Username', array(), 'users'),
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('stringLength', false, array(5, 80)),
            ),
        ));

        $this->addElement('submit', 'submit', array(
            'id' => 'save_button',
            'label' => $translator->trans('Save'),
            'ignore' => true,
        ));
    }
}
