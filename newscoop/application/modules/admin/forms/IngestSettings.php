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
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->addElement('hash', 'csrf');

        $this->addElement('text', 'article_type', array(
            'label' => $translator->trans('Article Type'),
            'required' => true,
        ));

        $this->addElement('select', 'publication', array(
            'label' => $translator->trans('Publication'),
            'multioptions' => array(
                null => $translator->trans('None', array(), 'comments'),
            ),
        ));

        $this->addElement('select', 'section', array(
            'label' => $translator->trans('Section'),
            'multioptions' => array(
                null => $translator->trans('None', array(), 'comments'),
            ),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Save'),
            'ignore' => true,
        ));
    }
}
