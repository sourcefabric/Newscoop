<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Admin_Form_Template extends Zend_Form
{
    public function init()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');

        $this->addElement('hash', 'csrf');

        $this->addElement('textarea', 'content', array(
            'required' => TRUE,
        ));

        $this->addElement('text', 'cache_lifetime', array(
            'class' => 'short'
        ));

        $this->addElement('button', 'geo_filtering', array(
            'label' => '<span class="ui-icon-polygon"></span>' . $translator->trans('Geo Filtering', array(), 'themes'),
            'class' => 'geo_filtering_button',
            'ignore' => TRUE,
            'escape' => false,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Save'),
            'ignore' => TRUE,
        ));
    }
}
