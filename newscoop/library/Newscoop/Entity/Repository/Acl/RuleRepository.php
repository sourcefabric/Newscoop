<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository\Acl;

use InvalidArgumentException,
    Doctrine\ORM\EntityRepository,
    Doctrine\ORM\QueryBuilder,
    Newscoop\Entity\Acl\Rule,
    Newscoop\Entity\Acl\Role,
    Newscoop\Entity\Acl\Resource,
    Newscoop\Entity\Acl\Action;

/**
 * Acl Rule repository
 */
class RuleRepository extends EntityRepository
{
    /**
     * Save rule
     *
     * @param Newscoop\Entity\Acl\Rule $rule
     * @param array $values
     * @return void
     */
    public function save(Rule $rule, array $values)
    {
        $em = $this->getEntityManager();

        // get entities
        $role = $em->find('Newscoop\Entity\Acl\Role', (int) $values['role']);
        $resource = $values['resource'] ?
            $em->getReference('Newscoop\Entity\Acl\Resource', (int) $values['resource']) : NULL;
        $action = $values['action'] ?
            $em->getReference('Newscoop\Entity\Acl\Action', (int) $values['action']) : NULL;

        if ($this->isDuplicated($role, $resource, $action)) {
            throw new InvalidArgumentException;
        }

        $rule->setType($values['type']);
        $rule->setRole($role);
        $rule->setResource($resource);
        $rule->setAction($action);

        $em->persist($rule);
        $em->flush();
    }

    /**
     * Delete rule
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        $em = $this->getEntityManager();
        $proxy = $em->getReference('Newscoop\Entity\Acl\Rule', $id);
        $em->remove($proxy);
        $em->flush();
    }

    /**
     * Check if rule is duplicated in db
     *
     * @param Newscoop\Entity\Acl\Role $role
     * @param Newscoop\Entity\Acl\Resource|NULL $resource
     * @param Newscoop\Entity\Acl\Action|NULL $action
     * @return bool
     */
    private function isDuplicated(Role $role, Resource $resource = NULL, Action $action = NULL)
    {
        $query = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(r)')
            ->from('Newscoop\Entity\Acl\Rule', 'r')
            ->where('r.role = :role')
            ->setParameter('role', $role->getId());

        if ($resource) {
            $query->andWhere('r.resource = :resource')
                ->setParameter('resource', $resource->getId());
        } else {
            $query->andWhere('r.resource IS NULL');
        }

        if ($action) {
            $query->andWhere('r.action = :action')
                ->setParameter('action', $action->getId());
        } else {
            $query->andWhere('r.action IS NULL');
        }

        // duplicated if any
        return $query->getQuery()->getSingleScalarResult() > 0;
    }
}
