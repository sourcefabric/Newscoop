<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Admin_Form_Image extends Zend_Form
{
    public function init()
    {
        $this->setAttrib('enctype', 'multipart/form-data');

        $this->addElement('file', 'image', array(
            'label' => 'Image',
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Upload',
            'ignore' => true,
        ));
    }
}
