<?php

use Newscoop\Entity\User;

/**
 * User form
 */
class Admin_Form_AddUser extends Admin_Form_BaseUser
{
    public function init()
    {
        $this->addElement('text', 'username', array(
            'required' => true,
            'label' => getGS('Account name'),
        ));

        $this->addElement('password', 'password', array(
            'required' => true,
            'label' => getGS('Password'),
        ));

        $this->addElement('password', 'password_confirm', array(
            'required' => true,
            'label' => getGS('Confirm password'),
        ));

        $this->addElement('text', 'name', array(
            'required' => true,
            'label' => getGS('Name'),
        ));

        $this->addElement('text', 'email', array(
            'label' => getGS('E-mail'),
        ));

        $this->addElement('text', 'phone', array(
            'label' => getGS('Phone'),
        ));

        $this->addElement('multiCheckbox', 'roles', array(
            'label' => getGS('User Type'),
        ));

        $this->addUserDetails();

        $this->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
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
        parent::setDefaultsFromEntity($user);

        $this->setDefaults(array(
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
        ));
    }
}
