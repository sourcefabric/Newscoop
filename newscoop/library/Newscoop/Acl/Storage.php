<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Acl;

use Resource\Acl\StorageInterface;
use Newscoop\Utils\PermissionToAcl;

/**
 * Acl storage
 */
class Storage implements StorageInterface
{
    /** @var Newscoop\Doctrine\Registry */
    private $doctrine;

    /**
     * @var Newscoop\Doctrine\Registry $doctrine
     */
    public function __construct($doctrine)
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
        $em = $this->doctrine->getManager();
        $repository = $em->getRepository('Newscoop\Entity\Acl\Rule');
        $criteria = array(
            'role' => $role->getRoleId(),
        );

        if (is_a($role, '\Newscoop\Entity\User\Group')) { // @fix WOBS-568: ignore deny rules for roles
            $criteria['type'] = 'allow';
        }

        return $repository->findBy($criteria);
    }

    /**
     * Get dynamic resources
     *
     * @return array
     */
    public function getResources()
    {
        $em = $this->doctrine->getManager();
        $repository = $em->getRepository('Newscoop\Entity\Acl\Permission');

        $resources = array();
        foreach ($repository->findAll() as $permission) {
            try {
                list($resource, $action) = PermissionToAcl::translate($permission);
            } catch (\InvalidArgumentException $e) { // ignore obsolete permissions
                continue;
            }

            if (!isset($resources[$resource])) {
                $resources[$resource] = array();
            }
            $resources[$resource][] = $action;
        }

        return $resources;
    }
}
