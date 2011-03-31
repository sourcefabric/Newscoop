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
    private $acl = NULL; /**
     * Init
     */
    public function init()
    {
        $this->acl = new Zend_Acl();
        $options = $this->getOptions();

        // get doctrine
        $bootstrap = $this->getBootstrap();
        $doctrine = $bootstrap->getResource('doctrine');
        $em = $doctrine->getEntityManager();
   
        // set resources
        $resourceRepository = $em->getRepository($options['resourceEntity']);
        foreach ($resourceRepository->findAll() as $resource) {
            $this->acl->addResource(new Zend_Acl_Resource(strtolower($resource->getName())));
        }

        // get user roles
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) { // no rules for guests
            return $this;
        }

        $user = $em->find('Newscoop\Entity\User', $auth->getIdentity());

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
            $this->acl->addRole(new Zend_Acl_Role($role->getId()), $parent ? $parent->getId() : NULL);

            // add rules
            foreach ($role->getRules() as $rule) {
                $type = $rule->getType();
                $resource = $rule->getResource() ? strtolower($rule->getResource()->getName()) : NULL;
                $action = $rule->getAction() ? strtolower($rule->getAction()->getName()) : NULL;
                $this->acl->$type((string) $role->getId(), $resource, $action);
            }
        }

        Zend_Registry::set('acl', $this->acl);
        return $this;
    }

    /**
     * Get Acl
     *
     * @return Zend_Acl
     */
    public function getAcl()
    {
        return $this->acl;
    }
}
