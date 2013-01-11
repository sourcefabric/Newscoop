<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

/**
 * WeatherStat entity
 * @Entity(repositoryClass="Newscoop\Entity\Repository\WeatherStatRepository")
 * @Table(name="weather_stat")
 */
class WeatherStat extends Entity
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer", name="id")
     * @Var int
     */
    protected $id;

    /**
     * @Column(name="location_id")
     * @Var string
     */
    private $locationId;

    /**
     * @Column(name="location_type")
     * @Var string 
     */
    private $locationType;

    /**
     * @Column(name="location_name")
     * @Var string
     */
    private $locationName;

    /**
     * @Column(name="location_list")
     * @Var string 
     */
    private $locationList;

    /**
     * @Column(name="region_name", nullable=True)
     * @Var string 
     */
    private $regionName;

    /**
     * @Column(type="integer", name="hour", nullable=True)
     * @Var int 
     */
    private $hour;

    /**
     * @Column(type="integer", name="symbol")
     * @Var int 
     */
    private $symbol;

    /**
     * @Column(type="integer", name="temperature", nullable=True)
     * @Var int
     */
    private $temperature;

    /**
     * @Column(type="integer", name="temperature_min", nullable=True)
     * @Var int
     */
    private $temperatureMin;

    /**
     * @Column(type="integer", name="temperature_max", nullable=True)
     * @Var int
     */
    private $temperatureMax;

    /**
     * @Column(type="integer", name="precip", nullable=True)
     * @Var int
     */
    private $precip;

    /**
     * @Column(type="integer", name="winddir", nullable=True)
     * @Var int
     */
    private $winddir;

    /**
     * @Column(type="integer", name="windforce", nullable=True)
     * @Var int
     */
    private $windforce;

    /**
     * @Column(name="snow_condition", nullable=True)
     * @Var string 
     */
    private $snowCondition;

    /**
     * @Column(name="slope_condition", nullable=True)
     * @Var string 
     */
    private $slopeCondition;

    /**
     * @Column(type="integer", name="total_slopes", nullable=True)
     * @Var int
     */
    private $totalSlopes;

    /**
     * @Column(type="integer", name="open_slopes", nullable=True)
     * @Var int
     */
    private $openSlopes;

    /**
     * @Column(type="integer", name="elevation", nullable=True)
     * @Var int
     */
    private $elevation;

    /**
     * @Column(type="datetime", name="time_updated")
     * @Var DateTime
     */
    private $timeUpdated;

    /**
     * @return int
     */
    public function getLocationId()
    {
        return $this->locationId;
    }

    /**
     * @return string 
     */
    public function getLocationType()
    {
        return $this->locationType;
    }

    /**
     * @return string 
     */
    public function getLocationName()
    {
        return $this->locationName;
    }

    /**
     * @return string 
     */
    public function getLocationList()
    {
        return $this->locationList;
    }

    /**
     * @return string
     */
    public function getRegionName()
    {
        return $this->regionName;
    }

    /**
     * @return int 
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * @return int 
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @return int 
     */
    public function getTemperature()
    {
        return $this->temperature;
    }

    /**
     * @return int 
     */
    public function getTemperatureMin()
    {
        return $this->temperatureMin;
    }

    /**
     * @return int 
     */
    public function getTemperatureMax()
    {
        return $this->temperatureMax;
    }

    /**
     * @return int 
     */
    public function getPrecip()
    {
        return $this->precip;
    }

    /**
     * @return int 
     */
    public function getWinddir()
    {
        return $this->winddir;
    }

    /**
     * @return int 
     */
    public function getWindforce()
    {
        return $this->windforce;
    }

    /**
     * @return string
     */
    public function getSnowCondition()
    {
        return $this->snowCondition;
    }

    /**
     * @return string
     */
    public function getSlopeCondition()
    {
        return $this->slopeCondition;
    }

    /**
     * @return int 
     */
    public function getTotalSlopes()
    {
        return $this->totalSlopes;
    }

    /**
     * @return int 
     */
    public function getOpenSlopes()
    {
        return $this->openSlopes;
    }

    /**
     * @return int 
     */
    public function getElevation()
    {
        return $this->elevation;
    }

    /**
     * @return DateTime
     */
    public function getTimeUpdated()
    {
        return $this->timeUpdated;
    }

    /**
     * Set timeupdated
     *
     * @param DateTime $p_datetime
     * @return Newscoop\Entity\Rating
     */
    public function setTimeUpdated(\DateTime $p_datetime)
    {
        $this->timeUpdated = $p_datetime;
        return $this;
    }

    /**
     * Set locationId
     *
     * @param int $locationId
     * @return Newscoop\Entity\WeatherStat
     */
    public function setLocationId($locationId)
    {
        $this->locationId = $locationId;
        return $this;
    }

    /**
     * Set locationType
     *
     * @param string $locationType
     * @return Newscoop\Entity\WeatherStat
     */
    public function setLocationType($locationType)
    {
        $this->locationType = $locationType;
        return $this;
    }

    /**
     * Set locationName
     *
     * @param string $locationName
     * @return Newscoop\Entity\WeatherStat
     */
    public function setLocationName($locationName)
    {
        $this->locationName = $locationName;
        return $this;
    }

    /**
     * Set locationList
     *
     * @param string $locationList
     * @return Newscoop\Entity\WeatherStat
     */
    public function setLocationList($locationList)
    {
        $this->locationList = $locationList;
        return $this;
    }

    /**
     * Set regionName
     *
     * @param string $regionName
     * @return Newscoop\Entity\WeatherStat
     */
    public function setRegionName($regionName)
    {
        $this->regionName = $regionName;
        return $this;
    }

    /**
     * Set hour
     *
     * @param int $hour
     * @return Newscoop\Entity\WeatherStat
     */
    public function setHour($hour)
    {
        $this->hour = $hour;
        return $this;
    }

    /**
     * Set symbol
     *
     * @param int $symbol
     * @return Newscoop\Entity\WeatherStat
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * Set temperature 
     *
     * @param int $temperature
     * @return Newscoop\Entity\WeatherStat
     */
    public function setTemperature($temperature)
    {
        $this->temperature = $temperature;
        return $this;
    }

    /**
     * Set temperatureMin 
     *
     * @param int $temperatureMin
     * @return Newscoop\Entity\WeatherStat
     */
    public function setTemperatureMin($temperatureMin)
    {
        $this->temperatureMin = $temperatureMin;
        return $this;
    }

    /**
     * Set temperatureMax
     *
     * @param int $temperatureMax
     * @return Newscoop\Entity\WeatherStat
     */
    public function setTemperatureMax($temperatureMax)
    {
        $this->temperatureMax = $temperatureMax;
        return $this;
    }

    /**
     * Set precip
     *
     * @param int $precip
     * @return Newscoop\Entity\WeatherStat
     */
    public function setPrecip($precip)
    {
        $this->precip = $precip;
        return $this;
    }

    /**
     * Set winddir
     *
     * @param int $winddir
     * @return Newscoop\Entity\WeatherStat
     */
    public function setWinddir($winddir)
    {
        $this->winddir = $winddir;
        return $this;
    }

    /**
     * Set windforce
     *
     * @param int $windforce
     * @return Newscoop\Entity\WeatherStat
     */
    public function setWindforce($windforce)
    {
        $this->windforce = $windforce;
        return $this;
    }

    /**
     * Set snowCondition
     *
     * @param string $snowCondition
     * @return Newscoop\Entity\WeatherStat
     */
    public function setSnowCondition($snowCondition)
    {
        $this->snowCondition = $snowCondition;
        return $this;
    }

    /**
     * Set slopeCondition
     *
     * @param string $slopeCondition
     * @return Newscoop\Entity\WeatherStat
     */
    public function setSlopeCondition($slopeCondition)
    {
        $this->slopeCondition = $slopeCondition;
        return $this;
    }

    /**
     * Set totalSlopes
     *
     * @param int $totalSlopes
     * @return Newscoop\Entity\WeatherStat
     */
    public function setTotalSlopes($totalSlopes)
    {
        $this->totalSlopes = $totalSlopes;
        return $this;
    }

    /**
     * Set openSlopes
     *
     * @param int $openSlopes
     * @return Newscoop\Entity\WeatherStat
     */
    public function setOpenSlopes($openSlopes)
    {
        $this->openSlopes = $openSlopes;
        return $this;
    }

    /**
     * Set elevation
     *
     * @param int $elevation
     * @return Newscoop\Entity\WeatherStat
     */
    public function setElevation($elevation)
    {
        $this->elevation = $elevation;
        return $this;
    }

    public function exists()
    {
        return true;
    }
}
