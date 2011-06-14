<?php
/**
 * @package Campsite
 */

require_once dirname(__FILE__) . '/DatabaseObject.php';
require_once dirname(__FILE__) . '/Article.php';
require_once dirname(__FILE__) . '/GeoLocation.php';
require_once dirname(__FILE__) . '/GeoMapLocation.php';
require_once dirname(__FILE__) . '/GeoMapLocationContent.php';
require_once dirname(__FILE__) . '/GeoMultimedia.php';
require_once dirname(__FILE__) . '/GeoPreferences.php';
require_once dirname(__FILE__) . '/IGeoMap.php';

require_once($GLOBALS['g_campsiteDir']."/admin-files/lib_campsite.php");
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/ComparisonOperation.php');
camp_load_translation_strings('globals');

/**
 * @package Campsite
 */
class Geo_Map extends DatabaseObject implements IGeoMap
{
    const TABLE = 'Maps';

    /**
     * @var string
     */
    public $m_dbTableName = self::TABLE;

    /**
     * @var array
     */
    public $m_keyColumnNames = array('id');

    /**
     * @var array
     */
    public $m_columnNames = array(
        'id', // int - Map ID
        'fk_article_number', // int - link to the respective article
        'MapRank', // int - rank of the map in the article
        'MapUsage', // int - map enabled
        'MapCenterLongitude', // real - initial map center
        'MapCenterLatitude',
        'MapDisplayResolution', // int - initial map resolution
        'MapProvider', // string - the map to be used for readers
        'MapWidth', // int - the map div size
        'MapHeight',
        'MapName', // string - the map name
        'IdUser', // int - management related things
        'time_updated' // timestamp
    );

    /**
     * Constructor
     *
     * @param int $p_id
     * @return Map
     */
    public function __construct($p_id = null)
    {
        if (is_numeric($p_id)) {
            $this->m_data['id'] = $p_id;
            $this->fetch();
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->m_data['id'];
    }

    /**
     * TODO: obsolete, use $this->getId() instead.
     */
    public function GetMapId()
    {
        return (int) $this->m_data['id'];
    }

    /**
     * @return int
     */
    public function getArticleNumber()
    {
        return (int) $this->m_data['fk_article_number'];
    }

    /**
     * @return double
     */
    public function getInitialCenterLongitude()
    {
        return (double) $this->m_data['MapCenterLongitude'];
    }

    /**
     * @return double
     */
    public function getInitialCenterLatitude()
    {
        return (double) $this->m_data['MapCenterLatitude'];
    }

    /**
     * @return int
     */
    public function getDisplayResolution()
    {
        return (int) $this->m_data['MapDisplayResolution'];
    }

    /**
     * @return string
     */
    public function getMapProvider()
    {
        return (string) $this->m_data['MapProvider'];
    }

    /**
     * @return array
     */
    public function getDimensions()
    {
        return array('width' => (int) $this->m_data['MapWidth'],
            'height' => (int) $this->m_data['MapHeight']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return (string) $this->m_data['MapName'];
    }

    /**
     * @return User|NULL
     */
    public function getUser()
    {
        $userObj = new User($this->m_data['IdUser']);
        return ($userObj->exists()) ? $userObj : NULL;
    }

    /**
     * @return timestamp
     */
    public function getLastModified()
    {
        return $this->m_data['time_updated'];
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool) ((int) $this->m_data['MapUsage']);
    }

    /**
     * @return array of IGeoMapLocation
     */
    public function getLocations()
    {
        $locations = Geo_MapLocation::GetByMap($this);
        return (array) $locations;
    }

    /**
     * @param int $p_articleNumber
     * @return Geo_Map
     */
    public static function GetMapByArticle($p_articleNumber)
    {
        global $g_ado_db;

        $queryStr = 'SELECT id
            FROM ' . self::TABLE . '
            WHERE fk_article_number = ' . (int) $p_articleNumber . '
            AND MapUsage = 1
            ORDER BY MapRank, id';
        $mapId = $g_ado_db->GetOne($queryStr);
        $map = new self((int) $mapId);
        return $map;
    }


    /**
     * Gives map id of the article
     *
     * @param Article
     * @return int
     */
    public static function GetArticleMapId($p_articleObj)
    {
        global $g_ado_db;

        $article_number = $p_articleObj->getArticleNumber();
        $map_id = self::GetMapIdByArticle($article_number);
        return $map_id;
    } // fn GetArticleMapId

    /**
     * @param int $p_articleNumber
     * @return int $map_id
     */
    public static function GetMapIdByArticle($p_articleNumber)
    {
        global $g_ado_db;

        $queryStr = 'SELECT id
            FROM ' . self::TABLE . '
            WHERE fk_article_number = ? AND MapUsage = 1
            ORDER BY MapRank, id
            LIMIT 1';
        $map_id = null;
        try {
            $sql_params = array();
            $sql_params[] = (int) $p_articleNumber;
            $rows = $g_ado_db->GetAll($queryStr, $sql_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $map_id = (int) $row['id'];
                }
            }
        } catch (Exception $exc) {
            return null;
        }
        return $map_id;
    } // fn GetMapIdByArticle

    /**
     * Gives array of article's maps, with usage flags.
     *
     * @param object Article
     * @return array
     */
    public static function GetMapIdsByArticle($p_articleObj)
    {
        global $g_ado_db;

        $article_number = $p_articleObj->getArticleNumber();
        $queryStr = 'SELECT id, MapUsage AS usage
            FROM ' . self::TABLE . '
            WHERE fk_article_number = ?
            ORDER BY MapRank, id';
        $map_ids = array();
        try {
            $sql_params = array();
            $sql_params[] = $article_number;
            $rows = $g_ado_db->GetAll($queryStr, $sql_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $map_ids[] = array('id' => (int) $row['id'],
                                       'usage' => (int) $row['usage']);
                }
            }
        } catch (Exception $exc) {
            return array();
        }

        return $map_ids;
    } // fn GetMapIdsByArticle

    /**
     * Gives array of artilce's map's points: just point names (of the article object language) and usage flags.
     *
     * @param object Article
     * @return array
     */
    public static function GetLocationsByArticle($p_articleObj)
    {
        global $g_ado_db;

        $queryStr = 'SELECT lc.poi_name AS name, mll.poi_display AS display ';
        $queryStr .= 'FROM Maps AS m INNER JOIN MapLocations AS ml ON ml.fk_map_id = m.id ';
        $queryStr .= 'INNER JOIN MapLocationLanguages AS mll ON mll.fk_maplocation_id = ml.id ';
        $queryStr .= 'INNER JOIN LocationContents AS lc ON lc.id = mll.fk_content_id ';
        $queryStr .= 'WHERE m.fk_article_number = ? AND mll.fk_language_id = ? ORDER BY ml.rank, ml.id';

        $article_number = $p_articleObj->getArticleNumber();
        $language_id = $p_articleObj->getLanguageId();

        $poi_names = array();

        try
        {
            $sql_params = array();

            $sql_params[] = $article_number;
            $sql_params[] = $language_id;

            $rows = $g_ado_db->GetAll($queryStr, $sql_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $poi_names[] = array('name' => $row['name'], 'display' => $row['display']);
                }
            }
        }
        catch (Exception $exc)
        {
            return array();
        }

        return $poi_names;
    } // fn GetLocationsByArticle

    /**
     * Sets the article's map to be without an article link, to stay as a lone map.
     *
     * @param object Article
     * @param int
     * @return array
     */
    public static function UnlinkArticle($p_articleObj = null, $p_articleNumber = 0)
    {
        global $g_ado_db;

        $article_number = 0;

        if ($p_articleObj)
        {
            $article_number = $p_articleObj->getArticleNumber();
        }
        else
        {
            $article_number = $p_articleNumber;
        }
        if ((!$article_number) || (0 == $article_number)) {return;}

        $queryStr = 'UPDATE Maps SET fk_article_number = 0 WHERE fk_article_number = ?';

        try
        {
            $sel_params = array();
            $sel_params[] = $article_number;

            $g_ado_db->Execute($queryStr, $sel_params);
        }
        catch (Exception $exc)
        {
            return false;
        }

        return true;
    } // fn UnlinkArticle

    /**
     * This is called when the (last language of the) article is deleted
     * Remove map pointers to the given article.
     * After article removal (with all its languages), the map is preserved with just the last language.
     *
     * @param int $p_articleNumber
     *
     * @return void
     */
    public static function OnArticleDelete($p_articleNumber)
    {
        return Geo_Map::UnlinkArticle(null, $p_articleNumber);
    } // fn OnArticleDelete


    /**
     * This is called when a (non-last) language is deleted.
     * It will remove the links on location contents, and the possible free contents.
     * Finally left maps are just with the last language (that is not processed herein).
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @return void
     */
    public static function OnLanguageDelete($p_articleNumber, $p_languageId)
    {
        global $g_ado_db;

        $queryStr_sel = 'SELECT mll.id AS mll_id, mll.fk_content_id AS con_id FROM MapLocationLanguages AS mll ';
        $queryStr_sel .= 'INNER JOIN MapLocations AS ml ON mll.fk_maplocation_id = ml.id ';
        $queryStr_sel .= 'INNER JOIN Maps AS m ON m.id = ml.fk_map_id ';
        $queryStr_sel .= 'WHERE m.fk_article_number = ? AND mll.fk_language_id = ?';

        $list_fill = '%%id_list%%';
        $queryStr_mll_del = 'DELETE FROM MapLocationLanguages WHERE id IN (%%id_list%%)';

        $queryStr_con_del = 'DELETE FROM LocationContents WHERE id = ? AND NOT EXISTS (SELECT id FROM MapLocationLanguages WHERE fk_content_id = ?)';

        $mll_ids = array();
        $con_ids = array();

        try
        {
            $sel_params = array();
            $sel_params[] = $p_articleNumber;
            $sel_params[] = $p_languageId;

            $rows = $g_ado_db->GetAll($queryStr_sel, $sel_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $mll_ids[] = $row['mll_id'];
                    $con_ids[] = $row['con_id'];
                }
            }
        }
        catch (Exception $exc)
        {
            return false;
        }

        if (0 == count($mll_ids)) {return true;}

        $mll_string = implode(', ', $mll_ids);

        try
        {
            $queryStr_mll_del = str_replace($list_fill, $mll_string, $queryStr_mll_del);

            $g_ado_db->Execute($queryStr_mll_del);
        }
        catch (Exception $exc)
        {
            return false;
        }

        foreach ($con_ids as $con_id)
        {
            $del_params = array();
            $del_params[] = $con_id;
            $del_params[] = $con_id;

            try
            {
                $g_ado_db->Execute($queryStr_con_del, $del_params);
            }
            catch (Exception $exc)
            {
                return false;
            }
        }

        return true;
    } // fn OnLanguageDelete


    /**
     * Deletes the map with all its points, translations, and other associated data.
     * This does real removals, unlike the OnArticleDelete/UnlinkArticle methods.
     *
     * @return void
     */
    public function delete()
    {
        global $g_ado_db;

        $queryStr_sel = 'SELECT id FROM MapLocations WHERE fk_map_id = ?';

        $queryStr_del = 'DELETE FROM Maps WHERE id = ?';

        $ml_ids = array();
        try
        {
            $sel_params = array();
            $sel_params[] = $this->m_data['id'];

            $rows = $g_ado_db->GetAll($queryStr_sel, $sel_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $ml_ids[] = array('location_id' => $row['id']);
                }
            }
        }
        catch (Exception $exc)
        {
            return false;
        }

        Geo_Map::RemovePoints($this->m_data['id'], $ml_ids);

        try
        {
            $del_params = array();
            $del_params[] = $this->m_data['id'];

            $g_ado_db->Execute($queryStr_del, $del_params);
        }
        catch (Exception $exc)
        {
            return false;
        }

        return true;
    } // fn delete


    /**
     * Copy all the map-related pointers for the given article.
     *  - read and copy all links / basic data on:
     *    - maps (Map table) with links into the given src article, set link into dest art no.
     *    - points (MapLocation table) with links into read map ids, set link into new map ids
     *    - text contents (MapLocationLanguages table) with links into read maploc ids and into the given languages
     *    - multimedia (MapLocationMultimedia table) with links into read maploc ids
     *
     * @param int $p_srcArticleNumber
     * @param int $p_destArticleNumber
     * @param array $p_copyTranslations
     * @return void
     */
    public static function OnArticleCopy($p_srcArticleNumber, $p_destArticleNumber, $p_copyTranslations, $p_userId = null)
    {
        global $g_ado_db;
        $list_fill = '%%id_list%%';
        $lang_fill = '%%id_langs%%';

        $map_columns = array('fk_article_number', 'MapRank', 'MapUsage', 'MapCenterLongitude', 'MapCenterLatitude', 'MapDisplayResolution', 'MapProvider', 'MapWidth', 'MapHeight', 'MapName', 'IdUser');
        $map_colstr = implode(', ', $map_columns);
        $map_colqms = implode(', ', str_split(str_repeat('?', count($map_columns))));
        $queryStr_map_sel = "SELECT id, $map_colstr FROM Maps WHERE fk_article_number = ?";
        $queryStr_map_ins = "INSERT INTO Maps ($map_colstr) VALUES ($map_colqms)";

        $maploc_columns = array('fk_map_id', 'fk_location_id', 'poi_style', 'rank');
        $maploc_colstr = implode(', ', $maploc_columns);
        $maploc_colqms = implode(', ', str_split(str_repeat('?', count($maploc_columns))));
        $queryStr_maploc_sel = "SELECT id, $maploc_colstr FROM MapLocations WHERE fk_map_id IN (%%id_list%%)";
        $queryStr_maploc_ins = "INSERT INTO MapLocations ($maploc_colstr) VALUES ($maploc_colqms)";

        $maploclan_columns = array('fk_maplocation_id', 'fk_language_id', 'fk_content_id', 'poi_display');
        $maploclan_colstr = implode(', ', $maploclan_columns);
        $maploclan_colqms = implode(', ', str_split(str_repeat('?', count($maploclan_columns))));
        $queryStr_maploclan_sel = "SELECT $maploclan_colstr FROM MapLocationLanguages WHERE fk_maplocation_id IN (%%id_list%%) AND fk_language_id IN (%%id_langs%%)";
        $queryStr_maploclan_ins = "INSERT INTO MapLocationLanguages ($maploclan_colstr) VALUES ($maploclan_colqms)";

        $maplocmed_columns = array('fk_maplocation_id', 'fk_multimedia_id');
        $maplocmed_colstr = implode(', ', $maplocmed_columns);
        $maplocmed_colqms = implode(', ', str_split(str_repeat('?', count($maplocmed_columns))));
        $queryStr_maplocmed_sel = "SELECT $maplocmed_colstr FROM MapLocationMultimedia WHERE fk_maplocation_id IN (%%id_list%%)";
        $queryStr_maplocmed_ins = "INSERT INTO MapLocationMultimedia ($maplocmed_colstr) VALUES ($maplocmed_colqms)";

        if (0 == count($p_copyTranslations)) {return;}
        $lang_str = implode(', ', $p_copyTranslations);


        $map_ids = array();

        $map_sel_params = array();
        $map_sel_params[] = $p_srcArticleNumber;
        $rows = $g_ado_db->GetAll($queryStr_map_sel, $map_sel_params);
        foreach ($rows as $row) {
            $old_map_id = $row['id'];
            $new_user_id = $p_userId;
            if (is_null($new_user_id)) {$new_user_id = $row['IdUser'];}

            $map_ins_params = array();
            $map_ins_params[] = $p_destArticleNumber;
            $map_ins_params[] = $row['MapRank'];
            $map_ins_params[] = $row['MapUsage'];
            $map_ins_params[] = $row['MapCenterLongitude'];
            $map_ins_params[] = $row['MapCenterLatitude'];
            $map_ins_params[] = $row['MapDisplayResolution'];
            $map_ins_params[] = $row['MapProvider'];
            $map_ins_params[] = $row['MapWidth'];
            $map_ins_params[] = $row['MapHeight'];
            $map_ins_params[] = $row['MapName'];
            $map_ins_params[] = $new_user_id;

            $success = $g_ado_db->Execute($queryStr_map_ins, $map_ins_params);
            // taking the map ID
            $new_map_id = $g_ado_db->Insert_ID();
            $map_ids[$old_map_id] = $new_map_id;
        }
        if (0 == count($map_ids)) {return;}

        $map_ids_str = implode(', ', array_keys($map_ids));


        $queryStr_maploc_sel = str_replace($list_fill, $map_ids_str, $queryStr_maploc_sel);
        $maploc_ids = array();

        $maploc_sel_params = array();

        $rows = $g_ado_db->GetAll($queryStr_maploc_sel, $maploc_sel_params);
        foreach ($rows as $row) {
            $old_maploc_id = $row['id'];
            $old_map_id = $row['fk_map_id'];
            $new_map_id = $map_ids[$old_map_id];

            $maploc_ins_params = array();
            $maploc_ins_params[] = $new_map_id;
            $maploc_ins_params[] = $row['fk_location_id'];
            $maploc_ins_params[] = $row['poi_style'];
            $maploc_ins_params[] = $row['rank'];

            $success = $g_ado_db->Execute($queryStr_maploc_ins, $maploc_ins_params);
            // taking the map ID
            $new_maploc_id = $g_ado_db->Insert_ID();
            $maploc_ids[$old_maploc_id] = $new_maploc_id;
        }
        if (0 == count($maploc_ids)) {return;}

        $maploc_ids_str = implode(', ', array_keys($maploc_ids));


        $queryStr_maploclan_sel = str_replace($list_fill, $maploc_ids_str, $queryStr_maploclan_sel);
        $queryStr_maploclan_sel = str_replace($lang_fill, $lang_str, $queryStr_maploclan_sel);
        $maploclan_sel_params = array();

        $rows = $g_ado_db->GetAll($queryStr_maploclan_sel, $maploclan_sel_params);
        foreach ($rows as $row) {
            $old_maploc_id = $row['fk_maplocation_id'];
            $new_maploc_id = $maploc_ids[$old_maploc_id];

            $maploclan_ins_params = array();
            $maploclan_ins_params[] = $new_maploc_id;
            $maploclan_ins_params[] = $row['fk_language_id'];
            $maploclan_ins_params[] = $row['fk_content_id'];
            $maploclan_ins_params[] = $row['poi_display'];

            $success = $g_ado_db->Execute($queryStr_maploclan_ins, $maploclan_ins_params);
        }


        $queryStr_maplocmed_sel = str_replace($list_fill, $maploc_ids_str, $queryStr_maplocmed_sel);
        $maplocmed_sel_params = array();

        $rows = $g_ado_db->GetAll($queryStr_maplocmed_sel, $maplocmed_sel_params);
        foreach ($rows as $row) {
            $old_maploc_id = $row['fk_maplocation_id'];
            $new_maploc_id = $maploc_ids[$old_maploc_id];

            $maplocmed_ins_params = array();
            $maplocmed_ins_params[] = $new_maploc_id;
            $maplocmed_ins_params[] = $row['fk_multimedia_id'];

            $success = $g_ado_db->Execute($queryStr_maplocmed_ins, $maplocmed_ins_params);
        }


    } // fn OnArticleCopy
    /**

     * Copy all the pointers for the given article.
     * @param int $p_articleNumber
     * @param int $p_srcLanguageId
     * @param int $p_destLanguageId
     * @return void
     */
    public static function OnCreateTranslation($p_articleNumber, $p_srcLanguageId, $p_destLanguageId)
    {
        global $g_ado_db;

        $queryStr_sel = 'SELECT mll.fk_maplocation_id AS ml_id, mll.fk_content_id AS con_id, mll.poi_display AS display ';
        $queryStr_sel .= 'FROM Maps AS m INNER JOIN MapLocations AS ml ON ml.fk_map_id = m.id ';
        $queryStr_sel .= 'INNER JOIN MapLocationLanguages AS mll ON mll.fk_maplocation_id = ml.id ';
        $queryStr_sel .= 'WHERE m.fk_article_number = ? AND mll.fk_language_id = ?';

        $queryStr_ins = 'INSERT INTO MapLocationLanguages (fk_maplocation_id, fk_language_id, fk_content_id, poi_display) ';
        $queryStr_ins .= 'VALUES (?, ?, ?, ?)';

        $poi_names = array();

        try
        {
            $sel_params = array();
            $sel_params[] = $p_articleNumber;
            $sel_params[] = $p_srcLanguageId;

            $rows = $g_ado_db->GetAll($queryStr_sel, $sel_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $ins_params = array();
                    $ins_params[] = $row['ml_id'];
                    $ins_params[] = (int) $p_destLanguageId;
                    $ins_params[] = $row['con_id'];
                    $ins_params[] = 0;

                    $g_ado_db->Execute($queryStr_ins, $ins_params);
                }
            }
        }
        catch (Exception $exc)
        {
            return false;
        }

        return true;
    } // fn OnCreateTranslation


    // ajax processing handlers

    /**
     * Load map data
     *
     * @param int $p_mapId
     * @param int $p_languageId
     * @param int $p_articleNumber
     *
     * @return array
     */
    public static function LoadMapData($p_mapId, $p_languageId, $p_articleNumber, $p_preview = false, $p_textOnly = false)
    {
        $poi_count = 0;

        $p_constraints = array();

        $leftOperand = 'as_array';
        $rightOperand = true;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $p_constraints[] = $constraint;

        $leftOperand = 'active_only';
        $rightOperand = $p_preview;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $p_constraints[] = $constraint;

        $leftOperand = 'text_only';
        $rightOperand = $p_textOnly;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $p_constraints[] = $constraint;

        $leftOperand = 'language';
        $rightOperand = $p_languageId;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $p_constraints[] = $constraint;

        $leftOperand = 'map';
        $rightOperand = $p_mapId;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $p_constraints[] = $constraint;

        $pois = array();
        $poi_objs = Geo_MapLocation::GetListExt($p_constraints, (array) null, 0, 0, $poi_count, false, $pois);
        return array(
            'pois' => $pois,
            'map' => Geo_Map::ReadMapInfo('map', (int) $p_mapId),
        );
    } // fn LoadMapData

    /**
     * The main dispatcher for ajax based editing of maps
     *
     * @param int $p_mapId
     * @param int $p_languageId
     * @param int $p_articleNumber
     * @param mixed $p_map
     * @param mixed $p_remove
     * @param mixed $p_insert
     * @param mixed $p_locations
     * @param mixed $p_contents
     * @param mixed $p_order
     *
     * @return array
     */
    public static function StoreMapData($p_mapId, $p_languageId, $p_articleNumber, $p_map = '', $p_remove = '', $p_insert = '', $p_locations = '', $p_contents = '', $p_order = '')
    {
        $security_problem = array('status' => '403', 'description' => 'Invalid security token!');
        $unknown_request = array('status' => '404', 'description' => 'Unknown request!');
        $data_wrong = array('status' => '404', 'description' => 'Wrong data.');


        $status = true;

        if ('' != $p_map)
        {
            $map_data = array();
            try
            {
                $p_map = str_replace('%2B', '+', $p_map);
                $p_map = str_replace('%2F', '/', $p_map);
                $map_json = base64_decode($p_map);

                $map_data = json_decode($map_json);
            }
            catch (Exception $exc)
            {
                $status = false;
            }
            if ($status)
            {
                $status = Geo_Map::UpdateMap($p_mapId, $p_articleNumber, $map_data);
            }
        }

        if (!$status)
        {
            return $data_wrong;
        }

        if ('' != $p_remove)
        {
            $remove_data = array();
            try
            {
                $p_remove = str_replace('%2B', '+', $p_remove);
                $p_remove = str_replace('%2F', '/', $p_remove);
                $remove_json = base64_decode($p_remove);
                $remove_data = json_decode($remove_json);
            }
            catch (Exception $exc)
            {
                $status = false;
            }
            if ($status)
            {
                $status = Geo_Map::RemovePoints($p_mapId, $remove_data);
            }
        }

        if (!$status)
        {
            return $data_wrong;
        }

        $new_ids = array();
        if ('' != $p_insert)
        {
            $insert_data = array();
            try
            {
                $p_insert = str_replace('%2B', '+', $p_insert);
                $p_insert = str_replace('%2F', '/', $p_insert);
                $insert_json = base64_decode($p_insert);

                $insert_data = json_decode($insert_json);
            }
            catch (Exception $exc)
            {
                $status = false;
            }
            if ($status)
            {
                $status = Geo_Map::InsertPoints($p_mapId, $p_languageId, $p_articleNumber, $insert_data, $new_ids);

            }
        }


        if (!$status)
        {
            return $data_wrong;
        }


        if ('' != $p_locations)
        {
            $locations_data = array();
            try
            {
                $p_locations = str_replace('%2B', '+', $p_locations);
                $p_locations = str_replace('%2F', '/', $p_locations);
                $locations_json = base64_decode($p_locations);
                $locations_data = json_decode($locations_json);
            }
            catch (Exception $exc)
            {
                $status = false;
            }
            if ($status)
            {

                $status = Geo_Location::UpdateLocations($p_mapId, $locations_data);
            }
        }

        if (!$status)
        {
            return $data_wrong;
        }


        if ('' != $p_contents)
        {
            $contents_data = array();
            try
            {

                $p_contents = str_replace('%2B', '+', $p_contents);
                $p_contents = str_replace('%2F', '/', $p_contents);
                $contents_json = base64_decode($p_contents);

                $contents_data = json_decode($contents_json);
            }
            catch (Exception $exc)
            {
                $status = false;
            }
            if ($status)
            {
                $status = Geo_Location::UpdateContents($p_mapId, $contents_data);
            }
        }

        if (!$status)
        {
            return $data_wrong;
        }

        if ('' != $p_order)
        {
            $order_data = array();
            try
            {
                $p_order = str_replace('%2B', '+', $p_order);
                $p_order = str_replace('%2F', '/', $p_order);
                $order_json = base64_decode($p_order);
                $order_data = json_decode($order_json);
            }
            catch (Exception $exc)
            {
                $status = false;
            }
            if ($status)
            {
                $status = Geo_Location::UpdateOrder($p_mapId, $order_data, $new_ids);
            }
        }

        if (!$status)
        {
            return $data_wrong;
        }

        $geo_map_usage = Geo_Map::ReadMapInfo('map', $p_mapId);

        $poi_count = 0;

        $p_constraints = array();

        $leftOperand = 'as_array';
        $rightOperand = true;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $p_constraints[] = $constraint;

        $leftOperand = 'preview';
        $rightOperand = false;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $p_constraints[] = $constraint;

        $leftOperand = 'text_only';
        $rightOperand = false;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $p_constraints[] = $constraint;

        $leftOperand = 'language';
        $rightOperand = $p_languageId;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $p_constraints[] = $constraint;

        $leftOperand = 'map';
        $rightOperand = $p_mapId;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $p_constraints[] = $constraint;

        $found_list = array();
        $found_objs = Geo_MapLocation::GetListExt($p_constraints, (array) null, 0, 0, $poi_count, false, $found_list);

        $res_array = array('status' => '200', 'pois' => $found_list, 'map' => $geo_map_usage);

        return $res_array;
    } // fn StoreMapData

    // the functions for map editing are below

    /**
     * Provides general information on a map, specified by map id or article number
     *
     * @param string $p_type
     * @param int $p_id
     *
     * @return array
     */
    public static function ReadMapInfo($p_type, $p_id)
    {
        global $g_ado_db;

        $map_info = array();
        $map_info['id'] = 0;

        $queryStr_common = 'SELECT id, MapCenterLongitude AS lon, MapCenterLatitude AS lat, MapDisplayResolution AS res, MapProvider AS prov, MapWidth AS width, MapHeight AS height, MapName AS name ';
        $queryStr_common .= 'FROM Maps ';

        $queryStr_art = $queryStr_common;
        $queryStr_art .= 'WHERE fk_article_number = ? AND MapUsage = 1 ORDER BY MapRank, id LIMIT 1';

        $queryStr_map = $queryStr_common;
        $queryStr_map .= 'WHERE id = ?';

        $queryStr = '';
        $sql_params = array();
        $sql_params[] = $p_id;
        if ('map' == $p_type)
        {
            $queryStr = $queryStr_map;
        }
        else
        {
            $queryStr = $queryStr_art;
        }

        try {
            $rows = $g_ado_db->GetAll($queryStr, $sql_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $map_info = array();
                    $map_info['id'] = $row['id'];
                    $map_info['lon'] = $row['lon'];
                    $map_info['lat'] = $row['lat'];
                    $map_info['res'] = $row['res'];
                    $map_info['prov'] = $row['prov'];
                    $map_info['width'] = $row['width'];
                    $map_info['height'] = $row['height'];
                    $map_info['name'] = $row['name'];
                }
            }
        }
        catch (Exception $exc)
        {
            return false;
        }

        return $map_info;
    } // fn ReadMapInfo


    public static function ReadMultiMapInfo()
    {
        $geo_map_info = Geo_Preferences::GetMapInfo();
        $map_data = $geo_map_info['json_obj'];

        $map_info = array();
        $map_info['id'] = 0;
        $map_info['lon'] = $map_data['longitude'];
        $map_info['lat'] = $map_data['latitude'];
        $map_info['res'] = $map_data['resolution'];
        $map_info['prov'] = $map_data['default'];
        $map_info['width'] = $map_data['width'];
        $map_info['height'] = $map_data['height'];
        $map_info['name'] = 'Multimap';

        return $map_info;
    }

    /**
     * Gives languages used at the map text contents
     *
     * @param int $p_mapId
     *
     * @return array
     */
    public static function ReadLanguagesByMap($p_mapId)
    {
        global $g_ado_db;

        $queryStr_langs = 'SELECT mll.fk_language_id AS lang FROM MapLocationLanguages AS mll ';
        $queryStr_langs .= 'INNER JOIN MapLocations AS ml ON mll.fk_maplocation_id = ml.id ';
        $queryStr_langs .= 'INNER JOIN Maps AS m ON ml.fk_map_id = m.id ';
        $queryStr_langs .= 'WHERE m.id = ?';

        $map_langs_arr = array();
        {
            $langs_params = array();
            $langs_params[] = $p_mapId;

            $rows = $g_ado_db->GetAll($queryStr_langs, $langs_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $map_langs_arr[] = $row['lang'];
                }
            }
        }

        return $map_langs_arr;
    } // fn ReadLanguagesByMap

    /**
     * Gives languages used at the map's article
     *
     * @param int $p_articleNumber
     *
     * @return array
     */
    public static function ReadLanguagesByArticle($p_articleNumber)
    {
        global $g_ado_db;

        $queryStr_langs = 'SELECT IdLanguage AS lang FROM Articles WHERE Number = ?';

        $art_langs_arr = array();
        {
            $langs_params = array();
            $langs_params[] = $p_articleNumber;

            $rows = $g_ado_db->GetAll($queryStr_langs, $langs_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $art_langs_arr[] = $row['lang'];
                }
            }
        }

        return $art_langs_arr;
    } // fn ReadLanguagesByArticle

    /**
     * Gives id of article's map id
     *
     * @param int $p_articleNumber
     * @param int $p_rank
     *
     * @return int
     */
    public static function ReadMapId($p_articleNumber, $p_rank = 1)
    {
        global $g_ado_db;

        // for testing whether the map was already created
        $queryStr_map_id = 'SELECT id AS map FROM Maps WHERE fk_article_number = ? AND MapRank = ? ORDER BY id LIMIT 1';

        $map_id = 0;

        {
            try
            {
                $map_id_params = array();
                $map_id_params[] = $p_articleNumber;
                $map_id_params[] = $p_rank;

                $rows = $g_ado_db->GetAll($queryStr_map_id, $map_id_params);
                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        $map_id = $row['map'];
                    }
                }

            }
            catch (Exception $exc)
            {
                return false;
            }

        }

        return $map_id;
    } // fn ReadMapId

    /**
     * Updates the basic information on the map.
     * If the p_mapId is not set, a new map is created (and the p_mapId is set then)
     * on the p_articleNumber article.
     *
     * @param int $p_mapId
     * @param int $p_articleNumber
     * @param array $p_map
     *
     * @return int
     */
    public static function UpdateMap(&$p_mapId, $p_articleNumber = 0, $p_map)
    {
        global $g_ado_db;
        global $g_user;

        if (is_object($p_map))
        {
            $p_map = get_object_vars($p_map);
        }
        // creating a new map, if the map does not exist yet
        $queryStr_map_new = 'INSERT INTO Maps (MapCenterLongitude, MapCenterLatitude, MapDisplayResolution, MapProvider, MapWidth, MapHeight, MapName, MapRank, fk_article_number, IdUser) ';
        $queryStr_map_new .= 'VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, %%user_id%%)';

        // update the map, if it already exists
        $queryStr_map_up = 'UPDATE Maps SET MapCenterLongitude = ?, MapCenterLatitude = ?, MapDisplayResolution = ?, MapProvider = ?, MapWidth = ?, MapHeight = ?, MapName = ? WHERE id = ?';


        $map_val_params = array();
        $map_val_params[] = $p_map['cen_lon'];
        $map_val_params[] = $p_map['cen_lat'];
        $map_val_params[] = $p_map['zoom'];
        $map_val_params[] = $p_map['provider'];
        $map_val_params[] = $p_map['width'];
        $map_val_params[] = $p_map['height'];
        $map_val_params[] = $p_map['name'];
        if (0 == $p_mapId)
        {
            // the way for a new map
            try
            {
                $map_val_params[] = $p_articleNumber;
                // create the new map

                // it has to be safe to use the user id directly
                $queryStr_map_new = str_replace('%%user_id%%', $g_user->getUserId(), $queryStr_map_new);

                $success = $g_ado_db->Execute($queryStr_map_new, $map_val_params);
                // taking the map ID
                $p_mapId = $g_ado_db->Insert_ID();
            }
            catch (Exception $exc)
            {
                return false;
            }
        }
        else
        {
            // the way for an already existing map
            try
            {
                $map_val_params[] = $p_mapId;
                // update the map values
                $success = $g_ado_db->Execute($queryStr_map_up, $map_val_params);
            }
            catch (Exception $exc)
            {
                return false;
            }

        }

        return $p_mapId;
    } // fn UpdateMap

    /**
     * Removes points (with locations and other contents) from the map.
     *
     * @param int $p_mapId
     * @param array $p_removal
     *
     * @return bool
     */
    public static function RemovePoints($p_mapId, $p_removal)
    {
        global $g_ado_db;

/*
    A)
        1) provided: map_id, list of maploc_ids

    B)
        1) read all location_id from MapLocations where id=maploc_id;
        2) read all content_id from MapLocationLanguages where maploc_id;
        3) read all multimedia_id from MapLocationMultimedia where maploc_id;

    C)
            1) delete from MapLocations where there are: id=maploc_id
            2) delete from MapLocationLanguages where there are: maploc_id
            3) delete from MapLocationMultimedia where fk_maplocation_id=maploc_id
    D)
        cycle:
            1) delete from Locations where there are: id=location_id, and there is no location_id at MapLocations
            2) delete from LocationContents where there are: id=content_id and there is no content_id at MapLocationLanguages
            2) delete from Multimedia where there are: id=multimedia_id and there is no multimedia_id at MapLocationMultimedia

*/

        $map_loc_ids = array();
        foreach ($p_removal as $one_rem)
        {
            if (is_object($one_rem))
            {
                $one_rem = get_object_vars($one_rem);
            }

            $val_rem = $one_rem['location_id'];
            if (is_numeric($val_rem))
            {
                $id_rem = 0 + $val_rem;
                if (is_int($id_rem)) {$map_loc_ids[] = $id_rem;}
            }
        }
        if (0 == count($map_loc_ids)) {return 0;}

        $map_loc_list = implode(', ', $map_loc_ids);

        $list_fill = '%%id_list%%';
        // ad B 1)
        $queryStr_maploc_sel = 'SELECT fk_location_id AS loc FROM MapLocations WHERE id IN (%%id_list%%)';
        // ad B 2)
        $queryStr_maploclan_sel = 'SELECT fk_content_id AS con FROM MapLocationLanguages WHERE fk_maplocation_id IN (%%id_list%%)';
        // ad B 3)
        $queryStr_maplocmed_sel = 'SELECT fk_multimedia_id AS med FROM MapLocationMultimedia WHERE fk_maplocation_id IN (%%id_list%%)';


        // ad C 1)
        $queryStr_maploc_del = 'DELETE FROM MapLocations WHERE id IN (%%id_list%%)';
        // ad C 2)
        $queryStr_maploclan_del = 'DELETE FROM MapLocationLanguages WHERE fk_maplocation_id IN (%%id_list%%)';
        // ad C 3)
        $queryStr_maplocmed_del = 'DELETE FROM MapLocationMultimedia WHERE fk_maplocation_id IN (%%id_list%%)';

        // ad D 1)
        $queryStr_locpos_del = 'DELETE FROM Locations WHERE id = ? AND NOT EXISTS (SELECT id FROM MapLocations WHERE fk_location_id = ?)';
        // ad D 2)
        $queryStr_loccon_del = 'DELETE FROM LocationContents WHERE id = ? AND NOT EXISTS (SELECT id FROM MapLocationLanguages WHERE fk_content_id = ?)';
        // ad D 3)
        $queryStr_locmed_del = 'DELETE FROM Multimedia WHERE id = ? AND NOT EXISTS (SELECT id FROM MapLocationMultimedia WHERE fk_multimedia_id = ?)';

        // ad B1)
        $location_ids = array();
        try
        {
            $maploc_sel_params = array();

            $queryStr_maploc_sel = str_replace($list_fill, $map_loc_list, $queryStr_maploc_sel);

            $rows = $g_ado_db->GetAll($queryStr_maploc_sel, $maploc_sel_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $location_ids[] = $row['loc'];
                }
            }
        }
        catch (Exception $exc)
        {
            return false;
        }

        // ad B2)
        $content_ids = array();
        try
        {
            $maploclan_sel_params = array();

            $queryStr_maploclan_sel = str_replace($list_fill, $map_loc_list, $queryStr_maploclan_sel);

            $rows = $g_ado_db->GetAll($queryStr_maploclan_sel, $maploclan_sel_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $content_ids[] = $row['con'];
                }
            }
        }
        catch (Exception $exc)
        {
            return false;
        }

        // ad B3)
        $media_ids = array();
        try
        {
            $maplocmed_sel_params = array();

            $queryStr_maplocmed_sel = str_replace($list_fill, $map_loc_list, $queryStr_maplocmed_sel);

            $rows = $g_ado_db->GetAll($queryStr_maplocmed_sel, $maplocmed_sel_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $media_ids[] = $row['med'];
                }
            }
        }
        catch (Exception $exc)
        {
            return false;
        }

        // ad C 1)
        try
        {
            $maploc_del_params = array();

            $queryStr_maploc_del = str_replace($list_fill, $map_loc_list, $queryStr_maploc_del);

            $success = $g_ado_db->Execute($queryStr_maploc_del, $maploc_del_params);
        }
        catch (Exception $exc)
        {
            return false;
        }

        // ad C 2)
        try
        {
            $maploclan_del_params = array();

            $queryStr_maploclan_del = str_replace($list_fill, $map_loc_list, $queryStr_maploclan_del);

            $success = $g_ado_db->Execute($queryStr_maploclan_del, $maploclan_del_params);
        }
        catch (Exception $exc)
        {
            return false;
        }

        // ad C 3)
        try
        {
            $maplocmed_del_params = array();

            $queryStr_maplocmed_del = str_replace($list_fill, $map_loc_list, $queryStr_maplocmed_del);

            $success = $g_ado_db->Execute($queryStr_maplocmed_del, $maplocmed_del_params);
        }
        catch (Exception $exc)
        {
            return false;
        }

        // ad D 1)
        try
        {
            foreach ($location_ids as $one_loc)
            {
                $locpos_del_params = array();
                $locpos_del_params[] = $one_loc;
                $locpos_del_params[] = $one_loc;

                $success = $g_ado_db->Execute($queryStr_locpos_del, $locpos_del_params);
            }
        }
        catch (Exception $exc)
        {
            return false;
        }

        // ad D 2)
        try
        {
            foreach ($content_ids as $one_con)
            {
                $loccon_del_params = array();
                $loccon_del_params[] = $one_con;
                $loccon_del_params[] = $one_con;

                $success = $g_ado_db->Execute($queryStr_loccon_del, $loccon_del_params);
            }
        }
        catch (Exception $exc)
        {
            return false;
        }

        // ad D 3)
        try
        {
            foreach ($media_ids as $one_med)
            {
                $locmed_del_params = array();
                $locmed_del_params[] = $one_med;
                $locmed_del_params[] = $one_med;

                $success = $g_ado_db->Execute($queryStr_locmed_del, $locmed_del_params);
            }
        }
        catch (Exception $exc)
        {
            return false;
        }

        return true;

    } // fn RemovePoints


    /**
     * Inserts points (with locations and other contents) into the map.
     * NB: The result indices are used at the point order updating, since that order-updating
     * would not know id's of the new points otherwise.
     *
     * @param int $p_mapId
     * @param int $p_languageId
     * @param int $p_articleNumber
     * @param array $p_insertion
     * @param array $p_indices
     *
     * @return array
     */
    public static function InsertPoints($p_mapId, $p_languageId, $p_articleNumber, $p_insertion, &$p_indices)
    {
        global $g_ado_db;
        global $g_user;

        // this should not happen
        if (0 == $p_mapId) {return array();}

/*
    A)
        1) given article_number, language_id, map_id, list of new data
        2) read languages of the article

    B)
        cycle:
            1) insert into Locations the new position
            2) get inserted id as location_id
            3) insert into LocationContents the most of the new data,
            4) get inserted id as content_id
            5) insert into MapLocations (map_id, location_id, data:style, rank=0)
            6) get inserted id as maplocation_id
            7) insert into MapLocationLanguages (maplocation_id, language_id, content_id, data:display)
            ad 7) this for all languages, with display=false for the other ones
*/

        // ad B 1)
        $queryStr_loc_in = 'INSERT INTO Locations (poi_location, poi_type, poi_type_style, poi_center, poi_radius, IdUser) VALUES (';
        $queryStr_loc_in .= "GeomFromText('POINT(? ?)'), 'point', 0, PointFromText('POINT(? ?)'), 0, %%user_id%%";
        $queryStr_loc_in .= ')';
        // ad B 3)
        // ad B 5)
        $queryStr_maploc = 'INSERT INTO MapLocations (fk_map_id, fk_location_id, poi_style, rank) ';
        $queryStr_maploc .= 'VALUES (?, ?, ?, 0)';
        // ad B 7)
        $queryStr_maploclan = 'INSERT INTO MapLocationLanguages (fk_maplocation_id, fk_language_id, fk_content_id, poi_display) ';
        $queryStr_maploclan .= 'VALUES (?, ?, ?, ?)';


        if ($p_articleNumber)
        {
            $languages = Geo_Map::ReadLanguagesByArticle($p_articleNumber);
        }
        else
        {
            $languages = Geo_Map::ReadLanguagesByMap($p_mapId);
        }

        foreach ($p_insertion as $poi)
        {
            if (is_object($poi)) {$poi = get_object_vars($poi);}

            {

                $loc_id = null;

                $new_loc = array();
                $new_loc[] = array('latitude' => $poi['latitude'], 'longitude' => $poi['longitude']);
                $new_cen = array('latitude' => $poi['latitude'], 'longitude' => $poi['longitude']);
                $new_style = 0;
                $new_radius = 0;
                $reuse_id = Geo_Location::FindLocation($new_loc, 'point', $new_style, $new_cen, $new_radius);

                if ($reuse_id && (0 < $reuse_id))
                {
                    $loc_id = $reuse_id;
                }
                else
                {
                    // ad B 1)
                    $loc_in_params = array();
                    $loc_in_params[] = $poi['latitude'];

                    $loc_in_params[] = $poi['longitude'];
                    $loc_in_params[] = $poi['latitude'];
                    $loc_in_params[] = $poi['longitude'];

                    // the POI itself insertion
                    $queryStr_loc_in = str_replace('%%user_id%%', $g_user->getUserId(), $queryStr_loc_in);

                    $success = $g_ado_db->Execute($queryStr_loc_in, $loc_in_params);
                    // ad B 2)
                    // taking its ID for the next processing
                    $loc_id = $g_ado_db->Insert_ID();
                }

                // ad B 3/4)
                $con_id = Geo_MapLocationContent::InsertContent($poi);

                // ad B 5)
                $maploc_params = array();
                $maploc_params[] = $p_mapId;
                $maploc_params[] = $loc_id;
                $maploc_params[] = '' . $poi['style'];
                // the map-point link insertion
                $success = $g_ado_db->Execute($queryStr_maploc, $maploc_params);

                // ad B 6)
                $maploc_id = $g_ado_db->Insert_ID();

                Geo_Multimedia::InsertMultimedia($maploc_id, $poi);

                // ad B 7)

                $maploclan_params = array();
                $maploclan_params[] = $maploc_id;
                $maploclan_params[] = 0 + $p_languageId;
                $maploclan_params[] = $con_id;
                $maploclan_params[] = 0 + $poi['display'];
                // the map-point link insertion
                $success = $g_ado_db->Execute($queryStr_maploclan, $maploclan_params);

                $poi_index = $poi['index'];

                $p_indices[$poi_index] = array('maploc' => $maploc_id);

                // insert the POI content for the other article's languages
                foreach ($languages as $one_lang)
                {
                    if ($one_lang == $p_languageId) {continue;}

                    $maploclan_params[1] = $one_lang;
                    $maploclan_params[3] = 0; // false; // display;

                    $success = $g_ado_db->Execute($queryStr_maploclan, $maploclan_params);
                }

                // if a new POI, then that's all for it here
                continue;
            }

        }

        return $p_indices;
    } // fn InsertPoints



    // presentation functions

    /**
     * Gives the large map opener part of the header part for all the presentation maps
     *
     * @param string $p_mapSuffix
     * @param int $p_widthLargeMap
     * @param int $p_heightLargeMap
     * @param string $p_labelLargeMap
     * @param string $p_tagStringPrev
     * @param string $p_tagStringBody
     *
     * @return string
     */
    private static function GetLargeMapOpener($p_mapSuffix, $p_widthLargeMap, $p_heightLargeMap, $p_labelLargeMap, $p_tagStringPrev, $p_tagStringBody)
    {
        $tag_string_fin = '';

        $tag_string_fin .= '
<script>
window.map_win_popup = null;
window.geo_open_large_map' . $p_mapSuffix . ' = function(params)
{
    window.deferred_poi_select = null;
    var select_poi = null;
    if (undefined !== params) {
        if (undefined !== params["select_poi"]) {
            select_poi = params["select_poi"];
            window.deferred_poi_select = select_poi;
        }
    }

    var already_focused = false;
    try {
        if (window.map_win_popup) {
            if ("' . $p_mapSuffix . '" == window.map_win_popup.map_obj_specifier) {
                setTimeout("try {window.map_win_popup.focus();} catch(e) {}", 0);
                if (null !== select_poi) {
                    window.point_large_map_center' . $p_mapSuffix . '(select_poi, true);
                }
                already_focused = true;
            }
        }
    } catch (e) {already_focused = false;}

    if (window.map_win_popup && window.map_win_popup.closed) {
        already_focused = false;
    }

    if (already_focused) {return;}

    window.map_win_popup = window.open("", "map_win_popup", "width=' . $p_widthLargeMap . ', height=' . $p_heightLargeMap . ',directories=0,location=0,menubar=0,toolbar=0,resizable=1");

    window.map_win_popup.document.write("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
    window.map_win_popup.document.write("<html xmlns=\"http://www.w3.org/1999/xhtml\">\n");
    window.map_win_popup.document.write("<head profile=\"http://gmpg.org/xfn/11\">\n");
    window.map_win_popup.document.write("<title>' . $p_labelLargeMap . '</title>\n");
    window.map_win_popup.document.write("<" + "script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js\"><" + "/script>\n");
    window.map_win_popup.document.write("\n");
    window.map_win_popup.document.write("\n");
';

$header_part = '
        <script type="text/javascript">
        window.set_map_popup_at_opener = function () {
            if (window.opener && (undefined !== window.opener.map_win_popup) && (!window.opener.map_win_popup)) {
                window.opener.map_win_popup = window;
            }
        };
        setInterval ("window.set_map_popup_at_opener();", 1000);
        //setInterval ("window.set_map_popup_at_opener();", 500);
        </script>
';

        foreach (explode("\n", $header_part) as $tag_string_line) {
            $tag_string_line = str_replace("\\", "\\\\", trim($tag_string_line));
            $tag_string_line = str_replace("\"", "\\\"", trim($tag_string_line));
            $tag_string_line = str_replace("<script", "<\" + \"script", trim($tag_string_line));
            $tag_string_line = str_replace("</script", "<\" + \"/script", trim($tag_string_line));
            $tag_string_fin .= 'window.map_win_popup.document.write("' . $tag_string_line . '" + "\n");' . "\n";
        }

        $tag_string_fin .= '
';

        $tag_string_fin .= 'window.map_win_popup.document.write("<script type=\"text/javascript\">" + "\n")' . "\n";
        $tag_string_fin .= 'window.map_win_popup.document.write("window.map_prepared = false;\n");' . "\n";
        $tag_string_fin .= 'window.map_win_popup.document.write("window.map_popup_win = true;\n");' . "\n";
        $tag_string_fin .= 'window.map_win_popup.document.write("window.map_obj_specifier = \"' . $p_mapSuffix . '\";\n");' . "\n";
        $tag_string_fin .= 'window.map_win_popup.document.write("window.onunload = function () {window.map_obj_specifier = null; window.map_prepared = false;}\n");' . "\n";

        $tag_string_fin .= 'window.map_win_popup.document.write("window.deferred_action = function() {if (null !== window.opener.deferred_poi_select) {window.geo_object' . $p_mapSuffix . '.proc_subst_action({select_poi: window.opener.deferred_poi_select});}}\n");' . "\n";

        $tag_string_fin .= 'window.map_win_popup.document.write("<" + "/script>" + "\n");' . "\n";

        foreach (explode("\n", $p_tagStringPrev) as $tag_string_line) {
            $tag_string_line = str_replace("\\", "\\\\", trim($tag_string_line));
            $tag_string_line = str_replace("\"", "\\\"", trim($tag_string_line));
            $tag_string_line = str_replace("<script", "<\" + \"script", trim($tag_string_line));
            $tag_string_line = str_replace("</script", "<\" + \"/script", trim($tag_string_line));
            $tag_string_fin .= 'window.map_win_popup.document.write("' . $tag_string_line . '" + "\n");' . "\n";
        }

        $tag_string_fin .= "\n";

        $tag_string_fin .= 'window.map_win_popup.document.write("<script type=\"text/javascript\">" + "\n")' . "\n";
        $tag_string_fin .= 'window.map_win_popup.document.write("setInterval(\"geo_object' . $p_mapSuffix . '.try_size_updated()\", 1000);\n");' . "\n";
        $tag_string_fin .= 'window.map_win_popup.document.write("<" + "/script>" + "\n");' . "\n";

        $tag_string_fin .= '
    window.map_win_popup.document.write("</head>\n");
    window.map_win_popup.document.write("<body>\n");
';

        $tag_string_fin .= 'window.map_win_popup.document.write("<div id=\"map_body_holder\" class=\"geomap_body_holder\">\n");';
        foreach (explode("\n", $p_tagStringBody) as $tag_string_line) {
            $tag_string_line = str_replace("\\", "\\\\", trim($tag_string_line));
            $tag_string_line = str_replace("\"", "\\\"", trim($tag_string_line));
            $tag_string_line = str_replace("<script", "<\" + \"script", trim($tag_string_line));
            $tag_string_line = str_replace("</script", "<\" + \"/script", trim($tag_string_line));
            $tag_string_fin .= 'window.map_win_popup.document.write("' . $tag_string_line . '" + "\n");' . "\n";
        }
        $tag_string_fin .= 'window.map_win_popup.document.write("</div>\n");';

        $tag_string_fin .= '
    window.map_win_popup.document.write("</body></html>");
    window.map_win_popup.document.close();
}
</script>
';

        return $tag_string_fin;
    } // fn GetLargeMapOpener

    /**
     * Gives the header part for the map front end presentation
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @param int $p_mapWidth
     * @param int $p_mapHeight
     * @param array $p_options
     *
     * @return string
     */
    public static function GetMapTagHeader($p_articleNumber, $p_languageId, $p_mapWidth = 0, $p_mapHeight = 0, $p_options = null)
    {
        global $Campsite;
        $tag_string = '';
        $tag_string_top = '';
        $tag_string_gv3_async = false;
        $tag_string_ini = '';
        $tag_string_mid = '';
        $tag_string_fin = '';

        $auto_focus = null;
        $max_zoom = null;
        $map_margin = null;
        $load_common = true;

        if (is_array($p_options)) {
            if (array_key_exists('auto_focus', $p_options)) {
                $auto_focus = $p_options['auto_focus'];
            }
            if (array_key_exists('max_zoom', $p_options)) {
                $max_zoom = $p_options['max_zoom'];
            }
            if (array_key_exists('map_margin', $p_options)) {
                $map_margin = $p_options['map_margin'];
            }
            if (array_key_exists('load_common', $p_options)) {
                $load_common = $p_options['load_common'];
            }
        }

        $large_map_on_click = false;
        $open_large_map = false;
        $width_large_map = 800;
        $height_large_map = 600;
        $label_large_map = '';
        if (is_array($p_options)) {
            if (array_key_exists('large_map_on_click', $p_options)) {
                $large_map_on_click = $p_options['large_map_on_click'];
            }
            if (array_key_exists('large_map_open', $p_options)) {
                $open_large_map = $p_options['large_map_open'];
            }
            if ($large_map_on_click && (!$open_large_map)) {
                $open_large_map = true;
            }
            if (array_key_exists('large_map_width', $p_options)) {
                $width_large_map_param = 0 + $p_options['large_map_width'];
                if (0 < $width_large_map_param) {
                    $width_large_map = $width_large_map_param;
                }
            }
            if (array_key_exists('large_map_height', $p_options)) {
                $height_large_map_param = 0 + $p_options['large_map_height'];
                if (0 < $height_large_map_param) {
                    $height_large_map = $height_large_map_param;
                }
            }
        }

        $f_article_number = $p_articleNumber;
        $f_language_id = $p_languageId;

        $map_suffix = '_' . $f_article_number . '_' . $f_language_id;

        $cnf_html_dir = $Campsite['HTML_DIR'];
        $cnf_website_url = $Campsite['WEBSITE_URL'];

        $geo_map_usage = Geo_Map::ReadMapInfo('article', $f_article_number);
        if (0 < $p_mapWidth) {
            $geo_map_usage['width'] = $p_mapWidth;
        }
        if (0 < $p_mapHeight) {
            $geo_map_usage['height'] = $p_mapHeight;
        }
        if ($geo_map_usage['name']) {
            $label_large_map = $geo_map_usage['name'];
        }

        $geo_map_usage_json = '';
        $geo_map_usage_json .= json_encode($geo_map_usage);

        $geo_map_info = Geo_Preferences::GetMapInfo($cnf_html_dir, $cnf_website_url, $geo_map_usage['prov']);
        $geo_map_incl = Geo_Preferences::PrepareMapIncludes($geo_map_info['incl_obj']);
        $geo_map_incl_async_arr = $geo_map_info['incl_obj_async'];
        $geo_map_incl_async_init = $geo_map_info['incl_gv3_init'];

        $tag_string_gv3_async = $geo_map_info['incl_gv3'];
        $geo_map_json = '';
        $geo_map_json .= json_encode($geo_map_info['json_obj']);

        $geo_icons_info = Geo_Preferences::GetIconsInfo($cnf_html_dir, $cnf_website_url);
        $geo_icons_json = '';
        $geo_icons_json .= json_encode($geo_icons_info['json_obj']);

        $geo_popups_info = Geo_Preferences::GetPopupsInfo($cnf_html_dir, $cnf_website_url);
        $geo_popups_json = '';
        $geo_popups_json .= json_encode($geo_popups_info['json_obj']);

        $geo_focus_info = Geo_Preferences::GetFocusInfo($cnf_html_dir, $cnf_website_url);
        if (null !== $auto_focus)
        {
            $geo_focus_info['json_obj']['auto_focus'] = $auto_focus;
        }
        if (null !== $max_zoom)
        {
            $geo_focus_info['json_obj']['max_zoom'] = $max_zoom;
        }
        if (null !== $map_margin)
        {
            $geo_focus_info['json_obj']['border'] = $map_margin;
        }
        $geo_focus_json = '';
        $geo_focus_json .= json_encode($geo_focus_info['json_obj']);
        
        $map_id = Geo_Map::GetMapIdByArticle($f_article_number);

        $preview = true;
        $poi_info = Geo_Map::LoadMapData($map_id, $f_language_id, $f_article_number, $preview);

        $poi_info_json = json_encode($poi_info);

        $geocodingdir = $Campsite['WEBSITE_URL'] . '/js/geocoding/';

        // map-provider specific includes shall be taken for all maps, since those maps can use different map providers
        $tag_string_top .= $geo_map_incl;
        $tag_string_top .= "\n";

        $check_gv3 = 'false';
        if ($tag_string_gv3_async) {
            $check_gv3 = 'true';
        }
        $check_gv3_init = 'false';
        if ('' != $geo_map_incl_async_init) {
            $check_gv3_init = 'true';
        }

        $include_files_tags = '';
        if (true) {

            $include_files = Geo_Preferences::GetIncludeCSS($cnf_html_dir, $cnf_website_url);
            $include_files_css = $include_files['css_files'];
            foreach ($include_files_css as $css_file)
            {
                $include_files_tags .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$css_file\" />\n";
            }

        }

        $tag_string_ini .= $include_files_tags;

        if (true) {

            $tag_string_ini .= '

    <script type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/geocoding/map_popups.js"></script>
    <script type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/geocoding/openlayers/OpenLayers.js"></script>
    <script type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/geocoding/openlayers/OLlocals.js"></script>
    <script type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/geocoding/map_preview.js"></script>
';

        }

        $tag_string_mid .= '
<script type="text/javascript">
    window.map_prepared = false;

';

        if ('' != $geo_map_incl_async_init) {
        $tag_string_mid .= '
if (undefined === window.' . $geo_map_incl_async_init . ') {
    window.' . $geo_map_incl_async_init . ' = function () {
        window.gv3_maps_loaded = true;
    };
}
';
        }

        $tag_string_mid .= '

    geo_object'. $map_suffix .' = new geo_locations();

window.center_large_map' . $map_suffix . ' = function () {
    try {
        if (window.map_win_popup && window.map_win_popup.map_prepared) {
            if ("' . $map_suffix . '" == window.map_win_popup.map_obj_specifier) {
                window.map_win_popup.geo_object' . $map_suffix . '.map_showview();
            }
        }
    } catch (e) {}
};

window.point_large_map_center' . $map_suffix . ' = function (index, select) {
    try {
        if (window.map_win_popup && window.map_win_popup.map_prepared) {
            if ("' . $map_suffix . '" == window.map_win_popup.map_obj_specifier) {
                window.map_win_popup.geo_object' . $map_suffix . '.center_poi(index);
                if (select) {
                    window.map_win_popup.OpenLayers.HooksPopups.on_map_feature_select(window.map_win_popup.geo_object' . $map_suffix . ', index);
                }
            }
        }
    } catch (e) {}
};

window.geo_on_load_proc_check_async_map' . $map_suffix . ' = function()
{

    var async_loaded = true;
    if (' . $check_gv3 . ') {
        var loaded_gv3 = false;
        if ((undefined !== window.google) && (undefined !== window.google.maps) && (undefined !== window.google.maps.event)) {
            loaded_gv3 = true;
        }

        if (' . $check_gv3_init . ') {
            if ((undefined === window.gv3_maps_loaded) || (!window.gv3_maps_loaded)) {
                loaded_gv3 = false;
            }
        }

        if (!loaded_gv3) {async_loaded = false;}
    }

    if (0 < window.count_to_async_load' . $map_suffix . ') {
        async_loaded = false;
    }

    if (async_loaded) {
        setTimeout("geo_on_load_proc_phase2_map' . $map_suffix . '();", 250);
        return;
    }

    setTimeout("geo_on_load_proc_check_async_map' . $map_suffix . '();", 250);

}

var geo_on_load_proc_map' . $map_suffix . ' = function()
{
    var map_obj = document.getElementById ? document.getElementById("geo_map_mapcanvas' . $map_suffix . '") : null;
    if (map_obj)
    {
        if (typeof(window.map_popup_win) == "undefined") {
            map_obj.style.width = "' . $geo_map_usage['width'] . 'px";
            map_obj.style.height = "' . $geo_map_usage['height'] . 'px";
        } else {
            // not setting the map size for the large map
        }

';

    $article_spec_arr = array('language_id' => $f_language_id, 'article_number' => $f_article_number);
    $article_spec = json_encode($article_spec_arr);

    $tag_string_mid .= "\n";
    $tag_string_mid .= "geo_object$map_suffix.set_article_spec($article_spec);";
    $tag_string_mid .= "\n";
    $tag_string_mid .= "geo_object$map_suffix.set_auto_focus($geo_focus_json);";
    $tag_string_mid .= "\n";
    $tag_string_mid .= "geo_object$map_suffix.set_map_info($geo_map_json);";
    $tag_string_mid .= "\n";
    $tag_string_mid .= "geo_object$map_suffix.set_map_usage($geo_map_usage_json);";
    $tag_string_mid .= "\n";
    $tag_string_mid .= "geo_object$map_suffix.set_icons_info($geo_icons_json);";
    $tag_string_mid .= "\n";
    $tag_string_mid .= "geo_object$map_suffix.set_popups_info($geo_popups_json);";
    $tag_string_mid .= "\n";
    if ($large_map_on_click) {
        $tag_string_mid .= "if (typeof(window.map_popup_win) == \"undefined\") {\n";
        $tag_string_mid .= "    geo_object$map_suffix.set_action_subst(function(params) {";
        $tag_string_mid .= "        " . self::GetMapTagOpen($p_articleNumber, $p_languageId, "open_form") . "(params);\n";
        $tag_string_mid .= "    });\n";
        $tag_string_mid .= "}\n";
        $tag_string_mid .= "\n";
    }
    $tag_string_mid .= "if (typeof(window.map_popup_win) != \"undefined\") {\n";
    $tag_string_mid .= "    geo_object$map_suffix.set_map_large({width:$width_large_map,height:$height_large_map,map_holder:\"map_body_holder\"});\n";
    $tag_string_mid .= "}\n";
    $tag_string_mid .= "\n";

        $tag_string_mid .= '

        if (true && (typeof(window.map_popup_win) != "undefined")) {
';

        $tag_async_load_bunch = '';
        $count_to_async_load = 0;
        foreach ($geo_map_incl_async_arr as $one_async_scr) {
            if (!empty($one_async_scr)) {
                $count_to_async_load += 1;
                $tag_async_load_bunch .= '$.getScript("' . $one_async_scr . '", function() {window.count_to_async_load' . $map_suffix . ' -= 1;});';
            }
        }

        $tag_string_mid .= '
            window.count_to_async_load' . $map_suffix . ' = ' . $count_to_async_load . ';
            window.gv3_maps_loaded = false;

            ' . $tag_async_load_bunch . '

            if (true) {
                setTimeout("geo_on_load_proc_check_async_map' . $map_suffix . '();", 250);
                return;
            }
        }

        setTimeout("geo_on_load_proc_phase2_map' . $map_suffix . '();", 250);
        return;
    }
};

var geo_on_load_proc_phase2_map' . $map_suffix . ' = function()
{
        var res_state = false;
        try {
            res_state = OpenLayers.Util.test_ready();
        } catch (e) {res_state = false;}

        if (!res_state)
        {
            setTimeout("geo_on_load_proc_phase2_map' . $map_suffix . '();", 250);
            return;
        }

        geo_object' . $map_suffix . '.main_openlayers_init("geo_map_mapcanvas' . $map_suffix. '");
        geo_object' . $map_suffix . '.got_load_data(' . $poi_info_json . ', true);

        window.map_prepared = true;

        if (undefined !== window.deferred_action) {
            try {window.deferred_action();} catch (e) {}
        }

};

    $(document).ready(function()
    {
        setTimeout("geo_on_load_proc_map' . $map_suffix . '();", 0);
    });
</script>
';

        // should we provide js for large-map openning
        if ($open_large_map) {

            $tag_string_fin .= self::GetLargeMapOpener($map_suffix, $width_large_map, $height_large_map, $label_large_map, $tag_string_ini . $tag_string_mid, self::GetMapTagBody($p_articleNumber, $p_languageId, true));

        }

        $tag_string .= $tag_string_top;
        if ($load_common) {
            $tag_string .= $tag_string_ini;
        }

        return $tag_string . $tag_string_mid . $tag_string_fin;

    } // fn GetMapTagHeader

    /**
     * Gives the body map-placement part for the map front end presentation
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     *
     * @return string
     */
    public static function GetMapTagBody($p_articleNumber, $p_languageId, $p_largeMap = false)
    {
        global $Campsite;
        $tag_string = '';

        $f_article_number = $p_articleNumber;
        $f_language_id = $p_languageId;

        $map_suffix = '_' . $f_article_number . '_' . $f_language_id;

        if ($p_largeMap) {
            $tag_string .= "<div id=\"geo_map_mapcanvas$map_suffix\" class=\"geo_map_mapcanvas_large\"></div>\n";
        }
        else {
            $tag_string .= "<div id=\"geo_map_mapcanvas$map_suffix\" class=\"geo_map_mapcanvas\"></div>\n";
        }

        return $tag_string;
    } // fn GetMapTagBody

    /**
     * Gives the body open-large-map link part for the article-map front end presentation
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     *
     * @return string
     */
    public static function GetMapTagOpen($p_articleNumber, $p_languageId, $p_specifier)
    {
        global $Campsite;
        $tag_string = '';

        $f_article_number = $p_articleNumber;
        $f_language_id = $p_languageId;

        $map_suffix = '_' . $f_article_number . '_' . $f_language_id;

        if ('open_form' == $p_specifier) {
            $tag_string .= "geo_open_large_map$map_suffix";
        }
        else {
            $tag_string .= "if (window.map_prepared) {geo_open_large_map$map_suffix();} ";
        }

        return $tag_string;
    } // fn GetMapTagOpen

    /**
     * Gives the body map-centering (js call) part for the map front end presentation
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     *
     * @return string
     */
    public static function GetMapTagCenter($p_articleNumber, $p_languageId)
    {
        global $Campsite;
        $tag_string = '';

        $f_article_number = $p_articleNumber;
        $f_language_id = $p_languageId;

        $map_suffix = '_' . $f_article_number . '_' . $f_language_id;

        $tag_string .= 'if (window.map_prepared) {geo_object' . $map_suffix . '.map_showview();} ';
        $tag_string .= 'window.center_large_map' . $map_suffix . '(); ';

        return $tag_string;
    } // fn GetMapTagCenter

    /**
     * Gives the body map-info and point-list part for the map front end presentation
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     *
     * @return array
     */
    public static function GetMapTagListData($p_articleNumber, $p_languageId)
    {
        $f_article_number = (int) $p_articleNumber;
        $f_language_id = (int) $p_languageId;
        $map_suffix = '_' . $f_article_number . '_' . $f_language_id;
        $map_id = Geo_Map::GetMapIdByArticle($f_article_number);
        $preview = true;
        $text_only = true;

        $poi_info = Geo_Map::LoadMapData($map_id, $f_language_id, $f_article_number, $preview, $text_only);
        $pind = 0;
        foreach ($poi_info['pois'] as $rank => $poi) {
            $cur_lon = $poi['longitude'];
            $cur_lat = $poi['latitude'];
            $center_poi = "if (window.map_prepared) {geo_object$map_suffix.center_lonlat($cur_lon, $cur_lat);} point_large_map_center" . $map_suffix . "($pind, false);";
            $select_poi = "if (window.map_prepared) {geo_object$map_suffix.select_poi($pind);} point_large_map_center" . $map_suffix . "($pind, true);";
            $poi_info['pois'][$rank]['center'] = $center_poi;
            $poi_info['pois'][$rank]['open'] = $select_poi;
            $pind += 1;
        }
        return (array) $poi_info;
    } // fn GetMapTagListData

    /**
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @return string
     */
    public static function GetMapTagList($p_articleNumber, $p_languageId)
    {
        $geo = self::GetMapTagListData((int) $p_articleNumber, (int) $p_languageId);
        $map = $geo['map'];
        $pois = $geo['pois'];

        $icons_dir = Geo_Preferences::GetIconsWebDir();

        $map_name = $map['name'];
        $map_name = str_replace('&', '&amp;', $map_name);
        $map_name = str_replace('<', '&lt;', $map_name);
        $map_name = str_replace('>', '&gt;', $map_name);

        $html = '
            <div class="geomap_info">
              <dl class="geomap_map_name">
                <dt class="geomap_map_name_label">' .
                  getGS('Map') . ':
                </dt>
                <dd class="geomap_map_name_value">' .
                  $map_name . '
                </dd>
              </dl>
            </div>
            <div id="side_info" class="geo_side_info">';
        $poiIdx = 0;
        foreach ($pois as $poi) {
            $poi_title = $poi['title'];
            $poi_title = str_replace('&', '&amp;', $poi_title);
            $poi_title = str_replace('<', '&lt;', $poi_title);
            $poi_title = str_replace('>', '&gt;', $poi_title);
            $poi_perex = $poi['perex'];
            $poi_perex = str_replace('&', '&amp;', $poi_perex);
            $poi_perex = str_replace('<', '&lt;', $poi_perex);
            $poi_perex = str_replace('>', '&gt;', $poi_perex);

            $poi_icon = $poi['style'];

            $html .= '<div id="poi_seq_' . $poiIdx . '">
                <a class="geomap_poi_icon_link" href="#" onClick="' . $poi['open'] . ' return false;"><img class="geomap_poi_icon" src="' . $icons_dir . '/' . $poi_icon . '" /></a>
                <a class="geomap_poi_name" href="#" onClick="'
                . $poi['open'] . ' return false;">' . $poi_title . '</a>
                <div class="geomap_poi_perex">' . $poi_perex . '</div>
                <div class="geomap_poi_center">
                    <a href="#" onClick="' . $poi['center'] . ' return false;">'
                        . getGS('Center') . '
                    </a>
                </div>
                <div class="geomap_poi_spacer">&nbsp;</div>
            </div>';
            $poiIdx += 1;
        }
        $html .= '</div>';
        return $html;
    } // fn GetMapTagList

    // multi-map functions

    /**
     * Gives the header part for the multi-map front end presentation
     *
     * @param int $p_languageId
     * @param array $p_constarints
     * @param array $p_options
     * @param int $p_offset
     * @param int $p_limit
     * @param int $p_mapWidth
     * @param int $p_mapHeight
     * @param int $p_rank
     *    The rank of the current multi-map, used to make unique ids
     *
     * @return string
     */
    public static function GetMultiMapTagHeader($p_languageId, $p_constraints, $p_options, $p_offset, $p_limit, $p_mapWidth, $p_mapHeight, $p_rank = 0)
    {
        global $Campsite;
        $tag_string = '';
        $tag_string_top = '';
        $tag_string_gv3_async = false;
        $tag_string_ini = '';
        $tag_string_mid = '';
        $tag_string_fin = '';

        $points = null;

        $pois_loaded = false;
        $max_zoom = null;
        $map_margin = null;
        $load_common = true;
        $area_points = null;
        $area_points_empty_only = 'false';

        if (is_array($p_options)) {
            if (array_key_exists('pois_retrieved', $p_options)) {
                $pois_loaded = $p_options['pois_retrieved'];
            }
            if (array_key_exists('max_zoom', $p_options)) {
                $max_zoom = $p_options['max_zoom'];
            }
            if (array_key_exists('map_margin', $p_options)) {
                $map_margin = $p_options['map_margin'];
            }
            if (array_key_exists('load_common', $p_options)) {
                $load_common = $p_options['load_common'];
            }
            if (array_key_exists('area_points', $p_options)) {
                $area_points = $p_options['area_points'];
            }
            if (array_key_exists('area_points_empty_only', $p_options)) {
                if ($p_options['area_points_empty_only']) {
                    $area_points_empty_only = 'true';
                }
            }
        }

        $large_map_on_click = false;
        $open_large_map = false;
        $width_large_map = 800;
        $height_large_map = 600;
        $label_large_map = '';
        if (is_array($p_options)) {
            if (array_key_exists('large_map_on_click', $p_options)) {
                $large_map_on_click = $p_options['large_map_on_click'];
            }
            if (array_key_exists('large_map_open', $p_options)) {
                $open_large_map = $p_options['large_map_open'];
            }
            if ($large_map_on_click && (!$open_large_map)) {
                $open_large_map = true;
            }
            if (array_key_exists('large_map_width', $p_options)) {
                $width_large_map_param = 0 + $p_options['large_map_width'];
                if (0 < $width_large_map_param) {
                    $width_large_map = $width_large_map_param;
                }
            }
            if (array_key_exists('large_map_height', $p_options)) {
                $height_large_map_param = 0 + $p_options['large_map_height'];
                if (0 < $height_large_map_param) {
                    $height_large_map = $height_large_map_param;
                }
            }
            if (array_key_exists('large_map_label', $p_options)) {
                $label_large_map_param = '' . $p_options['large_map_label'];
                if ('' != $label_large_map_param) {
                    $label_large_map = $label_large_map_param;
                }
            }
        }

        if (!$pois_loaded) {

            $leftOperand = 'as_array';
            $rightOperand = true;
            $operator = new Operator('is', 'php');
            $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
            $p_constraints[] = $constraint;
    
            $leftOperand = 'active_only';
            $rightOperand = true;
            $operator = new Operator('is', 'php');
            $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
            $p_constraints[] = $constraint;
    
            $leftOperand = 'text_only';
            $rightOperand = false;
            $operator = new Operator('is', 'php');
            $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
            $p_constraints[] = $constraint;
    
            $leftOperand = 'language';
            $rightOperand = $p_languageId;
            $operator = new Operator('is', 'php');
            $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
            $p_constraints[] = $constraint;
    
            $leftOperand = 'constrained';
            $rightOperand = true;
            $operator = new Operator('is', 'php');
            $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
            $p_constraints[] = $constraint;
    
            $poi_count = 0;
            $points = array();
            $point_objs = Geo_MapLocation::GetListExt($p_constraints, (array) null, $p_offset, $p_limit, $poi_count, false, $points);

        }
        else {

            $points = $p_constraints;

        }

        $f_language_id = $p_languageId;

        $map_suffix = '_' . 'multimap' . '_' . $f_language_id . '_' . $p_rank;

        $cnf_html_dir = $Campsite['HTML_DIR'];
        $cnf_website_url = $Campsite['WEBSITE_URL'];
        
        $geo_map_usage = Geo_Map::ReadMultiMapInfo();
        if (0 < $p_mapWidth)
        {
            $geo_map_usage['width'] = $p_mapWidth;
        }
        if (0 < $p_mapHeight)
        {
            $geo_map_usage['height'] = $p_mapHeight;
        }

        $geo_map_usage_json = '';
        $geo_map_usage_json .= json_encode($geo_map_usage);

        $geo_map_info = Geo_Preferences::GetMapInfo($cnf_html_dir, $cnf_website_url, $geo_map_usage['prov']);
        $geo_map_incl = Geo_Preferences::PrepareMapIncludes($geo_map_info['incl_obj']);
        $geo_map_incl_async_arr = $geo_map_info['incl_obj_async'];
        $geo_map_incl_async_init = $geo_map_info['incl_gv3_init'];

        $tag_string_gv3_async = $geo_map_info['incl_gv3'];
        $geo_map_json = '';
        $geo_map_json .= json_encode($geo_map_info['json_obj']);

        $geo_icons_info = Geo_Preferences::GetIconsInfo($cnf_html_dir, $cnf_website_url);
        $geo_icons_json = '';
        $geo_icons_json .= json_encode($geo_icons_info['json_obj']);
        
        $geo_popups_info = Geo_Preferences::GetPopupsInfo($cnf_html_dir, $cnf_website_url);
        $geo_popups_json = '';
        $geo_popups_json .= json_encode($geo_popups_info['json_obj']);

        $geo_focus_info = Geo_Preferences::GetFocusInfo($cnf_html_dir, $cnf_website_url);

        {
            $geo_focus_info['json_obj']['auto_focus'] = true;
        }
        if (null !== $max_zoom)
        {
            $geo_focus_info['json_obj']['max_zoom'] = $max_zoom;
        }
        if (null !== $map_margin)
        {
            $geo_focus_info['json_obj']['border'] = $map_margin;
        }
        $geo_focus_json = '';
        $geo_focus_json .= json_encode($geo_focus_info['json_obj']);
        
        $preview = true;
        $poi_info = array('pois' => $points, 'map' => $geo_map_usage);
        
        $poi_info_json = json_encode($poi_info);
        
        $geocodingdir = $Campsite['WEBSITE_URL'] . '/js/geocoding/';

        // map-provider specific includes shall be taken for all maps, since those maps can use different map providers
        $tag_string_top .= $geo_map_incl;
        $tag_string_top .= "\n";

        $check_gv3 = 'false';
        if ($tag_string_gv3_async) {
            $check_gv3 = 'true';
        }
        $check_gv3_init = 'false';
        if ('' != $geo_map_incl_async_init) {
            $check_gv3_init = 'true';
        }

        $include_files_tags = '';
        if (true) {
            $include_files = Geo_Preferences::GetIncludeCSS($cnf_html_dir, $cnf_website_url);
            $include_files_css = $include_files['css_files'];
            foreach ($include_files_css as $css_file)
            {
                $include_files_tags .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$css_file\" />\n";
            }
        }

        $tag_string_ini .= $include_files_tags;

        if (true) {

            $tag_string_ini .= '

    <script type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/geocoding/map_popups.js"></script>
    <script type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/geocoding/openlayers/OpenLayers.js"></script>
    <script type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/geocoding/openlayers/OLlocals.js"></script>
    <script type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/geocoding/map_preview.js"></script>

';
        }

        $tag_string_mid .= '

<script type="text/javascript">
    window.map_prepared = false;

';

        if ('' != $geo_map_incl_async_init) {
        $tag_string_mid .= '
if (undefined === window.' . $geo_map_incl_async_init . ') {
    window.' . $geo_map_incl_async_init . ' = function () {
        window.gv3_maps_loaded = true;
    };
}
';
        }

        $tag_string_mid .= '

    geo_object'. $map_suffix .' = new geo_locations();

window.center_large_map' . $map_suffix . ' = function () {
    try {
        if (window.map_win_popup && window.map_win_popup.map_prepared) {
            if ("' . $map_suffix . '" == window.map_win_popup.map_obj_specifier) {
                window.map_win_popup.geo_object' . $map_suffix . '.map_showview();
            }
        }
    } catch (e) {}
};

window.point_large_map_center' . $map_suffix . ' = function (index, select) {
    try {
        if (window.map_win_popup && window.map_win_popup.map_prepared) {
            if ("' . $map_suffix . '" == window.map_win_popup.map_obj_specifier) {
                window.map_win_popup.geo_object' . $map_suffix . '.center_poi(index);
                if (select) {
                    window.map_win_popup.OpenLayers.HooksPopups.on_map_feature_select(window.map_win_popup.geo_object' . $map_suffix . ', index);
                }
            }
        }
    } catch (e) {}
};

window.geo_on_load_proc_check_async_map' . $map_suffix . ' = function()
{

    var async_loaded = true;
    if (' . $check_gv3 . ') {
        var loaded_gv3 = false;
        if ((undefined !== window.google) && (undefined !== window.google.maps) && (undefined !== window.google.maps.event)) {
            loaded_gv3 = true;
        }

        if (' . $check_gv3_init . ') {
            if ((undefined === window.gv3_maps_loaded) || (!window.gv3_maps_loaded)) {
                loaded_gv3 = false;
            }
        }

        if (!loaded_gv3) {async_loaded = false;}
    }

    if (0 < window.count_to_async_load' . $map_suffix . ') {
        async_loaded = false;
    }

    if (async_loaded) {
        setTimeout("geo_on_load_proc_phase2_map' . $map_suffix . '();", 250);
        return;
    }

    setTimeout("geo_on_load_proc_check_async_map' . $map_suffix . '();", 250);

}

var geo_on_load_proc_map' . $map_suffix . ' = function()
{

    var map_obj = document.getElementById ? document.getElementById("geo_map_mapcanvas' . $map_suffix . '") : null;
    if (map_obj)
    {
        if (typeof(window.map_popup_win) == "undefined") {
            map_obj.style.width = "' . $geo_map_usage['width'] . 'px";
            map_obj.style.height = "' . $geo_map_usage['height'] . 'px";
        } else {
            // not setting the map size for the large map
        }
';

    $article_spec_arr = array('language_id' => $f_language_id, 'article_number' => 0);
    $article_spec = json_encode($article_spec_arr);

    $local_strings = array();
    $local_strings['articles'] = getGS('Articles');
    $local_strings_json = json_encode($local_strings);

    $tag_string_mid .= "\n";
    $tag_string_mid .= "geo_object$map_suffix.set_article_spec($article_spec);";
    $tag_string_mid .= "\n";
    $tag_string_mid .= "geo_object$map_suffix.set_auto_focus($geo_focus_json);";
    $tag_string_mid .= "\n";
    $tag_string_mid .= "geo_object$map_suffix.set_map_info($geo_map_json);";
    $tag_string_mid .= "\n";
    $tag_string_mid .= "geo_object$map_suffix.set_map_usage($geo_map_usage_json);";
    $tag_string_mid .= "\n";
    $tag_string_mid .= "geo_object$map_suffix.set_icons_info($geo_icons_json);";
    $tag_string_mid .= "\n";
    $tag_string_mid .= "geo_object$map_suffix.set_popups_info($geo_popups_json);";
    $tag_string_mid .= "\n";
    $tag_string_mid .= "geo_object$map_suffix.set_display_strings($local_strings_json);";
    $tag_string_mid .= "\n";

    if ($area_points) {
        $tag_string_mid .= "geo_object$map_suffix.set_area_constraints($area_points, {\"empty_only\":$area_points_empty_only});";
        $tag_string_mid .= "\n";
    }

    if ($large_map_on_click) {
        $tag_string_mid .= "if (typeof(window.map_popup_win) == \"undefined\") {\n";
        $tag_string_mid .= "    geo_object$map_suffix.set_action_subst(function(params) {";
        $tag_string_mid .= "        " . self::GetMultiMapTagOpen($p_languageId, $p_rank, "open_form") . "(params);\n";
        $tag_string_mid .= "    });\n";
        $tag_string_mid .= "}\n";
        $tag_string_mid .= "\n";
    }
    $tag_string_mid .= "if (typeof(window.map_popup_win) != \"undefined\") {\n";
    $tag_string_mid .= "    geo_object$map_suffix.set_map_large({width:$width_large_map,height:$height_large_map,map_holder:\"map_body_holder\"});\n";
    $tag_string_mid .= "}\n";
    $tag_string_mid .= "\n";

        $tag_string_mid .= '

        if (true && (typeof(window.map_popup_win) != "undefined")) {
';

        $tag_async_load_bunch = '';
        $count_to_async_load = 0;
        foreach ($geo_map_incl_async_arr as $one_async_scr) {
            if (!empty($one_async_scr)) {
                $count_to_async_load += 1;
                $tag_async_load_bunch .= '$.getScript("' . $one_async_scr . '", function() {window.count_to_async_load' . $map_suffix . ' -= 1;});';
            }
        }

        $tag_string_mid .= '
            window.count_to_async_load' . $map_suffix . ' = ' . $count_to_async_load . ';
            window.gv3_maps_loaded = false;

            ' . $tag_async_load_bunch . '

            if (true) {
                setTimeout("geo_on_load_proc_check_async_map' . $map_suffix . '();", 250);
                return;
            }
        }

        setTimeout("geo_on_load_proc_phase2_map' . $map_suffix . '();", 250);
        return;
    }
};

var geo_on_load_proc_phase2_map' . $map_suffix . ' = function()
{
        var res_state = false;
        try {
            res_state = OpenLayers.Util.test_ready();
        } catch (e) {res_state = false;}

        if (!res_state)
        {
            setTimeout("geo_on_load_proc_phase2_map' . $map_suffix . '();", 250);
            return;
        }

        geo_object' . $map_suffix . '.main_openlayers_init("geo_map_mapcanvas' . $map_suffix. '");
        geo_object' . $map_suffix . '.got_load_data(' . $poi_info_json . ', true);

        window.map_prepared = true;

        if (undefined !== window.deferred_action) {
            try {window.deferred_action();} catch (e) {}
        }
};

    $(document).ready(function()
    {
        setTimeout("geo_on_load_proc_map' . $map_suffix . '();", 0);
    });
</script>
';

        // should we provide js for large-map openning
        if ($open_large_map) {

            $tag_string_fin .= self::GetLargeMapOpener($map_suffix, $width_large_map, $height_large_map, $label_large_map, $tag_string_ini . $tag_string_mid, self::GetMultiMapTagBody($p_languageId, $p_rank, true));

        }

        $tag_string .= $tag_string_top;
        if ($load_common) {
            $tag_string .= $tag_string_ini;
        }

        return $tag_string . $tag_string_mid . $tag_string_fin;

    } // fn GetMultiMapTagHeader

    /**
     * Gives the body map-placement part for the map front end presentation
     *
     * @param int $p_languageId
     * @param int $p_rank
     *    The rank of the current multi-map, used to make unique ids
     *
     * @return string
     */
    public static function GetMultiMapTagBody($p_languageId, $p_rank = 0, $p_largeMap = false)
    {
        global $Campsite;
        $tag_string = '';

        $f_language_id = $p_languageId;

        $map_suffix = '_' . 'multimap' . '_' . $f_language_id . '_' . $p_rank;

        if ($p_largeMap) {
            $tag_string .= "<div id=\"geo_map_mapcanvas$map_suffix\" class=\"geo_map_mapcanvas_large\"></div>\n";
        }
        else {
            $tag_string .= "<div id=\"geo_map_mapcanvas$map_suffix\" class=\"geo_map_mapcanvas\"></div>\n";
        }

        return $tag_string;
    } // fn GetMultiMapTagBody

    /**
     * Gives the body open-large-map link part for the multi-map front end presentation
     *
     * @param int $p_languageId
     * @param int $p_rank
     *    The rank of the current multi-map, used to make unique ids
     *
     * @return string
     */
    public static function GetMultiMapTagOpen($p_languageId, $p_rank = 0, $p_specifier)
    {
        global $Campsite;
        $tag_string = '';

        $f_language_id = $p_languageId;

        $map_suffix = '_' . 'multimap' . '_' . $f_language_id . '_' . $p_rank;

        if ('open_form' == $p_specifier) {
            $tag_string .= "geo_open_large_map$map_suffix";
        }
        else {
            $tag_string .= "if (window.map_prepared) {geo_open_large_map$map_suffix();} ";
        }

        return $tag_string;
    } // fn GetMultiMapTagOpen

    /**
     * Gives the body map-centering (js call) part for the map front end presentation
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     *
     * @return string
     */
    public static function GetMultiMapTagCenter($p_languageId, $p_rank = 0)
    {
        global $Campsite;
        $tag_string = '';

        $f_language_id = $p_languageId;

        $map_suffix = '_' . 'multimap' . '_' . $f_language_id . '_' . $p_rank;

        $tag_string .= 'if (window.map_prepared) {geo_object' . $map_suffix . '.map_showview();} ';
        $tag_string .= 'window.center_large_map' . $map_suffix . '(); ';

        return $tag_string;
    } // fn GetMultiMapTagCenter

    /**
     * Gives the body map-info and point-list part for the map front end presentation
     *
     * @param int $p_languageId
     * @param array $p_constraints
     * @param array $p_options
     * @param int $p_offset
     * @param int $p_limit
     * @param int $p_rank
     *    The rank of the current multi-map, used to make unique ids
     *
     * @return array
     */
    public static function GetMultiMapTagListData($p_languageId, $p_constraints, $p_options, $p_offset, $p_limit, $p_rank = 0)
    {
        $f_language_id = (int) $p_languageId;
        $map_suffix = '_' . 'multimap' . '_' . $f_language_id . '_' . $p_rank;
        $preview = true;
        $text_only = true;

        $geo_map_usage = Geo_Map::ReadMultiMapInfo();

        $points = null;

        $pois_loaded = false;

        if (is_array($p_options)) {
            if (array_key_exists('pois_retrieved', $p_options)) {
                $pois_loaded = $p_options['pois_retrieved'];
            }
        }

        if (!$pois_loaded) {

            $leftOperand = 'as_array';
            $rightOperand = true;
            $operator = new Operator('is', 'php');
            $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
            $p_constraints[] = $constraint;
    
            $leftOperand = 'active_only';
            $rightOperand = true;
            $operator = new Operator('is', 'php');
            $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
            $p_constraints[] = $constraint;
    
            $leftOperand = 'text_only';
            $rightOperand = true;
            $operator = new Operator('is', 'php');
            $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
            $p_constraints[] = $constraint;
    
            $leftOperand = 'language';
            $rightOperand = $p_languageId;
            $operator = new Operator('is', 'php');
            $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
            $p_constraints[] = $constraint;
    
            $leftOperand = 'constrained';
            $rightOperand = true;
            $operator = new Operator('is', 'php');
            $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
            $p_constraints[] = $constraint;

            $poi_count = 0;
            $points = array();
            $point_objs = Geo_MapLocation::GetListExt($p_constraints, (array) null, $p_offset, $p_limit, $poi_count, false, $points);

        }
        else {

            $points = $p_constraints;

        }

        $poi_info = array('pois' => $points, 'map' => $geo_map_usage);

        $pind = 0;
        foreach ($poi_info['pois'] as $rank => $poi) {
            $cur_lon = $poi['longitude'];
            $cur_lat = $poi['latitude'];
            $center_poi = "if (window.map_prepared) {geo_object$map_suffix.center_lonlat($cur_lon, $cur_lat);} point_large_map_center" . $map_suffix . "($pind, false);";
            $select_poi = "if (window.map_prepared) {geo_object$map_suffix.select_poi($pind);} point_large_map_center" . $map_suffix . "($pind, true);";
            $poi_info['pois'][$rank]['center'] = $center_poi;
            $poi_info['pois'][$rank]['open'] = $select_poi;
            $pind += 1;
        }
        return (array) $poi_info;
    } // fn GetMultiMapTagListData

    /**
     * @param int $p_languageId
     * @param array $p_constraints
     * @param array $p_options
     * @param string $p_label
     * @param int $p_offset
     * @param int $p_limit
     * @param int $p_rank
     *    The rank of the current multi-map, used to make unique ids
     *
     * @return string
     */
    public static function GetMultiMapTagList($p_languageId, $p_constraints, $p_options, $p_label, $p_offset, $p_limit, $p_rank = 0)
    {

        $geo = self::GetMultiMapTagListData((int) $p_languageId, $p_constraints, $p_options, $p_offset, $p_limit, $p_rank);
        $map = $geo['map'];
        $pois = $geo['pois'];

        $icons_dir = Geo_Preferences::GetIconsWebDir();

        //$map_name = $map['name'];
        $map_name = $p_label;
        $map_name = str_replace('&', '&amp;', $map_name);
        $map_name = str_replace('<', '&lt;', $map_name);
        $map_name = str_replace('>', '&gt;', $map_name);

        if (0 < strlen($map_name)) {
            $html = '
            <div class="geomap_info">
              <dl class="geomap_map_name">
                <dt class="geomap_map_name_label">' .
                  getGS('Map') . ':
                </dt>
                <dd class="geomap_map_name_value">' .
                  $map_name . '
                </dd>
              </dl>
            </div>';
        }

        $html .= '
            <div id="side_info" class="geo_side_info">';
        $poiIdx = 0;
        foreach ($pois as $poi) {
            $poi_title = $poi['title'];
            $poi_title = str_replace('&', '&amp;', $poi_title);
            $poi_title = str_replace('<', '&lt;', $poi_title);
            $poi_title = str_replace('>', '&gt;', $poi_title);
            $poi_perex = $poi['perex'];
            $poi_perex = str_replace('&', '&amp;', $poi_perex);
            $poi_perex = str_replace('<', '&lt;', $poi_perex);
            $poi_perex = str_replace('>', '&gt;', $poi_perex);

            $poi_icon = $poi['style'];

            $html .= '<div id="poi_seq_' . $poiIdx . '">
                <a class="geomap_poi_icon_link" href="#" onClick="' . $poi['open'] . ' return false;"><img class="geomap_poi_icon" src="' . $icons_dir . '/' . $poi_icon . '" /></a>
                <a class="geomap_poi_name" href="#" onClick="'
                . $poi['open'] . ' return false;">' . $poi_title . '</a>
                <div class="geomap_poi_perex">' . $poi_perex . '</div>
                <div class="geomap_poi_center">
                    <a href="#" onClick="' . $poi['center'] . ' return false;">'
                        . getGS('Center') . '
                    </a>
                </div>
                <div class="geomap_poi_spacer">&nbsp;</div>
            </div>';
            $poiIdx += 1;
        }
        $html .= '</div>';
        return $html;
    } // fn GetMultiMapTagList

    // filter functions

    /**
     * Gives the name of the javascript object used for the map filtering
     *
     * @return string
     */
    public static function GetMapFilterObjName()
    {
        $map_suffix = '_filter';
        return 'geo_object' . $map_suffix;
    } // fn GetMapFilterObjName

    /**
     * Gives the header part for the map filtering
     *
     * @param int $p_mapWidth
     * @param int $p_mapHeight
     *
     * @return string
     */
    public static function GetMapFilterHeader($p_mapWidth = 0, $p_mapHeight = 0)
    {
        global $Campsite;
        $tag_string = '';

        $map_suffix = '_filter';

        $cnf_html_dir = $Campsite['HTML_DIR'];
        $cnf_website_url = $Campsite['WEBSITE_URL'];

        $geo_map_info = Geo_Preferences::GetMapInfo($cnf_html_dir, $cnf_website_url);
        if (0 < $p_mapWidth)
        {
            $geo_map_info['width'] = $p_mapWidth;
        }
        if (0 < $p_mapHeight)
        {
            $geo_map_info['height'] = $p_mapHeight;
        }

        $geo_map_incl = Geo_Preferences::PrepareMapIncludes($geo_map_info['incl_obj']);
        $geo_map_json = '';
        $geo_map_json .= json_encode($geo_map_info['json_obj']);

        $geocodingdir = $Campsite['WEBSITE_URL'] . '/js/geocoding/';


        $tag_string .= $geo_map_incl;
        $tag_string .= "\n";

        $tag_string .= '

    <script type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/geocoding/openlayers/OpenLayers.js"></script>
    <script type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/geocoding/openlayers/OLlocals.js"></script>
    <script type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/geocoding/map_filter.js"></script>

<script type="text/javascript">

    geo_object'. $map_suffix .' = new geo_locations_filter();

var on_load_proc_filter = function()
{
    var res_state = false;
    try {
        res_state = OpenLayers.Util.test_ready();
    } catch (e) {res_state = false;}

    if (!res_state)
    {
        setTimeout("on_load_proc_filter();", 250);
        return;
    }

    var map_obj = document.getElementById ? document.getElementById("geo_map_mapcanvas' . $map_suffix . '") : null;
    if (map_obj)
    {
        map_obj.style.width = "' . $geo_map_info['width'] . 'px";
        map_obj.style.height = "' . $geo_map_info['height'] . 'px";
';
    $loc_strings = json_encode(array(
        'corners' => getGS('vertices'),
        'pan_map' => getGS('Pan Map'),
        'edit_polygon' => getGS('Edit Polygon'),
        'create_polygon' => getGS('Create Polygon'),
    ));
    $img_dir = $Campsite['ADMIN_STYLE_URL'] . '/images/';

    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_map_info($geo_map_json);";
    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_obj_name('geo_object$map_suffix');";
    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_display_strings($loc_strings);";
    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_img_dir('$img_dir');";
    $tag_string .= "\n";

    $tag_string .= '
        geo_object' . $map_suffix . '.main_init("geo_map_mapcanvas' . $map_suffix. '");

    }
};
    $(document).ready(function()
    {
        on_load_proc_filter();
    });
</script>
';

        return $tag_string;

    } // fn GetMapFilterHeader

    /**
     * Gives the body map-placement part for the map filtering
     *
     * @return string
     */
    public static function GetMapFilterBody()
    {
        global $Campsite;
        $tag_string = '';

        $map_suffix = '_filter';

        $tag_string .= "<div id=\"geo_map_mapcanvas$map_suffix\"></div>\n";

        return $tag_string;
    } // fn GetMapFilterBody

    /**
     * Gives the body map-centering (js call) part for the map filtering
     *
     * @return string
     */
    public static function GetMapFilterCenter()
    {
        global $Campsite;
        $tag_string = '';

        $map_suffix = '_filter';

        $tag_string .= 'geo_object' . $map_suffix . '.map_showview();';

        return $tag_string;
    } // fn GetMapFilterCenter

    // search functions

    /**
     * Gives the header part for the map front end search by map-based rectangle selection
     * the optional p_bboxDivs array of divs for automatical setting of the box corners coordinates.
     * The bounding-box corners are available by js calls too (see e.g. locations/search.php).
     *
     * @param int $p_mapWidth
     * @param int $p_mapHeight
     * @param mixed $p_bboxDivs
     *
     * @return string
     */
    public static function GetMapSearchHeader($p_mapWidth = 0, $p_mapHeight = 0, $p_bboxDivs = null)
    {
        global $Campsite;
        $tag_string = '';

        $map_suffix = '_search';

        $cnf_html_dir = $Campsite['HTML_DIR'];
        $cnf_website_url = $Campsite['WEBSITE_URL'];

        $map_provider = Geo_Preferences::GetMapProviderDefault();
        $geo_map_info = Geo_Preferences::GetMapInfo($cnf_html_dir, $cnf_website_url, $map_provider);
        if (0 < $p_mapWidth)
        {
            $geo_map_info['width'] = $p_mapWidth;
        }
        if (0 < $p_mapHeight)
        {
            $geo_map_info['height'] = $p_mapHeight;
        }

        $geo_map_incl = Geo_Preferences::PrepareMapIncludes($geo_map_info['incl_obj']);
        $geo_map_json = '';
        $geo_map_json .= json_encode($geo_map_info['json_obj']);

        $geo_icons_info = Geo_Preferences::GetSearchInfo($cnf_html_dir, $cnf_website_url);
        $geo_icons_json = '';
        $geo_icons_json .= json_encode($geo_icons_info['json_obj']);

        $geocodingdir = $Campsite['WEBSITE_URL'] . '/js/geocoding/';


        $tag_string .= $geo_map_incl;
        $tag_string .= "\n";

        $tag_string .= '

    <script type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/geocoding/map_popups.js"></script>
    <script type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/geocoding/openlayers/OpenLayers.js"></script>
    <script type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/geocoding/openlayers/OLlocals.js"></script>
    <script type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/geocoding/map_search.js"></script>

<script type="text/javascript">

    geo_object'. $map_suffix .' = new geo_locations();

var useSystemParameters = function()
{
';

    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_map_info($geo_map_json);";
    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_icons_info($geo_icons_json);";
    $tag_string .= "\n";

        if ($p_bboxDivs)
        {
            $bbox_divs_json = '';
            $bbox_divs_json .= json_encode($p_bboxDivs);

            $tag_string .= "geo_object$map_suffix.set_bbox_divs($bbox_divs_json);";
            $tag_string .= "\n";

        }

        $tag_string .= '
};
var on_load_proc = function()
{

    var map_obj = document.getElementById ? document.getElementById("geo_map_mapcanvas' . $map_suffix . '") : null;
    if (map_obj)
    {
        map_obj.style.width = "' . $geo_map_info['width'] . 'px";
        map_obj.style.height = "' . $geo_map_info['height'] . 'px";

        geo_main_selecting_locations(geo_object' . $map_suffix . ', "' . $geocodingdir. '", "geo_map_mapcanvas' . $map_suffix. '", "map_sidedescs", "", "", true);

    }
};
    $(document).ready(function()
    {
        on_load_proc();
    });
</script>
';

        return $tag_string;

    } // fn GetMapSearchHeader

    /**
     * Gives the body map-placement part for the map front end search by map-based rectangle selection
     *
     * @return string
     */
    public static function GetMapSearchBody()
    {
        global $Campsite;
        $tag_string = '';

        $map_suffix = '_search';

        $tag_string .= "<div id=\"geo_map_mapcanvas$map_suffix\"></div>\n";

        return $tag_string;
    } // fn GetMapSearchBody

    /**
     * Gives the body map-centering (js call) part for the map front end search by map-based rectangle selection
     *
     * @return string
     */
    public static function GetMapSearchCenter()
    {
        global $Campsite;
        $tag_string = '';

        $map_suffix = '_search';

        $tag_string .= 'geo_object' . $map_suffix . '.map_showview();';

        return $tag_string;
    } // fn GetMapSearchCenter

    /**
     * Gives the SQL query for article searching via their point inside the box specified by the p_coordinates.
     * The (two) corner lon/lat coordinates should go west to east.
     *
     * @param array $p_coordinates
     *
     * @return string
     */
    public static function GetGeoSearchSQLQuery($p_coordinates)
    {
        $queryStr = '';
        $queryStr_1 = '';
        $queryStr_2 = '';
        $queryStr_end = '';

        $queryStr .= 'SELECT DISTINCT m.fk_article_number AS Number FROM Maps AS m INNER JOIN MapLocations AS ml ON m.id = ml.fk_map_id INNER JOIN ';
        $queryStr .= 'Locations AS l ON ml.fk_location_id = l.id WHERE ';

        $cons_res = Geo_MapLocation::GetGeoSearchSQLCons($p_coordinates, 'rectangle', 'l');
        if ($cons_res['error']) {return '';}

        $queryStr .= $cons_res['cons'];

        $queryStr_end .= 'AND m.fk_article_number != 0';
        $queryStr .= $queryStr_end;

        return $queryStr;

    } // fn GetGeoSearchSQLQuery

} // class Geo_Map

