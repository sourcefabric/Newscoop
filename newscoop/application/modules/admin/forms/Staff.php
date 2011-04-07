<?php

use Newscoop\Entity\User\Staff;

/**
 * User form
 */
class Admin_Form_Staff extends Admin_Form_User
{
    public function init()
    {
        parent::init();

        $this->addElement('multiCheckbox', 'groups', array(
            'label' => getGS('User Type'),
            'order' => 61,
        ));
    }

    /**
     * Set staff groups
     *
     * @param Newscoop\Entity\User\Staff $staff
     * @return void
     */
    public function setDefaultsFromEntity(Staff $staff)
    {
        parent::setDefaultsFromEntity($staff);

        $groups = array();
        foreach ($staff->getGroups() as $group) {
            $groups[] = $group->getId();
        }

        $this->setDefaults(array(
            'groups' => $groups,
        ));

        $this->getElement('groups')->setOrder(31);
    }
}
