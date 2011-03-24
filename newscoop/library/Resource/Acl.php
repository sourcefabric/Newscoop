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
    private $acl = NULL;

    /**
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
            $this->acl->addResource(new Zend_Acl_Resource($resource->getName()));
        }

        // get roles
        $userRole = $em->find($options['roleEntity'], 1); // TODO add real user role id
        $roles = array(
            $userRole->getParent(),
            $userRole,
        );

        foreach ($roles as $role) {
            if ($role == NULL) {
                continue;
            }

            // add role
            $this->acl->addRole(new Zend_Acl_Role($role->getName(), $role->getParent() ? $role->getParent()->getName() : NULL));

            // add rules
            foreach ($role->getRules() as $rule) {
                $type = $rule->getType();
                $resource = $rule->getResource() ? $rule->getResource()->getName() : NULL;
                $action = $rule->getAction() ? $rule->getAction()->getName() : NULL;
                $this->acl->$type($role->getName(), $resource, $action);
            }
        }

        Zend_Registry::set('acl', $this);
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
