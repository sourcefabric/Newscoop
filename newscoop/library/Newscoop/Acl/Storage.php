<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Acl;

use Resource\Acl\StorageInterface,
    Newscoop\Utils\PermissionToAcl;

/**
 * Acl storage
 */
class Storage implements StorageInterface
{
    /** @var Resource_Doctrine */
    private $doctrine;

    /**
     * @var Resource_Doctrine $doctrine
     */
    public function __construct(\Resource_Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Get rules for role
     *
     * @param Zend_Acl_Role_Interface $role
     * @return array
     */
    public function getRules(\Zend_Acl_Role_Interface $role)
    {
        $em = $this->doctrine->getEntityManager();
        $repository = $em->getRepository('Newscoop\Entity\Acl\Rule');
        return (array) $repository->findBy(array(
            'role' => $role->getRoleId(),
        ));
    }

    /**
     * Get dynamic resources
     *
     * @return array
     */
    public function getResources()
    {
        $em = $this->doctrine->getEntityManager();
        $repository = $em->getRepository('Newscoop\Entity\Acl\Permission');

        $resources = array();
        foreach ($repository->findAll() as $permission) {
            list($resource, $action) = PermissionToAcl::translate($permission);
            if (!isset($resources[$resource])) {
                $resources[$resource] = array();
            }
            $resources[$resource][] = $action;
        }

        return $resources;
    }
}
