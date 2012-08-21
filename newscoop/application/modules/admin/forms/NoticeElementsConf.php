<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Admin_Form_NoticeElementsConf extends Zend_Form
{
    /**
     */
    public function init($confOptions)
    {
        $notice_elements = new Zend_Form_Element_MultiCheckbox('notice_elements');
        $notice_elements->setLabel(getGS('Hidden Elements'));
        $notice_elements->setDescription(getGS('check the checkbox next to the element that should be hidden in the notices edit screen '));

        foreach($confOptions as $name => $option){
            $notice_elements->addMultiOption($name,$option);
        }

        $this->addElement($notice_elements);

        $this->addElement('submit', 'submit', array(
            'label' => getGS('save'),
        ));

    }
}
