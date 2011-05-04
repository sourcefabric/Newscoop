<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

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
            'order' => 61, // $this->getEelement('phone')->getOrder() + 1;
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

        $this->getElement('groups')->setOrder(31); // $this->getElement('password_confirm')->getOrder() + 1
    }
}
