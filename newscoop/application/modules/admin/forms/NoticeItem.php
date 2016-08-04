<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Admin_Form_NoticeItem extends Zend_Form
{
    /**
     */
    public function init()
    {
        $this->addElement('hidden', 'id');

        $this->addElement('hidden', 'categories', array(
            'label' => getGS('Categories'),
        ));

        $parent = new Zend_Form_Element_Select('priority');
        $parent->setLabel(getGS('Priority'));
        $parent->addMultiOptions(array(0 => 'Set priority',1 => '1', 2 => '2',3 => '3',4 => '4',5 => '5'));

        $this->addElement($parent);

        $this->addElement('text', 'title', array(
            'label' => getGS('Title'),
        ));

        $this->addElement('text', 'sub_title', array(
            'label' => getGS('Sub title'),
        ));

        $this->addElement('text', 'firstname', array(
            'label' => getGS('First name'),
        ));

        $this->addElement('text', 'lastname', array(
            'label' => getGS('Last name'),
        ));

        $this->addElement('textarea', 'body', array(
            'label' => getGS('CONTENT'),
        ));

        $this->addElement('text', 'date', array(
            'label' => getGS('Date'),
            'required' => true
        ));

        $this->addElement('text', 'published', array(
            'label' => getGS('Date of publication'),
            'required' => true
        ));

        $this->addElement('text', 'unpublished', array(
            'label' => getGS('Date of depublication'),
            'required' => true
        ));

        $this->addElement('submit', 'submit', array(
            'label' => getGS('save'),
        ));

        $this->addElement('submit', 'submit_next', array(
            'label' => getGS('Save and next'),
        ));


    }

    /**
     * Set default for given entity
     *
     * @param Newscoop\Package\Item $item
     * @return Admin_Form_SlideshowItem
     */
    public function setDefaultsFromEntity(\Newscoop\Entity\Notice $notice)
    {
        $categories = $notice->getCategories();
        $cats = array();
        if($categories){
            foreach($categories as $cat){
                $cats[] = $cat->getId();
            }
        }

        $this->setDefaults(array(
            'id' => $notice->getId(),
            'categories' => implode($cats,','),
            'title' => $notice->getTitle(),
            'sub_title' => $notice->getSubTitle(),
            'firstname' => $notice->getFirstname(),
            'lastname' => $notice->getLastname(),
            'date' => $notice->getDate()->format('Y-m-d'),
            'published' => $notice->getPublished()->format('Y-m-d'),
            'unpublished' => $notice->getUnpublished()->format('Y-m-d'),
            'body' => $notice->getBody()
        ));
        return $this;
    }


    public function disableFields(array $fields){

        foreach($fields as $field){
            $field = $this->getElement($field);
            $field->removeDecorator('HtmlTag');
            $field->removeDecorator('Label');
            $field->removeDecorator('ViewHelper');
            $field->setRequired(false);
        }
    }
}
