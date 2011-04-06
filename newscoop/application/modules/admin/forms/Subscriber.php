<?php

use Newscoop\Entity\User\Subscriber;

/**
 * User form
 */
class Admin_Form_Subscriber extends Admin_Form_User
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
            'required' => true,
            'label' => getGS('E-mail'),
        ));

        $this->addElement('text', 'phone', array(
            'label' => getGS('Phone'),
        ));

        $this->addElement('multiCheckbox', 'groups', array(
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
     * @param Newscoop\Entity\User\Subscriber $staff
     * @return void
     */
    public function setDefaultsFromEntity(Subscriber $user)
    {
        parent::setDefaultsFromEntity($user);

        $groups = array();
        foreach ($user->getGroups() as $group) {
            $groups[] = $group->getId();
        }

        $this->setDefaults(array(
            'username' => $user->getUsername(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'groups' => $groups,
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
            'order' => 5,
        ));

    }
}
