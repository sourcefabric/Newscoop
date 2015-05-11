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
    protected $doctrine;

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
        $queryBuilder = $em->getRepository('Newscoop\Entity\Acl\Rule')
            ->createQueryBuilder('r')
            ->select('r.resource, r.action, r.type')
            ->where('r.role = :role')
            ->setParameter('role', $role->getRoleId());

        if (is_a($role, '\Newscoop\Entity\User\Group')) { // @fix WOBS-568: ignore deny rules for roles
            $queryBuilder->andWhere('r.type = :allow')
                ->setParameter('allow', 'allow');
        }

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * Get dynamic resources
     *
     * @return array
     */
    public function getResources()
    {
        $em = $this->doctrine->getManager();
        $permissions = $em->getRepository('Newscoop\Entity\Acl\Permission')
            ->createQueryBuilder('p')
            ->select('p.name')
            ->getQuery()
            ->getArrayResult();

        $resources = array();
        foreach ($permissions as $permission) {
            try {
                list($resource, $action) = PermissionToAcl::translate($permission['name']);
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
