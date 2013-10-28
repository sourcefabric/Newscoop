<?php
/**
 * Ban User form
 */
class Admin_Form_BanUser extends Zend_Form
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
    public function getDeleteMessages()
    {
        return $this->delete_messages;
    }

    /**
     * Getter for the name checkbox
     *
	 * @return Zend_Form_Element_Checkbox
     */
    public function getElementName()
    {
        return $this->name;
    }

    /**
     * Getter for the email checkbox
     *
	 * @return Zend_Form_Element_Checkbox
     */
    public function getElementEmail()
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
            'label' => $translator->trans($translator->trans('Email').":"),
            'required' => false,
            'order' => 20,
        ));

        $this->addElement('checkbox', 'delete_messages', array(
            'label' => $translator->trans('Delete feedback messages?', array(), 'feedback'),
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
     * @param $user
     * @param $values
     */
    public function setValues($p_user, $p_values)
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        /* @var $name Zend_Form_Element_CheckBox */
        $this->name->setLabel($translator->trans('Name').":".$p_user->getName())
                    ->setChecked($p_values['name']);

        $this->email->setLabel($translator->trans('Email').":".$p_user->getEmail())
                    ->setChecked($p_values['email']);
    }

}
