<?php
/**
 * @package Campsite
 */

require_once dirname(__FILE__) . '/MetaDbObject.php';
require_once dirname(__FILE__) . '/MetaMapLocation.php';
require_once $GLOBALS['g_campsiteDir'] . '/classes/IGeoMap.php';

/**
 * @package Campsite
 */
final class MetaMap extends MetaDbObject
{
    /**
     * @var array
     */
    private static $m_baseProperties = array(
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
    public function getLocations()
    {
        return $this->m_dbObject->getLocations();
    }

    /**
     * Is Map enabled?
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->m_dbObject->isEnabled();
    }
}
