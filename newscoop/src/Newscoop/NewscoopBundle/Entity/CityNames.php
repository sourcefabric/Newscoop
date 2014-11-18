<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * City Names entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="CityNames")
 */
class CityNames
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Newscoop\NewscoopBundle\Entity\CityLocations")
     */
    protected $fk_citylocations;

    /**
     * @ORM\Column(type="string", length=10, name="name_type")
     * @var int
     */
    protected $name_type;

    /**
     * @ORM\Column(type="string", length=1024, name="city_name")
     * @var string
     */
    protected $city_name;

    public function __construct() {}

    /**
     * Get fk_citylocations
     *
     * @return integer
     */
    public function getFkCitylocations()
    {
        return $this->fk_citylocations;
    }

    /**
     * Get name_type
     *
     * @return integer
     */
    public function getNameType()
    {
        return $this->name_type;
    }

    /**
     * Set name_type
     *
     * @param string $name_type
     *
     */
    public function setNameType($name_type)
    {
        $this->name_type = $name_type;

        return $this;
    }

    /**
     * Get city_name
     *
     * @return string
     */
    public function getCityName()
    {
        return $this->city_name;
    }

    /**
     * Set city_name
     *
     * @param string $city_name
     *
     */
    public function setCityName($city_name)
    {
        $this->city_name = $city_name;

        return $this;
    }
}