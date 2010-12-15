<?php
/**
 * @package Campsite
 */

require_once dirname(__FILE__) . '/MetaDbObject.php';
require_once $GLOBALS['g_campsiteDir'] . '/classes/IGeoMapLocation.php';

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
	);

    /** @var IGeoMapLocationContent */
    private $m_content = NULL;

    /**
     * @param IGeoMapLocation $p_dbObject
     */
    public function __construct(IGeoMapLocation $p_dbObject)
    {
        $this->m_customProperties = self::$m_defaultCustomProperties;
        $this->m_dbObject = $p_dbObject;

        $languageId = (int) CampTemplate::singleton()->context()->language->number;
        $this->m_content = $this->m_dbObject->getContent($languageId);
    }

    /**
     * Get name
     * @return string
     */
    protected function getName()
    {
        return $this->m_content->getName();
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
        return $this->m_content->getText();
    }

    /**
     * Get content
     * @return string
     */
    public function getContent()
    {
        $this->m_content->getContent();
    }
}
