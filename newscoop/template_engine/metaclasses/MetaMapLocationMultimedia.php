<?php
/**
 * @package Campsite
 */

require_once dirname(__FILE__) . '/MetaDbObject.php';
require_once $GLOBALS['g_campsiteDir'] . '/classes/GeoMultimedia.php';
require_once $GLOBALS['g_campsiteDir'] . '/classes/IGeoMultimedia.php';
require_once $GLOBALS['g_campsiteDir'] . '/classes/IGeoMapLocation.php';

/**
 * @package Campsite
 */
final class MetaMapLocationMultimedia extends MetaDbObject
{
    /** @var array */
    private static $m_defaultCustomProperties = array(
        'src' => 'getSrc',
        'type' => 'getType',
        'spec' => 'getSpec',
        'width' => 'getWidth',
        'height' => 'getHeight',
    );

    /**
     * @param IGeoMultimedia $p_dbObject
     */
    public function __construct(IGeoMultimedia $p_dbObject = NULL)
    {
        $this->m_properties = array();
        $this->m_customProperties = self::$m_defaultCustomProperties;

        if (!is_null($p_dbObject)) {
            $this->m_dbObject = $p_dbObject;
        } else {
            $this->m_dbObject = new Geo_Multimedia;
        }
    }

    /**
     * Get src
     * @return string
     */
    protected function getSrc()
    {
        return $this->m_dbObject->getSrc();
    }

    /**
     * Get type
     * @return string
     */
    protected function getType()
    {
        return $this->m_dbObject->getType();
    }

    /**
     * Get spec
     * @return string
     */
    protected function getSpec()
    {
        return $this->m_dbObject->getSpec();
    }

    /**
     * Get width
     * @return int
     */
    protected function getWidth()
    {
        return $this->m_dbObject->getWidth();
    }

    /**
     * Get height
     * @return int
     */
    protected function getHeight()
    {
        return $this->m_dbObject->getHeight();
    }
}
