<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ArticleType repository
 */
class ArticleTypeFieldRepository extends EntityRepository
{
    public function getFieldsForType($type)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb ->select('atf')
            ->from('\Newscoop\Entity\ArticleTypeField', 'atf')
            ->where("atf.typeHack = ?1 AND atf.name IS NOT NULL AND atf.name <> 'NULL'" )
            ->setParameter(1, $type->getName());

        return $qb->getQuery();
    }
}
