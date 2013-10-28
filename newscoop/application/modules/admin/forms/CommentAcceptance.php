<?php
/**
 * Commenter form
 */
class Admin_Form_CommentAcceptance extends Zend_Form
{
    public function init()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');

        $this->addElement('text', 'forum', array(
            'label' => $translator->trans('Forum', array(), 'comments'),
            'required' => false,
            'order' => 0
        ));

        $type = new Zend_Form_Element_Select('type');
        $type->setLabel($translator->trans('Type'))
            ->setRequired(false)
            ->setOrder(10)
            ->setMultiOptions(array(
                "allow" => "Allow",
                "deny" => "Deny"
            ))
            ->addValidator('NotEmpty',true)
            ->setValue('deny');
        $this->addElement($type);

        $for_column = new Zend_Form_Element_Select('for_column');
        $for_column->setLabel($translator->trans('Column', array(), 'comments'))
            ->setRequired(false)
            ->setOrder(20)
            ->setMultiOptions(array(
                "name" => "Name",
                "email" => "Email",
                "ip"    => "Ip"
            ))
            ->addValidator('NotEmpty',true)
            ->setValue('email');
        $this->addElement($for_column);

        $this->addElement('text', 'search', array(
            'label' => $translator->trans('Search'),
            'required' => false,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('stringLength', false, array(1, 255)),
            ),
            'errorMessages' => array($translator->trans('Value is not $1 characters long', array('$1' => '1-255'), 'comments')),
            'order' => 30
        ));

        $searchType = new Zend_Form_Element_Select('search_type');
        $searchType->setLabel($translator->trans('Search Type', array(), 'comments'))
            ->setRequired(false)
            ->setOrder(40)
            ->setMultiOptions(array(
                "normal" => "normal",
                "regex" => "regex"
            ));
        $this->addElement($searchType);

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Save'),
            'order' => 99,
        ));

    }

    /**
     * Set default values by entity
     *
     * @param $acceptance
     * @return void
     */
    public function setFromEntity($acceptance)
    {
        $this->setDefaults(array(
            'forum' => $acceptance->getForum()->getId(),
            'type' => $acceptance->getType(),
            'for_column' => $acceptance->getForColumn(),
            'search'   => $acceptance->getSearch(),
            'search_type'   => $acceptance->getSearchType(),
        ));

    }

}
