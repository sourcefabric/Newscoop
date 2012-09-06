<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Package;

use \Doctrine\ORM\EntityRepository;

/**
 * Item repository
 */
class ItemRepository extends EntityRepository
{
    public function getAllForPackage($id)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Package\Item')
            ->createQueryBuilder('i')
            ->where('i.package = :package')
            ->setParameter('package', $id);
        
        $query = $queryBuilder->getQuery();
        
        return $query;
    }
}
