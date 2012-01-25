<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Admin_Form_Slideshow extends Zend_Form
{
    /**
     */
    public function init()
    {
        $this->addElement('text', 'headline', array(
            'label' => getGS('Headline'),
            'required' => true,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
            'ignore' => true,
        ));
    }
}
