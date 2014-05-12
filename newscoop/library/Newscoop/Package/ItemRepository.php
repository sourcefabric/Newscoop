<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Package;

use \Doctrine\ORM\EntityRepository;
use Newscoop\Criteria\SlideshowItemCriteria;

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
            ->select('i, ii')
            ->leftJoin('i.image', 'ii')
            ->where('i.package = :package')
            ->setParameter('package', $id);

        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Get list for given criteria
     *
     * @param Newscoop\Criteria\SlideshowItemCriteria $criteria
     *
     * @return Newscoop\ListResult
     */
    public function getListByCriteria(SlideshowItemCriteria $criteria)
    {
        $qb = $this->createQueryBuilder('i');
        $qb->select('i, ii, p')
            ->leftJoin('i.image', 'ii')
            ->leftJoin('i.package', 'p');

        if ($criteria->slideshow) {
            $qb->andWhere('i.package '.$criteria->perametersOperators['slideshow'].' :package')
                ->setParameter('package', $criteria->slideshow);

            unset($criteria->perametersOperators['slideshow']);
        }

        if ($criteria->type) {
            if ($criteria->type == 'image' && $criteria->perametersOperators['type'] == '=') {
                $qb->andWhere('i.videoUrl IS NULL');
            } elseif ($criteria->type == 'video' && $criteria->perametersOperators['type'] == '=') {
                $qb->andWhere('i.videoUrl IS NOT NULL');
            }

            unset($criteria->perametersOperators['type']);
        }

        foreach ($criteria->perametersOperators as $key => $operator) {
            if ($criteria->$key !== null) {
                $qb->andWhere('i.'.$key.' '.$operator.' :'.$key)
                    ->setParameter($key, $criteria->$key);
            }
        }

        $metadata = $this->getClassMetadata();
        foreach ($criteria->orderBy as $key => $order) {
            if (array_key_exists($key, $metadata->columnNames)) {
                $key = 'i.' . $key;
            }

            $qb->orderBy($key, $order);
        }
        $query = $qb->getQuery();

        return $query;
    }
}
