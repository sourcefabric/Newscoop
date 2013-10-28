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
        $translator = \Zend_Registry::get('container')->getService('translator');

        $this->addElement('text', 'headline', array(
            'label' => $translator->trans('Headline', array(), 'article_images'),
            'required' => true,
        ));

        $this->addElement('text', 'slug', array(
            'label' => $translator->trans('Slug', array(), 'article_images'),
        ));

        $this->addElement('text', 'description', array(
            'label' => $translator->trans('Description'),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Save'),
            'ignore' => true,
        ));
    }

    /**
     * Set defaults by given entity
     *
     * @param Newscoop\Package\Package $package
     * @return Admin_Form_Slideshow
     */
    public function setDefaultsFromEntity(\Newscoop\Package\Package $package)
    {
        $this->setDefaults(array(
            'headline' => $package->getHeadline(),
            'slug' => $package->getSlug(),
        ));

        return $this;
    }
}
