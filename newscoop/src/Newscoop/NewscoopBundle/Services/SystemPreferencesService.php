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
use Doctrine\Common\Collections\Criteria;

/**
 * System preferences service
 */
class SystemPreferencesService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    protected $preferences;

    private $requestedPropertiesStack = array();

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
                'option' => $property,
        ));

        if ($checkProperty) {
            if (isset($this->requestedPropertiesStack[$property])) {
                unset($this->requestedPropertiesStack[$property]);
            }

            $queryBuilder = $this->em->createQueryBuilder();
            $preference = $queryBuilder->update('Newscoop\NewscoopBundle\Entity\SystemPreferences', 's')
                ->set('s.value', ':value')
                ->set('s.created_at', ':lastmodified')
                ->where('s.option = :property')
                ->setParameters(array(
                    'value' => $value,
                    'property' => $property,
                    'lastmodified' => new \DateTime('now'),
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
        if (isset($this->requestedPropertiesStack[$property])) {
            return $this->requestedPropertiesStack[$property];
        }

        $currentProperty = $this->findOneBy($property);
        if (!empty($currentProperty)) {
            $currentProperty = reset($currentProperty);
            $this->requestedPropertiesStack[$property] = $currentProperty['value'];

            return $currentProperty['value'];
        }
    }

    /**
     * Set given value for given property
     *
     * @param $property Given property
     * @param $value    Value for given property
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
     * @param $property Given property
     *
     * @return string
     */
    public function get($property, $default = null)
    {
        $property = $this->$property;

        if ($property === null) {
            return $default;
        }

        return $property;
    }

    /**
     * Delete system preference by varname
     *
     * @param string $varname System preference varname
     *
     * @return void
     */
    public function delete($varname)
    {
        $property = $this->em->getRepository('Newscoop\NewscoopBundle\Entity\SystemPreferences')
            ->findOneBy(array(
                'option' => $varname,
        ));

        if ($property) {
            if (isset($this->requestedPropertiesStack[$varname])) {
                unset($this->requestedPropertiesStack[$varname]);
            }

            $this->em->remove($property);
            $this->em->flush();
        }
    }

    /**
     * Get all available preferences
     *
     * @return array
     */
    public function getAllPreferences()
    {
        if ($this->preferences) {
            return $this->preferences;
        }

        return $this->preferences = $this->em
            ->createQueryBuilder('p')
            ->select('p.value', 'p.option')
            ->from('Newscoop\NewscoopBundle\Entity\SystemPreferences', 'p')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Find one preference by matching criteria from object
     * Works like caching
     *
     * @param string $property Property name
     *
     * @return array
     */
    public function findOneBy($property)
    {
        return array_filter($this->getAllPreferences(), function ($pref) use ($property) {
            return $pref['option'] === $property;
        });
    }
}
