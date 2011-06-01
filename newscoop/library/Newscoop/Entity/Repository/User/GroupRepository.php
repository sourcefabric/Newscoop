<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository\User;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\User\Group,
    Newscoop\Entity\Acl\Role;

/**
 * User Group repository
 */
class GroupRepository extends EntityRepository
{
    /**
     * Save group
     *
     * @param Newscoop\Entity\User\Group $group
     * @param array $values
     * @return void
     */
    public function save(Group $group, array $values)
    {
        $em = $this->getEntityManager();

        $group->setName($values['name']);

        if (!$group->getRoleId()) {
            $role = new Role;
            $em->persist($role);
            $group->setRole($role);
        }

        $em->persist($group);
        $em->flush();
    }

    /**
     * Delete group
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        $em = $this->getEntityManager();
        $proxy = $em->getReference('Newscoop\Entity\User\Group', (int) $id);
        $em->remove($proxy);
        $em->flush();
    }
}
