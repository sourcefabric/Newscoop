<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\NewscoopBundle\Entity\SystemPreferences;

/**
 * System preferences service
 */
class SystemPreferencesService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Magic function to set given value for given property
     *
     * @param  $property Given property
     * @param  $value    Value for given property
     *
     * @return void
     */
    public function __set($property, $value) 
    {
        if (empty($property) || !is_string($property)) {
            return;
        }

        $checkProperty = $this->em->getRepository('Newscoop\NewscoopBundle\Entity\SystemPreferences')
            ->findOneBy(array(
                'option' => $property
        ));

        if ($checkProperty) {
            $queryBuilder = $this->em->createQueryBuilder();
            $preference = $queryBuilder->update('Newscoop\NewscoopBundle\Entity\SystemPreferences', 's')
                ->set('s.value', ':value')
                ->set('s.created_at', ':lastmodified')
                ->where('s.option = :property')
                ->setParameters(array(
                    'value' => $value,
                    'property' => $property,
                    'lastmodified' => new \DateTime('now')
                ))
                ->getQuery();
            $preference->execute();

            $this->$property = $value;
        } else {
            $newProperty = new SystemPreferences();
            $newProperty->setOption($property);
            $newProperty->setValue($value);
            $newProperty->setCreatedAt(new \DateTime('now'));
            $this->em->persist($newProperty);
            $this->em->flush();
        }
    }

    /**
     * Magic function to get property value
     *
     * @param  $property Given property
     *
     * @return string|null
     */
    public function __get($property)
    {   
        $currentProperty = $this->em->getRepository('Newscoop\NewscoopBundle\Entity\SystemPreferences')
            ->findOneBy(array(
                'option' => $property
        ));

        if ($currentProperty) {
            return $currentProperty->getValue();
        } else {
            return null;
        }
    }

    /**
     * Set given value for given property
     * 
     * @param  $property Given property
     * @param  $value    Value for given property
     *
     * @return void
     */
    public function set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * Get value for given property
     *
     * @param  $property Given property
     *
     * @return string
     */
    public function get($property)
    {
        return $this->$property;
    }

    /**
     * Return whether statistics collecting was set on.
     *
     * @return bool
     */
    public function collectStatisticsAuto()
    {   
        return ($this->CollectStatistics == 'Y');
    }
}