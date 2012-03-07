<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Template;

/**
 */
class Admin_Form_Template extends Zend_Form
{
    public function init()
    {
        $this->addElement('hash', 'csrf');

        $this->addElement('textarea', 'content', array(
            'required' => TRUE,
        ));

        $this->addElement('text', 'cache_lifetime', array(
            'class' => 'short'
        ));

        $this->addElement('button', 'geo_filtering', array(
            'label' => '<span class="ui-icon-polygon"></span>' . getGS('Geo Filtering'),
            'class' => 'geo_filtering_button',
            'ignore' => TRUE,
            'escape' => false,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
            'ignore' => TRUE,
        ));
    }
}
