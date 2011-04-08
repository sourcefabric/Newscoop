<?php

use Newscoop\Entity\User;

/**
 * User form
 */
class Admin_Form_CommentsUser extends Zend_Form
{
    public function init()
    {
        $this->addElement('hash', 'csrf', array('salt' => get_class($this)));

        $this->addElement('text', 'name', array(
            'label' => getGS('Name'),
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('stringLength', false, array(1, 128)),
            ),
            'errorMessages' => array(getGS('Value is not $1 characters long', '1-128')),
            'order' => 40,
        ));

        $this->addElement('text', 'email', array(
            'label' => getGS('E-mail'),
            'required' => true,
            'order' => 50,
        ));


        $this->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
            'order' => 99,
        ));
    }

    /**
     * Set default values by entity
     *
     * @param Newscoop\Entity\User $user
     * @return void
     */
    public function setDefaultsFromEntity(User $user)
    {
        $this->setDefaults(array(
            'username' => $user->getUsername(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'title' => $user->getTitle(),
            'gender' => $user->getGender(),
            'age' => $user->getAge(),
            'city' => $user->getCity(),
            'street_address' => $user->getStreetAddress(),
            'postal_code' => $user->getPostalCode(),
            'state' => $user->getState(),
            'country' => $user->getCountry(),
            'fax' => $user->getFax(),
            'contact_person' => $user->getContactPerson(),
            'phone_second' => $user->getPhoneSecond(),
            'employer' => $user->getEmployer(),
            'employer_type' => $user->getEmployerType(),
            'position' => $user->getPosition(),
        ));

        // can't change on edit
        $this->getElement('username')->setAttrib('readonly', TRUE);

        // make password change optional
        $this->getElement('password')->setRequired(false);
        $this->getElement('password_confirm')->setRequired(false);
        $this->addDisplayGroup(array(
            'password',
            'password_confirm',
        ), 'password_change', array(
            'legend' => getGS('Change password'),
            'class' => 'toggle',
            'order' => 62,
        ));
    }

}
