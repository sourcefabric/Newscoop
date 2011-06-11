<?php
/**
 * @package Campsite
 */

require_once dirname(__FILE__) . '/DatabaseObject.php';
require_once dirname(__FILE__) . '/IGeoMapLocationLanguage.php';

/**
 */
class Geo_MapLocationLanguage extends DatabaseObject implements IGeoMapLocationLanguage
{
    const TABLE = 'MapLocationLanguages';

    /** @var string */
    public $m_dbTableName = self::TABLE;

    /** @var array */
    public $m_keyColumnNames = array('fk_maplocation_id', 'fk_language_id');

    /** @var array */
    public $m_columnNames = array(
        'id',
        'fk_maplocation_id',
        'fk_language_id',
        'fk_content_id',
        'poi_display',
    );


    /**
     * @param IGeoMapLocation $mapLocation
     * @param int $languageId
     */
    public function __construct(IGeoMapLocation $mapLocation = NULL, $languageId = 0, array $p_languageSource = NULL, $p_forceExists = false)
    {
        global $g_ado_db;

        parent::__construct($this->m_columnNames);

        if ($p_languageSource) {
            $this->fetch($p_languageSource, $p_forceExists);
            return;
        }

        if ($mapLocation === NULL || $languageId < 1) {
            return;
        }
        $this->m_data['fk_maplocation_id'] = $mapLocation->getId();
        $this->m_data['fk_language_id'] = $languageId;

        $this->fetch();
    } // fn __construct


    /**
     * Return the location content identifier
     * @return int
     */
    public function getContentId()
    {
        return $this->m_data['fk_content_id'];
    } // fn getContentId


    /**
     * Point in this language is enabled?
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) ((int) $this->m_data['poi_display']);
    } // fn isEnabled
} // class Geo_MapLocationLanguage
