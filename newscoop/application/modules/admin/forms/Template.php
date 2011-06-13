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
            'label' => getGS('Edit template'),
            'required' => TRUE,
        ));

        $this->addElement('text', 'cache_lifetime', array(
            'label' => getGS('Cache lifetime'),
            'class' => 'short',
        ));

        $this->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
            'ignore' => TRUE,
        ));
    }
}
