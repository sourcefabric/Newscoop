<?php
/**
 * @package Newscoop\CommunityTickerBundle
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\CommunityTickerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Newscoop\CommunityTickerBundle\Entity\CommunityTickerEvent;
use Newscoop\CommunityTickerBundle\TemplateList\ListCriteria;
use Newscoop\ListResult;

/**
 * Community Ticker Event Repository
 */
class CommunityTickerEventRepository extends EntityRepository
{
    /**
     * Save event
     *
     * @param  CommunityTickerEvent $event
     * @param  array                $values
     * @return void
     */
    public function save(CommunityTickerEvent $event, array $values)
    {
        $event->setEvent($values['event']);
        $event->setParams(isset($values['params']) ? $values['params'] : array());

        if (!empty($values['user'])) {
            $user = $values['user'];

            if (is_int($user)) {
                $user = $this->getEntityManager()->getReference('Newscoop\Entity\User', $values['user']);
            }

            $event->setUser($user);
        }

        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();
    }

    /**
     * Get list for given criteria
     *
     * @param  ListCriteria        $criteria
     * @return ListResult
     */
    public function getListByCriteria(ListCriteria $criteria)
    {
        $qb = $this->createQueryBuilder('ct');

        $qb->andWhere('ct.is_active = :is_active')
            ->setParameter('is_active', true);

        foreach ($criteria->perametersOperators as $key => $operator) {
            $qb->andWhere('ct.'.$key.' = :'.$key)
                ->setParameter($key, $criteria->$key);
        }

        $list = new ListResult();
        $countBuilder = clone $qb;
        $list->count = (int) $countBuilder->select('COUNT(ct)')->getQuery()->getSingleScalarResult();

        if($criteria->length != 0) {
            $qb->setMaxResults($criteria->length);
        }
        
        $metadata = $this->getClassMetadata();
        foreach ($criteria->orderBy as $key => $order) {
            if (array_key_exists($key, $metadata->columnNames)) {
                $key = 'ct.' . $key;
            }

            $qb->orderBy($key, $order);
        }

        $list->items = $qb->getQuery()->getResult();

        return $list;
    }

    /**
     * Get community feeds count by given criteria
     *
     * @param array $criteria
     * @return int
     */
    public function findByCount(array $criteria)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(c)')
            ->from($this->getEntityName(), 'c');

        foreach ($criteria as $property => $value) {
            if (!is_array($value)) {
                $queryBuilder->andWhere("u.$property = :$property");
            }
        }

        $query = $queryBuilder->getQuery();
        foreach ($criteria as $property => $value) {
            if (!is_array($value)) {
                $query->setParameter($property, $value);
            }
        }

        return (int) $query->getSingleScalarResult();
    }
}
