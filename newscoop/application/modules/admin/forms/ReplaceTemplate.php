<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Admin_Form_ReplaceTemplate extends Zend_Form
{
    public function init()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');

        $this->addElement('file', 'file', array(
            'required' => TRUE
        ));

        $this->addElement('reset', 'reset', array(
            'label' => $translator->trans('Cancel')
        ));

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Replace', array(), 'themes'),
            'ignore' => TRUE
        ));
    }
}
