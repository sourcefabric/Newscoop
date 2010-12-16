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
	public $m_keyColumnNames = array('id');

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
	public function __construct(IGeoMapLocation $mapLocation = NULL, $languageId = 0)
	{
        global $g_ado_db;

        parent::__construct($this->m_columnNames);

        if ($mapLocation === NULL) {
            return;
        }

        $queryStr = 'SELECT ' . implode(', ', $this->m_columnNames) . '
            FROM ' . self::TABLE . '
            WHERE fk_maplocation_id = ' . $mapLocation->getId() . '
                AND fk_language_id = ' . ((int) $languageId);
        $this->m_data = $g_ado_db->GetRow($queryStr);
	}

    /**
     * Point in this language is enabled?
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) ((int) $this->m_data['poi_display']);
    }
} // class Geo_MapLocationLanguage
