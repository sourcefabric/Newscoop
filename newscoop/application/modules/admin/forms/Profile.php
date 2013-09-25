<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Admin_Form_Profile extends Zend_Form
{
    public function init()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');

        $this->setAttrib('enctype', 'multipart/form-data');

        $this->addElement('file', 'image', array(
            'label' => $translator->trans('Picture', array(), 'users'),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Save',
            'ignore' => true,
            'order' => 99,
        ));
    }
}
