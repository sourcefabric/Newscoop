<?php

/**
 * Acl action helper
 */
class Action_Helper_Acl extends Zend_Controller_Action_Helper_Abstract
{
    /** @var Zend_Acl */
    private $acl = NULL;

    /**
     * Init
     *
     * @return Zend_Acl
     */
    public function init()
    {
        return $this->getAcl();
    }

    /**
     * Set Acl
     *
     * @param Zend_Acl $acl
     * @return Action_Helper_Acl
     */
    public function setAcl(Zend_Acl $acl)
    {
        $this->acl = $acl;
        return $this;
    }

    /**
     * Get Acl
     *
     * @return Zend_Acl
     */
    public function getAcl()
    {
        if ($this->acl === NULL) {
            $controller = $this->getActionController();
            $bootstrap = $controller->getInvokeArg('bootstrap');
            $this->setAcl($bootstrap->getResource('Acl')->getAcl());
        }

        return $this->acl;
    }

    /**
     * Permission check
     *
     * @params string|NULL $resource
     * @params string|NULL $action
     * @params Newscoop\Entity\User|NULL $user
     * @return void
     */
    public function isAllowed($resource, $action = NULL, $user = NULL)
    {
        $role = $user ? $user->getRole() : 'Administrator';
        return $this->getAcl()->isAllowed($role, $resource, $action);
    }
}
