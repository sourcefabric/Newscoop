<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository\Acl;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\Acl\Rule;

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

        $role = $em->getReference('Newscoop\Entity\Acl\Role', (int) $values['role']);

        $rule->setType($values['type']);
        $rule->setRole($role);
        $rule->setResource((string) $values['resource']);
        $rule->setAction((string) $values['action']);

        $em->persist($rule);
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
    }
}
