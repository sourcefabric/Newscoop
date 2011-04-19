<?php

use Newscoop\Entity\User;

/**
 * Acl action helper
 */
class Action_Helper_Acl extends Zend_Controller_Action_Helper_Abstract
{
    /** @var Zend_Acl */
    private $acl = NULL;

    /** @var Newscoop\Entity\User */
    private $user = NULL;

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
     * @return bool
     */
    public function isAllowed($resource, $action = NULL, $user = NULL)
    {
        $role = $user ? $user->getRoleId() : $this->getCurrentUser()->getRoleId();

        if ($resource !== NULL) {
            $resource = strtolower($resource);
        }

        if ($action !== NULL) {
            $action = strtolower($action);
        }

        return $this->getAcl()->isAllowed($role, $resource, $action);
    }

    /**
     * Check permissions and redirect to error screen if not allowed
     *
     * @params string|NULL $resource
     * @params string|NULL $action
     * @return void
     */
    public function check($resource, $action = NULL)
    {
        if (!$this->isAllowed($resource, $action)) {
            $this->getRequest()
                ->setControllerName('error')
                ->setActionName('deny')
                ->setDispatched(false)
                ->setParam('message', getGS('You are not allowed to $1 $2.',
                    $action ? $action : getGS('handle'),
                    $resource ? $resource : getGS('any resource')));
        }
    }

    /**
     * Set current user
     *
     * @param Newscoop\Entity\User $user
     * @return Action_Helper_Acl
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get current user
     *
     * @return Newscoop\Entity\User
     */
    private function getCurrentUser()
    {
        if ($this->user === NULL) {
            $this->setUser(Zend_Registry::get('user'));
        }

        return $this->user;
    }
}
