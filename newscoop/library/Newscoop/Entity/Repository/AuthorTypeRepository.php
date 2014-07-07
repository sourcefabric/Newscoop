<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * AuthorType repository
 */
class AuthorTypeRepository extends EntityRepository
{
    public function getAuthorsTypes()
    {
        $qb = $this->createQueryBuilder('at');

        return $qb->getQuery();
    }

    public function getAuthorType($id)
    {
        $qb = $this->createQueryBuilder('at')
            ->where('at.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery();
    }
}
