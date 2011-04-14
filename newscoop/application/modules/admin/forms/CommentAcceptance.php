<?php
/**
 * Commenter form
 */
class Admin_Form_CommentAcceptance extends Zend_Form
{
    public function init()
    {

        $this->addElement('text', 'search', array(
            'label' => getGS('Search'),
            'required' => false,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('stringLength', false, array(1, 128)),
            ),
            'errorMessages' => array(getGS('Value is not $1 characters long', '1-128')),
            'order' => 10
        ));


        $this->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
            'order' => 99,
        ));
    }

    /**
     * Set default values by entity
     *
     * @param $commenter
     * @return void
     */
    public function setFromEntity($commenter)
    {
        $this->setDefaults(array(
            'user' => $commenter->getUserId(),
            'name' => $commenter->getName(),
            'email' => $commenter->getEmail(),
            'url'   => $commenter->getUrl()
        ));

    }

}
