<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

/**
 * Acl Zend application resource
 */
class Resource_Acl extends Zend_Application_Resource_ResourceAbstract
{
    /** @var Zend_Acl */
    private $acl;

    /**
     * Init
     */
    public function init()
    {
        $this->acl = $this->getAcl();

        return $this;
    }

    /**
     * Get acl for user
     *
     * @param object|null $user
     * @return Zend_Acl|null
     */
    public function getAcl($user = NULL)
    {
        if ($user === NULL && $this->acl !== NULL) {
            return $this->acl;
        }

        $acl = new Zend_Acl;
        $auth = Zend_Auth::getInstance();
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
            Zend_Registry::set('acl', $acl);
            $this->acl = $acl;
        }

        return $acl;
    }
}
