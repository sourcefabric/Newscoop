<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaWeatherStat extends MetaDbObject
{
    /** @var array */
    private static $m_baseProperties = array(
        'id' => 'id',
    );

    private static $m_defaultCustomProperties = array(
        'location_name' => 'getLocationName',
        'location_id' => 'getLocationId',
        'location_type' => 'getLocationType',
        'location_list' => 'getLocationList',
        'region_name' => 'getRegionName',
        'hour' => 'getHour',
        'symbol' => 'getSymbol',
        'temperature' => 'getTemperature',
        'temperature_min' => 'getTemperatureMin',
        'temperature_max' => 'getTemperatureMax',
        'precip' => 'getPrecip',
        'winddir' => 'getWindforce',
        'windforce' => 'getWinddir',
        'snow_condition' => 'getSnowCondition',
        'slope_condition' => 'getSlopeCondition',
        'total_slopes' => 'getTotalSlopes',
        'open_slopes' => 'getOpenSlopes',
    );

    public function __construct($id = null)
    {
        global $controller;
        $repository = $controller->getHelper('entity')->getRepository('Newscoop\Entity\WeatherStat');

        $this->m_properties = self::$m_baseProperties;
        $this->m_customProperties = self::$m_defaultCustomProperties;

        if (is_null($id)) {
            $this->m_dbObject = $repository->getPrototype();
        } else {
            $this->m_dbObject = $repository->find($id);
        }
    }

    protected function getLocationName()
    {
        return $this->m_dbObject->getLocationName();
    }

    protected function getLocationId()
    {
        return $this->m_dbObject->getLocationId();
    }

    protected function getLocationType()
    {
        return $this->m_dbObject->getLocationType();
    }

    protected function getLocationList()
    {
        return $this->m_dbObject->getLocationList();
    }

    protected function getRegionName()
    {
        return $this->m_dbObject->getRegionName();
    }

    protected function getHour()
    {
        return $this->m_dbObject->getHour();
    }

    protected function getSymbol()
    {
        return $this->m_dbObject->getSymbol();
    }

    protected function getTemperature()
    {
        return $this->m_dbObject->getTemperature();
    }

    protected function getTemperatureMin()
    {
        return $this->m_dbObject->getTemperatureMin();
    }

    protected function getTemperatureMax()
    {
        return $this->m_dbObject->getTemperatureMax();
    }

    protected function getPrecip()
    {
        return $this->m_dbObject->getPrecip();
    }

    protected function getWinddir()
    {
        return $this->m_dbObject->getWinddir();
    }

    protected function getWindforce()
    {
        return $this->m_dbObject->getWindforce();
    }

    protected function getSnowCondition()
    {
        return $this->m_dbObject->getSnowCondition();
    }

    protected function getSlopeCondition()
    {
        return $this->m_dbObject->getSlopeCondition();
    }

    protected function getTotalSlopes()
    {
        return $this->m_dbObject->getTotalSlopes();
    }

    protected function getOpenSlopes()
    {
        return $this->m_dbObject->getOpenSlopes();
    }
}
