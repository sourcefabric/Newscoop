<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Newscoop\Entity\WeatherStat;

/**
 * WeatherStat repository
 */
class WeatherStatRepository extends EntityRepository
{

    /**
     * Get new instance of the rating
     *
     * @return \Newscoop\Entity\WeatherStat
     */
    public function getPrototype()
    {
        return new WeatherStat;
    }

    /**
     * Method for saving a weather stat 
     *
     * @param WeatherStat $entity
     * @param array $values
     * @return WeatherStat $entity
     */
    public function save(WeatherStat $entity, $values)
    {
        $em = $this->getEntityManager();

        $entity->setLocationId($values['location_id'])
            ->setLocationType($values['location_type'])
            ->setLocationName($values['location_name'])
            ->setLocationList($values['location_list'])
            ->setRegionName($values['region_name'])
            ->setHour($values['hour'])
            ->setTimeUpdated(new \DateTime);

        if (isset($values['symbol'])) {
            $entity->setSymbol($values['symbol']);
        }
        if (isset($values['temperature'])) {
            $entity->setTemperature($values['temperature']);
        }
        if (isset($values['temperature_min'])) {
            $entity->setTemperatureMin($values['temperature_min']);
        }
        if (isset($values['temperature_max'])) {
            $entity->setTemperatureMax($values['temperature_max']);
        }
        if (isset($values['precip'])) {
            $entity->setPrecip($values['precip']);
        }
        if (isset($values['winddir'])) {
            $entity->setWinddir($values['winddir']);
        }
        if (isset($values['windforce'])) {
            $entity->setWindforce($values['windforce']);
        }
        if (isset($values['snow_condition'])) {
            $entity->setSnowCondition($values['snow_condition']);
        }
        if (isset($values['slope_condition'])) {
            $entity->setSlopeCondition($values['slope_condition']);
        }
        if (isset($values['total_slopes'])) {
            $entity->setTotalSlopes($values['total_slopes']);
        }
        if (isset($values['open_slopes'])) {
            $entity->setOpenSlopes($values['open_slopes']);
        }
        if (isset($values['elevation'])) {
            $entity->setElevation($values['elevation']);
        }

        $em->persist($entity);

        return $entity;
    }

    /**
     * Flush method
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * Get rating count
     *
     * @param array $criteria
     * @return int
     */
    public function countBy(array $criteria)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(u)')
            ->from($this->getEntityName(), 'u');

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
