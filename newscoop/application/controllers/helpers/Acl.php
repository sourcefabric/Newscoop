<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;

/**
 * Acl action helper
 */
class Action_Helper_Acl extends Zend_Controller_Action_Helper_Abstract
{
    /** @var Newscoop\Entity\User */
    private $user = NULL;

    /**
     * Init
     *
     * @return Zend_Acl
     */
    public function init()
    {
        return $this;
    }

    /**
     * Get Acl
     *
     * @return Zend_Acl
     */
    public function getAcl(\Zend_Acl_Role_Interface $role)
    {
        $aclResource = Zend_Registry::get('acl');
        return $aclResource->getAcl($role);
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
        if ($user === NULL) {
            $user = $this->getCurrentUser();
        }

        if ($resource !== NULL) {
            $resource = strtolower($resource);
        }

        if ($action !== NULL) {
            $action = strtolower($action);
        }

        return $this->getAcl($user)->isAllowed($user, $resource, $action);
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
            $redirector = $this->getActionController()->getHelper('redirector');
            $redirector->gotoSimple('deny', 'error', 'admin', array(
                'message' => getGS('You are not allowed to $1 $2.',
                    $action ? $action : getGS('handle'),
                    $resource ? $resource : getGS('any resource')),
            ));
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
