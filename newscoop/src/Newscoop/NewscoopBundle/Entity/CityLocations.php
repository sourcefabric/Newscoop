<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Newscoop\NewscoopBundle\ORM\Point;

/**
 * City Locations entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="CityLocations")
 */
class CityLocations 
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", name="id")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="point", name="position")
     */
    private $position;

    /**
     * @ORM\Column(type="string", name="city_type", nullable=true)
     * @var string
     */
    private $city_type;

    /**
     * @ORM\Column(type="integer", name="population")
     * @var int
     */
    private $population;

    /**
     * @ORM\Column(type="string", columnDefinition="CHAR(2) NOT NULL", name="elevation")
     * @var string
     */
    private $elevation;

    /**
     * @ORM\Column(type="string", length=1023, name="time_zone")
     * @var string
     */
    private $time_zone;

    public function __construct() {}

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get city_type
     *
     * @return string
     */
    public function getCityType()
    {
        return $this->city_type;
    }

    /**
     * Set city_type
     *
     * @param string $city_type
     *
     */
    public function setCityType($city_type)
    {
        $this->city_type = $city_type;

        return $this;
    }

    /**
     * Set position
     *
     * @return Point $position
     */
    public function setPosition(Point $position) 
    {
        $this->position = $position;

        return $this;
    }
    
    /**
     * Get position
     *
     * @return Point
     */
    public function getPosition() 
    {
        return $this->position;
    }

    /**
     * Set population
     *
     * @return integer $population
     */
    public function setPopulation($population) 
    {
        $this->population = $population;

        return $this;
    }
    
    /**
     * Get population
     *
     * @return integer
     */
    public function getPopulation() 
    {
        return $this->population;
    }

    /**
     * Set elevation
     *
     * @return integer $elevation
     */
    public function setElevation($elevation) 
    {
        $this->elevation = $elevation;

        return $this;
    }
    
    /**
     * Get elevation
     *
     * @return integer
     */
    public function getElevation() 
    {
        return $this->elevation;
    }

    /**
     * Set time zone
     *
     * @return string $time_zone
     */
    public function setTimeZone($time_zone) 
    {
        $this->time_zone = $time_zone;

        return $this;
    }
    
    /**
     * Get time zone
     *
     * @return string
     */
    public function getTimeZone() 
    {
        return $this->time_zone;
    }
}