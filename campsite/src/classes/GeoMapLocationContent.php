<?php
/**
 * @package Campsite
 */

require_once dirname(__FILE__) . '/DatabaseObject.php';
require_once dirname(__FILE__) . '/IGeoMapLocationContent.php';

/**
 */
class Geo_MapLocationContent extends DatabaseObject implements IGeoMapLocationContent
{
    const TABLE = 'LocationContents';

    /** @var string */
	public $m_dbTableName = self::TABLE;

    /** @var array */
	public $m_keyColumnNames = array('id');

    /** @var array */
    public $m_columnNames = array(
        'id',
        'poi_name',
        'poi_link',
        'poi_perex',
        'poi_content_type',
        'poi_content',
        'poi_text',
        'IdUser',
        'time_updated',
    );

	/**
     * @param IGeoMapLocation $mapLocation
     * @param int $languageId
	 */
	public function __construct(IGeoMapLocation $mapLocation, $languageId)
	{
        global $g_ado_db;

        parent::__construct($this->m_columnNames);

        $queryStr = 'SELECT lc.' . implode(', lc.', $this->m_columnNames) . '
            FROM ' . self::TABLE . ' lc
                INNER JOIN ' . Geo_MapLocationLanguage::TABLE . ' ll on lc.id = ll.fk_content_id
            WHERE ll.fk_maplocation_id = ' . $mapLocation->getId() . '
                AND fk_language_id = ' . ((int) $languageId);
        $this->m_data = $g_ado_db->GetRow($queryStr);
	}

    /**
     * Get content
     * @return string
     */
    public function getContent()
    {
        return (string) $this->m_data['poi_content'];
    }

    /**
     * Get text
     * @return string
     */
    public function getText()
    {
        return (string) $this->m_data['poi_text'];
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return (string) $this->m_data['poi_name'];
    }
}
