<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Doctrine\ORM\QueryBuilder;

/**
 * Language repository
 */
class LanguageRepository extends EntityRepository
{
    /**
     * Delete rule
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        $em = $this->getEntityManager();
        $proxy = $em->getReference('Newscoop\Entity\Language', $id);
        $em->remove($proxy);
        $em->flush();
    }

    /**
     * Get languages
     *
     * @return array
     */
    public function getLanguages()
    {
        $qb = $this->createQueryBuilder('l');

        return $qb->orderBy('l.name', 'DESC')
            ->getQuery()
            ->getResult();
    }
}