<?php
/**
 * @package Campsite
 */

require_once dirname(__FILE__) . '/MetaDbObject.php';
require_once dirname(__FILE__) . '/MetaMapLocationMultimedia.php';
require_once $GLOBALS['g_campsiteDir'] . '/classes/GeoMapLocation.php';

/**
 * @package Campsite
 */
final class MetaMapLocation extends MetaDbObject
{
    /** @var array */
    private static $m_defaultCustomProperties = array(
        'name' => 'getName',
        'latitude' => 'getLatitude',
        'longitude' => 'getLongitude',
        'text' => 'getText',
        'content' => 'getContent',
        'multimedia' => 'getMultimedia',
        'enabled' => 'isEnabled',
    );

    /** @var IGeoMapLocationContent */
    private $m_content = NULL;

    /** @var array of MetaMapLocationMultimedia */
    private $multimedia = NULL;

    /**
     * @param IGeoMapLocation $p_dbObject
     */
    public function __construct(IGeoMapLocation $p_dbObject = null)
    {
        $this->m_properties = array();
        $this->m_customProperties = self::$m_defaultCustomProperties;
        if (!is_null($p_dbObject)) {
            $this->m_dbObject = $p_dbObject;
            $languageId = (int) CampTemplate::singleton()->context()->language->number;
            $this->m_content = $this->m_dbObject->getContent($languageId);
        } else {
            $this->m_dbObject = new Geo_MapLocation();
        }
    }

    /**
     * Get name
     * @return string
     */
    protected function getName()
    {
        return (!is_null($this->m_content)) ? $this->m_content->getName() : null;
    }

    /**
     * Get enabled state
     * @return boolean
     */
    protected function isEnabled()
    {
        $languageId = (int) CampTemplate::singleton()->context()->language->number;
        if ($this->m_dbObject->isEnabled($languageId)) {return true;}
        return false;
    }

    /**
     * Get latitude
     * @return float
     */
    protected function getLatitude()
    {
        return $this->m_dbObject->getLatitude();
    }

    /**
     * Get longitude
     * @return float
     */
    protected function getLongitude()
    {
        return $this->m_dbObject->getLongitude();
    }

    /**
     * Get text
     * @return string
     */
    protected function getText()
    {
        return (!is_null($this->m_content)) ? $this->m_content->getText() : null;
    }

    /**
     * Get content
     * @return string
     */
    protected function getContent()
    {
        return !is_null($this->m_content) ? $this->m_content->getContent() : null;
    }

    /**
     * Get multimedia
     * @return array of MetaMapLocationMultimedia
     */
    protected function getMultimedia()
    {
        if ($this->multimedia === NULL) {
            $this->multimedia = array();
            foreach ($this->m_dbObject->getMultimedia() as $multimedia) {
                $this->multimedia[] = new MetaMapLocationMultimedia($multimedia);
            }
        }
        return $this->multimedia;
    }
}
