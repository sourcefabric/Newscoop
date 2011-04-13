<?php

use Newscoop\Entity\User\Staff;

/**
 * User form
 */
class Admin_Form_Staff extends Admin_Form_User
{
    /** @var bool */
    private $isAdmin = FALSE;

    /**
     * @param bool $isAdmin
     */
    public function __construct($isAdmin = TRUE)
    {
        $this->isAdmin = (bool) $isAdmin;

        parent::__construct();
    }

    public function init()
    {
        parent::init();

        if (!$this->isAdmin) {
            return;
        }

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

        if (!$this->isAdmin) {
            return;
        }

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
