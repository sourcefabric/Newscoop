<?php
/**
 * @package Campsite
 */

require_once dirname(__FILE__) . '/DatabaseObject.php';
require_once dirname(__FILE__) . '/GeoMapLocationLanguage.php';
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
    public function __construct(IGeoMapLocation $mapLocation = NULL, IGeoMapLocationLanguage $locationLanguage = NULL, array $p_contentSource = NULL, $p_forceExists = false)
    {
        global $g_ado_db;

        parent::__construct($this->m_columnNames);

        if ($p_contentSource) {
            $this->fetch($p_contentSource, $p_forceExists);
            return;
        }

        if ($mapLocation === NULL || $locationLanguage == NULL) {
            return;
        }

        if ($locationLanguage->exists()) {
            $this->m_data['id'] = $locationLanguage->getContentId();
            $this->fetch();
        } else {
            $this->m_data = array();
            $this->m_exists = false;
        }
    }


    /**
     * Get content
     * @return string
     */
    public function getContent()
    {
        return (string) $this->m_data['poi_content'];
    } // fn getContent


    /**
     * Get text
     * @return string
     */
    public function getText()
    {
        return (string) $this->m_data['poi_text'];
    } // fn getText


    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return (string) $this->m_data['poi_name'];
    } // fn getName


    /**
     * Inserts text content of the poi
     *
     * @param array $poi
     *
     * @return int
     */
    public static function InsertContent($poi)
    {
        global $g_ado_db;
        global $g_user;

        $queryStr_con_in = 'INSERT INTO ' . self::TABLE . ' (';
        $queryStr_con_in .= 'poi_name, poi_link, poi_perex, ';
        $queryStr_con_in .= 'poi_content_type, poi_content, poi_text, IdUser';
        $queryStr_con_in .= ') VALUES (';

        $quest_marks = array();
        for ($ind = 0; $ind < 6; $ind++) {$quest_marks[] = '?';}
        $queryStr_con_in .= implode(', ', $quest_marks);

        $queryStr_con_in .= ', %%user_id%%)';

        $queryStr_con_sl = 'SELECT id FROM ' . self::TABLE . ' WHERE poi_name = ? AND poi_link = ? ';
        $queryStr_con_sl .= 'AND poi_perex = ? AND poi_content_type = ? AND poi_content = ? AND poi_text = ? ';
        $queryStr_con_sl .= 'ORDER BY id LIMIT 1';

        // ad B 3)
        $con_in_params = array();

        $con_in_params[] = '' . $poi['name'];
        $con_in_params[] = '' . $poi['link'];

        $con_in_params[] = '' . $poi['perex'];
        $con_in_params[] = 0 + $poi['content_type'];
        $con_in_params[] = '' . $poi['content'];
        $con_in_params[] = '' . $poi['text'];

        $con_id = 0;

        $rows = $g_ado_db->GetAll($queryStr_con_sl, $con_in_params);
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $con_id = $row['id'];
            }
        }

        if ((!$con_id) || (0 == $con_id))
        {
            // insert the POI content on the used language
            $queryStr_con_in = str_replace('%%user_id%%', $g_user->getUserId(), $queryStr_con_in);

            $success = $g_ado_db->Execute($queryStr_con_in, $con_in_params);

            // ad B 4)
            $con_id = $g_ado_db->Insert_ID();
        }

        return $con_id;
    } // fn InsertContent


    /**
     * Updates visibility state of the poi
     *
     * @param array $poi
     *
     * @return void
     */
    public static function UpdateState($poi)
    {
        global $g_ado_db;

        $queryStr = 'UPDATE ' . Geo_MapLocationLanguage::TABLE . ' SET poi_display = ? WHERE id = ?';

        $sql_params = array();
        $sql_params[] = $poi['display'];
        $sql_params[] = $poi['id'];

        $success = $g_ado_db->Execute($queryStr, $sql_params);
    } // fn UpdateState


    /**
     * Updates text content of the poi
     *
     * @param array $poi
     *
     * @return void
     */
    public static function UpdateText($poi)
    {
        global $g_ado_db;

/*
    A)
        1) given article_number, language_id, map_id, list of map_loc_lan_id / new data

    B)
        cycle:
            1) read content_id (as old_con_id) of the map_loc_lan_id
            2) insert new content with new data
            3) get the inserted id into new_con_id
            4) update maplocationlanguages into the new_con_id for the map_loc_lan_id
            6) delete content of old_con_id if none maplocationlanguage with a link into the old_con_id

*/


        // ad B 1)
        $queryStr_con_id = 'SELECT fk_content_id AS con FROM ' . Geo_MapLocationLanguage::TABLE . ' WHERE id = ?';
        // ad B 2)
        // call InsertContent();
        // ad B 4)
        $queryStr_map_up = 'UPDATE ' . Geo_MapLocationLanguage::TABLE . ' SET fk_content_id = ? WHERE id = ?';
        // ad B 6)
        $queryStr_con_rm = 'DELETE FROM ' .  self::TABLE . ' WHERE id = ? AND NOT EXISTS (SELECT id FROM ' . Geo_MapLocationLanguage::TABLE . ' WHERE fk_content_id = ?)';

        {
            // ad B 1)
            $con_old_id = null;
            try
            {
                $mapcon_sel_params = array();

                $mapcon_sel_params[] = $poi['id'];

                $rows = $g_ado_db->GetAll($queryStr_con_id, $mapcon_sel_params);
                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        $con_old_id = $row['con'];
                    }
                }
            }
            catch (Exception $exc)
            {
                return false;
            }

            if (null === $con_old_id) {continue;}

            // ad B 2/3)
            $con_new_id = self::InsertContent($poi);

            // ad B 4)
            {
                $map_up_params = array();
                $map_up_params[] = $con_new_id;

                $map_up_params[] = $poi['id'];

                $success = $g_ado_db->Execute($queryStr_map_up, $map_up_params);
            }

            // ad B 6)
            try
            {
                $con_rm_params = array();
                $con_rm_params[] = $con_old_id;
                $con_rm_params[] = $con_old_id;

                $success = $g_ado_db->Execute($queryStr_con_rm, $con_rm_params);
            }
            catch (Exception $exc)
            {
                return false;
            }

        }

    } // fn UpdateText
} // class Geo_MapLocationContent
