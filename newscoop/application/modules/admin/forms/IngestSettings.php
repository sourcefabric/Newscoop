<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Admin_Form_IngestSettings extends Zend_Form
{
    /**
     */
    public function init()
    {
        $this->addElement('hash', 'csrf');

        $this->addElement('text', 'article_type', array(
            'label' => getGS('Article Type'),
            'required' => true,
        ));

        $this->addElement('select', 'publication', array(
            'label' => getGS('Publication'),
            'multioptions' => array(
                null => getGS('None'),
            ),
        ));

        $this->addElement('select', 'section', array(
            'label' => getGS('Section'),
            'multioptions' => array(
                null => getGS('None'),
            ),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
            'ignore' => true,
        ));
    }
}
