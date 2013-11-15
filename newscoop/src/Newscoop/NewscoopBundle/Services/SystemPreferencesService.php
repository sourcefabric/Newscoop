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

    public function __set($property, $value) {
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
        }
    }

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
     * Return whether statistics collecting was set on.
     *
     * @return bool
     */
    public function collectStatisticsAuto()
    {   
        return ($this->CollectStatistics == 'Y');
    }
}