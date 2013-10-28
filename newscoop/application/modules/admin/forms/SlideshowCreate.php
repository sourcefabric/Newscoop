<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Admin_Form_SlideshowCreate extends Zend_Form
{
    /**
     */
    public function init()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        
        $this->addElement('text', 'headline', array(
            'label' => $translator->trans('Headline', array(), 'article_images'),
            'required' => true,
        ));

        $this->addElement('select', 'rendition', array(
            'label' => $translator->trans('Slideshow rendition', array(), 'article_images'),
            'required' => true,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Create', array(), 'home'),
            'ignore' => true,
        ));
    }
}
