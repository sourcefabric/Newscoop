<?php
/**
 * @package Resource
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

use Newscoop\Entity\User\Staff;

/**
 * Acl Zend application resource
 */
class Resource_Acl extends Zend_Application_Resource_ResourceAbstract
{
    /** @var Zend_Acl */
    private $acl;

    /**
     * Init acl
     */
    public function init()
    {
        Zend_Registry::set('acl', $this);
        return $this;
    }

    /**
     * Get acl for user
     *
     * @param object|null $user
     * @return Zend_Acl|null
     */
    public function getAcl(Staff $user = NULL)
    {
        $auth = Zend_Auth::getInstance();

        if ($this->acl !== NULL
            && ($user === NULL || $auth->getIdentity() == $user->getId())) { // current user
            return $this->acl;
        }

        $acl = new Zend_Acl;
        $options = $this->getOptions();

        if (!$auth->hasIdentity()) { // no rules for guests
            return NULL;
        }

        // get doctrine
        $bootstrap = $this->getBootstrap();
        $doctrine = $bootstrap->getResource('doctrine');
        $em = $doctrine->getEntityManager();
   
        // set resources
        $resourceRepository = $em->getRepository($options['resourceEntity']);
        foreach ($resourceRepository->findAll() as $resource) {
            $acl->addResource($resource);
        }

        if (empty($user)) { // get auth user
            $user = $em->find($options['userEntity'], $auth->getIdentity());
            if (!$user instanceof $options['userEntity']) { // ignore subscribers
                return;
            }
        } else {
            if (!$user instanceof $options['userEntity']) {
                throw new InvalidArgumentException;
            }
        }

        // get user groups roles
        $parent = NULL;
        $roles = array();
        foreach ($user->getGroups() as $group) {
            $roles[] = array(
                $group->getRole(),
                $parent,
            );
            $parent = $group->getRole();
        }

        // get user specific role
        $roles[] = array($user->getRole(), $parent);

        // set acl roles/rules
        foreach ($roles as $role) {
            list($role, $parent) = array_values($role);

            // add role
            $acl->addRole($role, $parent);

            // add rules
            foreach ($role->getRules() as $rule) {
                $type = $rule->getType();
                $resource = $rule->getResource() ?: NULL;
                $action = $rule->getAction() ? strtolower($rule->getAction()->getName()) : NULL;
                $acl->$type($role, $resource, $action);
            }
        }

        if ($user->getId() == $auth->getIdentity()) { // store for current user
            $this->acl = $acl;
        }

        return $acl;
    }
}
