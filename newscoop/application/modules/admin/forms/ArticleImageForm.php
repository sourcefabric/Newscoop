<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Image\ArticleImage;

/**
 */
class Admin_Form_ArticleImageForm extends Zend_Form
{
    public function init()
    {
        $this->addElement('hash', 'csrf');

        $this->addArticleSubForm();
        $this->addImageSubForm();
    }

    /**
     * Add article specific subform
     *
     * @return void
     */
    private function addArticleSubForm()
    {
        $form = new Zend_Form_SubForm();
        $form->setLegend(getGS('Article specific'));

        $form->addElement('text', 'number', array(
            'label' => getGS('Number'),
            'required' => true,
            'filters' => array('int'),
        ));

        $form->addElement('text', 'caption', array(
            'label' => getGS('Caption'),
        ));

        $form->addElement('hidden', 'language', array(
            'filters' => array('int'),
        ));

        $this->addSubForm($form, 'article');
    }

    /**
     * Add global image subform
     *
     * @return void
     */
    private function addImageSubForm()
    {
        $form = new Zend_Form_SubForm();
        $form->setLegend(getGS('Global'));

        $form->addElement('select', 'status', array(
            'label' => getGS('Status'),
            'multioptions' => array(
                'approved' => getGS('Approved'),
                'unapproved' => getGS('Unapproved'),
            ),
        ));

        $form->addElement('text', 'description', array(
            'label' => getGS('Description'),
        ));

        $form->addElement('text', 'photographer', array(
            'label' => getGS('Photographer'),
        ));

        $form->addElement('text', 'photographer_url', array(
            'label' => getGS('Photographer URL'),
        ));

        $form->addElement('text', 'place', array(
            'label' => getGS('Place'),
        ));

        $form->addElement('text', 'date', array(
            'label' => getGS('Date'),
            'placeholder' => date('Y-m-d'),
        ));

        $this->addSubForm($form, 'image');
    }

    /**
     * Set default values from entity
     *
     * @param Newscoop\Image\ArticleImage $articleImage
     * @param int $languageId
     * @return void
     */
    public function setDefaultsFromEntity(ArticleImage $articleImage, $languageId)
    {
        $this->setDefaults(array(
            'article' => array(
                'number' => $articleImage->getNumber(),
                'caption' => $articleImage->getCaption($languageId),
            ),
            'image' => array(
                'status' => $articleImage->getImage()->getStatus(),
                'description' => $articleImage->getImage()->getDescription(),
                'photographer' => $articleImage->getImage()->getPhotographer(),
                'photographer_url' => $articleImage->getImage()->getPhotographerUrl(),
                'place' => $articleImage->getImage()->getPlace(),
                'date' => $articleImage->getImage()->getDate(),
            ),
        ));

        $this->article->caption->setAttrib('placeholder', $articleImage->getImage()->getDescription());
    }
}
