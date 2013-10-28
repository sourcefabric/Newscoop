<?php
/**
 * Ban Commenter form
 */
class Admin_Form_Ban extends Zend_Form
{

    /**
     * Getter for the submit button
     *
	 * @return Zend_Form_Element_Submit
     */
    public function getSubmit()
    {
        return $this->submit;
    }

    /**
     * Getter for the delete comments
     *
	 * @return Zend_Form_Element_Checkbox
     */
    public function getDeleteComments()
    {
        return $this->delete_comments;
    }

    /**
     * Getter for the ip checkbox
     *
	 * @return Zend_Form_Element_Checkbox
     */
    public function getElementIp()
    {
        return $this->ip;
    }

    /**
     * Getter for the name checkbox
     *
	 * @return Zend_Form_Element_Checkbox
     */
    public function gettElementName()
    {
        return $this->name;
    }

    /**
     * Getter for the email checkbox
     *
	 * @return Zend_Form_Element_Checkbox
     */
    public function gettElementEmail()
    {
        return $this->email;
    }


    public function init()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');

        $this->addElement('checkbox', 'name', array(
            'label' => $translator->trans($translator->trans('Name').":"),
            'required' => false,
            'order' => 10,
        ));

        $this->addElement('checkbox', 'email', array(
            'label' => $translator->trans($translator->trans('Email', array(), 'comments').":"),
            'required' => false,
            'order' => 20,
        ));

        $this->addElement('checkbox', 'ip', array(
            'label' => $translator->trans($translator->trans('Ip', array(), 'comments').":"),
            'required' => false,
            'order' => 30,
        ));


        $this->addElement('checkbox', 'delete_comments', array(
            'label' => $translator->trans($translator->trans('Delete all comments?', array(), 'comments').":"),
            'required' => false,
            'order' => 40,
        ));

        $this->addElement('submit', 'cancel', array(
            'label' => $translator->trans('Cancel'),
            'order' => 98,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Save'),
            'order' => 99,
        ));
    }

    /**
     * Set values
     *
     * @param $commenter
     * @param $values
     */
    public function setValues($p_commenter, $p_values)
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        /* @var $name Zend_Form_Element_CheckBox */
        $this->name->setLabel($translator->trans('Name').":".strip_tags($p_commenter->getName()))
                    ->setChecked($p_values['name']);

        $this->email->setLabel($translator->trans('Email', array(), 'comments').":".$p_commenter->getEmail())
                    ->setChecked($p_values['email']);

        $this->ip->setLabel($translator->trans('Ip').":".$p_commenter->getIp())
                 ->setChecked($p_values['ip']);
    }

}
