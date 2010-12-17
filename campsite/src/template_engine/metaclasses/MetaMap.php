<?php
/**
 * @package Campsite
 */

require_once dirname(__FILE__) . '/MetaDbObject.php';
require_once dirname(__FILE__) . '/MetaMapLocation.php';
require_once($GLOBALS['g_campsiteDir'] . '/classes/GeoMap.php');

/**
 * @package Campsite
 */
final class MetaMap extends MetaDbObject
{
    /**
     * @var array
     */
    private static $m_baseProperties = array(
        'number' => 'id',
        'name' => 'MapName',
        'provider' => 'MapProvider',
	);

    /**
     * @var array
     */
	private static $m_defaultCustomProperties = array(
        'locations' => 'getLocations',
        'is_enabled' => 'isEnabled',
	);

    /**
     * @var array of IGeoMapLocation
     */
    private $m_locations = NULL;

    /**
     * @param IGeoMapLocation $p_dbObject
     */
    public function __construct(IGeoMap $p_dbObject)
    {
        $this->m_properties = self::$m_baseProperties;
        $this->m_customProperties = self::$m_defaultCustomProperties;
        $this->m_dbObject = $p_dbObject;
    }

    /**
     * Get locations
     *
     * @return array of IGeoMapLocation
     */
    protected function getLocations()
    {
    	$locations = array();
        foreach ($this->m_dbObject->getLocations() as $location) {
        	$locations[] = new MetaMapLocation($location);
        }
        return $locations;
    }

    /**
     * Is Map enabled?
     *
     * @return bool
     */
    protected function isEnabled()
    {
        return $this->m_dbObject->isEnabled();
    }

    /**
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @return string
     */
    public static function GetMapTagBody($p_articleNumber, $p_languageId)
    {
        return Geo_Map::GetMapTagBody((int) $p_articleNumber, (int) $p_languageId);
    }

    /**
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @return string
     */
    public static function GetMapTagHeader($p_articleNumber, $p_languageId,
                                           $p_mapWidth = 0, $p_mapHeight = 0)
    {
        return Geo_Map::GetMapTagHeader((int) $p_articleNumber, (int) $p_languageId,
                                        (int) $p_mapWidth, (int) $p_mapHeight);
    }

    /**
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @return string
     */
    public static function GetMapTagCenter($p_articleNumber, $p_languageId)
    {
        return Geo_Map::GetMapTagCenter((int) $p_articleNumber, (int) $p_languageId);
    }

    /**
     * @param int $p_article
     * @param int $p_language
     * @return string
     */
    public static function GetMapTagList($p_articleNumber, $p_languageId)
    {
        return Geo_Map::GetMapTagList((int) $p_articleNumber, (int) $p_languageId);
    }
}
