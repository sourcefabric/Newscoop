<?php

use Newscoop\Entity\User;

/**
 * User form
 */
class Application_Form_EditUser extends Application_Form_BaseUser
{
    public function init()
    {
        $this->addElement('text', 'name', array(
            'required' => true,
            'label' => getGS('Name'),
        ));

        $this->addElement('text', 'email', array(
            'required' => true,
            'label' => getGS('E-mail'),
        ));

        $this->addElement('text', 'phone', array(
            'label' => getGS('Phone'),
        ));

        // password change
        $this->addElement('password', 'password', array(
            'label' => getGS('Password'),
        ));

        $this->addElement('password', 'password_confirm', array(
            'label' => getGS('Confirm password'),
        ));

        $this->addDisplayGroup(array(
            'password',
            'password_confirm',
        ), 'password_change', array(
            'legend' => getGS('Change password'),
            'class' => 'toggle',
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

        $roles = array();
        foreach ($user->getGroups() as $role) {
            $roles[] = $role->getId();
        }

        $this->setDefaults(array(
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'roles' => $roles,
        ));
    }
}
