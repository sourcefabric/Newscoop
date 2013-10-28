<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Admin_Form_Blog extends Zend_Form
{
    /**
     */
    public function init()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->addElement('hash', 'csrf');

        $this->addElement('text', 'title', array(
            'label' => $translator->trans('Title', array(), 'api'),
            'required' => TRUE,
            'filters' => array(
                'stringTrim',
            ),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Create', array(), 'home'),
            'ignore' => true,
        ));
    }
}
