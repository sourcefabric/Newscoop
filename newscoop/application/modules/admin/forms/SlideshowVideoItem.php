<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Admin_Form_SlideshowVideoItem extends Zend_Form
{
    /**
     */
    public function init()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        
        $this->addElement('text', 'url', array(
            'label' => $translator->trans('URL'),
            'required' => true,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Add video', array(), 'article_images'),
            'ignore' => true,
        ));
    }
}
