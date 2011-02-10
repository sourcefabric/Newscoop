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
                    $map_ids[] = array("id" => (int) $row['id'],
                                       "usage" => (int) $row['usage']);
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

        $queryStr = "SELECT lc.poi_name AS name, mll.poi_display AS display ";
        $queryStr .= "FROM Maps AS m INNER JOIN MapLocations AS ml ON ml.fk_map_id = m.id ";
        $queryStr .= "INNER JOIN MapLocationLanguages AS mll ON mll.fk_maplocation_id = ml.id ";
        $queryStr .= "INNER JOIN LocationContents AS lc ON lc.id = mll.fk_content_id ";
        $queryStr .= "WHERE m.fk_article_number = ? AND mll.fk_language_id = ? ORDER BY ml.rank, ml.id";

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
                    $poi_names[] = array("name" => $row['name'], "display" => $row['display']);
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

        $queryStr = "UPDATE Maps SET fk_article_number = 0 WHERE fk_article_number = ?";

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

        $queryStr_sel = "SELECT mll.id AS mll_id, mll.fk_content_id AS con_id FROM MapLocationLanguages AS mll ";
        $queryStr_sel .= "INNER JOIN MapLocations AS ml ON mll.fk_maplocation_id = ml.id ";
        $queryStr_sel .= "INNER JOIN Maps AS m ON m.id = ml.fk_map_id ";
        $queryStr_sel .= "WHERE m.fk_article_number = ? AND mll.fk_language_id = ?";

        $list_fill = "%%id_list%%";
        $queryStr_mll_del = "DELETE FROM MapLocationLanguages WHERE id IN (%%id_list%%)";

        $queryStr_con_del = "DELETE FROM LocationContents WHERE id = ? AND NOT EXISTS (SELECT id FROM MapLocationLanguages WHERE fk_content_id = ?)";

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

        $mll_string = implode(", ", $mll_ids);

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

        $queryStr_sel = "SELECT id FROM MapLocations WHERE fk_map_id = ?";

        $queryStr_del = "DELETE FROM Maps WHERE id = ?";

        $ml_ids = array();
        try
        {
            $sel_params = array();
            $sel_params[] = $this->m_data['id'];

            $rows = $g_ado_db->GetAll($queryStr_sel, $sel_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $ml_ids[] = array("location_id" => $row["id"]);
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
        $list_fill = "%%id_list%%";
        $lang_fill = "%%id_langs%%";

        $map_columns = array("fk_article_number", "MapRank", "MapUsage", "MapCenterLongitude", "MapCenterLatitude", "MapDisplayResolution", "MapProvider", "MapWidth", "MapHeight", "MapName", "IdUser");
        $map_colstr = implode(", ", $map_columns);
        $map_colqms = implode(", ", str_split(str_repeat("?", count($map_columns))));
        $queryStr_map_sel = "SELECT id, $map_colstr FROM Maps WHERE fk_article_number = ?";
        $queryStr_map_ins = "INSERT INTO Maps ($map_colstr) VALUES ($map_colqms)";

        $maploc_columns = array("fk_map_id", "fk_location_id", "poi_style", "rank");
        $maploc_colstr = implode(", ", $maploc_columns);
        $maploc_colqms = implode(", ", str_split(str_repeat("?", count($maploc_columns))));
        $queryStr_maploc_sel = "SELECT id, $maploc_colstr FROM MapLocations WHERE fk_map_id IN (%%id_list%%)";
        $queryStr_maploc_ins = "INSERT INTO MapLocations ($maploc_colstr) VALUES ($maploc_colqms)";

        $maploclan_columns = array("fk_maplocation_id", "fk_language_id", "fk_content_id", "poi_display");
        $maploclan_colstr = implode(", ", $maploclan_columns);
        $maploclan_colqms = implode(", ", str_split(str_repeat("?", count($maploclan_columns))));
        $queryStr_maploclan_sel = "SELECT $maploclan_colstr FROM MapLocationLanguages WHERE fk_maplocation_id IN (%%id_list%%) AND fk_language_id IN (%%id_langs%%)";
        $queryStr_maploclan_ins = "INSERT INTO MapLocationLanguages ($maploclan_colstr) VALUES ($maploclan_colqms)";

        $maplocmed_columns = array("fk_maplocation_id", "fk_multimedia_id");
        $maplocmed_colstr = implode(", ", $maplocmed_columns);
        $maplocmed_colqms = implode(", ", str_split(str_repeat("?", count($maplocmed_columns))));
        $queryStr_maplocmed_sel = "SELECT $maplocmed_colstr FROM MapLocationMultimedia WHERE fk_maplocation_id IN (%%id_list%%)";
        $queryStr_maplocmed_ins = "INSERT INTO MapLocationMultimedia ($maplocmed_colstr) VALUES ($maplocmed_colqms)";

        if (0 == count($p_copyTranslations)) {return;}
        $lang_str = implode(", ", $p_copyTranslations);


        $map_ids = array();

        $map_sel_params = array();
        $map_sel_params[] = $p_srcArticleNumber;
		$rows = $g_ado_db->GetAll($queryStr_map_sel, $map_sel_params);
		foreach ($rows as $row) {
            $old_map_id = $row["id"];
            $new_user_id = $p_userId;
            if (is_null($new_user_id)) {$new_user_id = $row["IdUser"];}

            $map_ins_params = array();
            $map_ins_params[] = $p_destArticleNumber;
            $map_ins_params[] = $row["MapRank"];
            $map_ins_params[] = $row["MapUsage"];
            $map_ins_params[] = $row["MapCenterLongitude"];
            $map_ins_params[] = $row["MapCenterLatitude"];
            $map_ins_params[] = $row["MapDisplayResolution"];
            $map_ins_params[] = $row["MapProvider"];
            $map_ins_params[] = $row["MapWidth"];
            $map_ins_params[] = $row["MapHeight"];
            $map_ins_params[] = $row["MapName"];
            $map_ins_params[] = $new_user_id;

            $success = $g_ado_db->Execute($queryStr_map_ins, $map_ins_params);
            // taking the map ID
            $new_map_id = $g_ado_db->Insert_ID();
            $map_ids[$old_map_id] = $new_map_id;
        }
        if (0 == count($map_ids)) {return;}

        $map_ids_str = implode(", ", array_keys($map_ids));


        $queryStr_maploc_sel = str_replace($list_fill, $map_ids_str, $queryStr_maploc_sel);
        $maploc_ids = array();

        $maploc_sel_params = array();

		$rows = $g_ado_db->GetAll($queryStr_maploc_sel, $maploc_sel_params);
		foreach ($rows as $row) {
            $old_maploc_id = $row["id"];
            $old_map_id = $row["fk_map_id"];
            $new_map_id = $map_ids[$old_map_id];

            $maploc_ins_params = array();
            $maploc_ins_params[] = $new_map_id;
            $maploc_ins_params[] = $row["fk_location_id"];
            $maploc_ins_params[] = $row["poi_style"];
            $maploc_ins_params[] = $row["rank"];

            $success = $g_ado_db->Execute($queryStr_maploc_ins, $maploc_ins_params);
            // taking the map ID
            $new_maploc_id = $g_ado_db->Insert_ID();
            $maploc_ids[$old_maploc_id] = $new_maploc_id;
        }
        if (0 == count($maploc_ids)) {return;}

        $maploc_ids_str = implode(", ", array_keys($maploc_ids));


        $queryStr_maploclan_sel = str_replace($list_fill, $maploc_ids_str, $queryStr_maploclan_sel);
        $queryStr_maploclan_sel = str_replace($lang_fill, $lang_str, $queryStr_maploclan_sel);
        $maploclan_sel_params = array();

		$rows = $g_ado_db->GetAll($queryStr_maploclan_sel, $maploclan_sel_params);
		foreach ($rows as $row) {
            $old_maploc_id = $row["fk_maplocation_id"];
            $new_maploc_id = $maploc_ids[$old_maploc_id];

            $maploclan_ins_params = array();
            $maploclan_ins_params[] = $new_maploc_id;
            $maploclan_ins_params[] = $row["fk_language_id"];
            $maploclan_ins_params[] = $row["fk_content_id"];
            $maploclan_ins_params[] = $row["poi_display"];

            $success = $g_ado_db->Execute($queryStr_maploclan_ins, $maploclan_ins_params);
        }


        $queryStr_maplocmed_sel = str_replace($list_fill, $maploc_ids_str, $queryStr_maplocmed_sel);
        $maplocmed_sel_params = array();

		$rows = $g_ado_db->GetAll($queryStr_maplocmed_sel, $maplocmed_sel_params);
		foreach ($rows as $row) {
            $old_maploc_id = $row["fk_maplocation_id"];
            $new_maploc_id = $maploc_ids[$old_maploc_id];

            $maplocmed_ins_params = array();
            $maplocmed_ins_params[] = $new_maploc_id;
            $maplocmed_ins_params[] = $row["fk_multimedia_id"];

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

        $queryStr_sel = "SELECT mll.fk_maplocation_id AS ml_id, mll.fk_content_id AS con_id, mll.poi_display AS display ";
        $queryStr_sel .= "FROM Maps AS m INNER JOIN MapLocations AS ml ON ml.fk_map_id = m.id ";
        $queryStr_sel .= "INNER JOIN MapLocationLanguages AS mll ON mll.fk_maplocation_id = ml.id ";
        $queryStr_sel .= "WHERE m.fk_article_number = ? AND mll.fk_language_id = ?";

        $queryStr_ins = "INSERT INTO MapLocationLanguages (fk_maplocation_id, fk_language_id, fk_content_id, poi_display) ";
        $queryStr_ins .= "VALUES (?, ?, ?, ?)";

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
                    $ins_params[] = $row["ml_id"];
                    $ins_params[] = (int) $p_destLanguageId;
                    $ins_params[] = $row["con_id"];
                    //$ins_params[] = $row["display"];
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

        return array(
            //'pois' => Geo_Map::ReadMapPoints((int) $p_mapId, (int) $p_languageId, $p_preview, $p_textOnly),
            'pois' => Geo_MapLocation::GetListExt(array("asArray" => true, "mapId" => $p_mapId, "languageId" => $p_languageId, "preview" => $p_preview, "textOnly" => $p_textOnly, "mapCons" => array()), (array) null, 0, 0, $poi_count),
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
	public static function StoreMapData($p_mapId, $p_languageId, $p_articleNumber, $p_map = "", $p_remove = "", $p_insert = "", $p_locations = "", $p_contents = "", $p_order = "")
	{
        $security_problem = array("status" => "403", "description" => "Invalid security token!");
        $unknown_request = array("status" => "404", "description" => "Unknown request!");
        $data_wrong = array("status" => "404", "description" => "Wrong data.");


        $status = true;
        
        if ("" != $p_map)
        {
            $map_data = array();
            try
            {
                $p_map = str_replace("%2B", "+", $p_map);
                $p_map = str_replace("%2F", "/", $p_map);
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
    
        if ("" != $p_remove)
        {
            $remove_data = array();
            try
            {
                $p_remove = str_replace("%2B", "+", $p_remove);
                $p_remove = str_replace("%2F", "/", $p_remove);
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
        if ("" != $p_insert)
        {
            $insert_data = array();
            try
            {
                $p_insert = str_replace("%2B", "+", $p_insert);
                $p_insert = str_replace("%2F", "/", $p_insert);
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
    
    
        if ("" != $p_locations)
        {
            $locations_data = array();
            try
            {
                $p_locations = str_replace("%2B", "+", $p_locations);
                $p_locations = str_replace("%2F", "/", $p_locations);
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
    
    
        if ("" != $p_contents)
        {
            $contents_data = array();
            try
            {
    
                $p_contents = str_replace("%2B", "+", $p_contents);
                $p_contents = str_replace("%2F", "/", $p_contents);
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
    
        if ("" != $p_order)
        {
            $order_data = array();
            try
            {
                $p_order = str_replace("%2B", "+", $p_order);
                $p_order = str_replace("%2F", "/", $p_order);
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
    
        $geo_map_usage = Geo_Map::ReadMapInfo("map", $p_mapId);

        //$found_list = Geo_Map::ReadMapPoints($p_mapId, $p_languageId);
        $poi_count = 0;
        $found_list = Geo_MapLocation::GetListExt(array("asArray" => true, "mapId" => $p_mapId, "languageId" => $p_languageId, "preview" => false, "textOnly" => false, "mapCons" => array()), (array) null, 0, 0, $poi_count);

    
        $res_array = array("status" => "200", "pois" => $found_list, "map" => $geo_map_usage);

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
        $map_info["id"] = 0;

        $queryStr_common = "SELECT id, MapCenterLongitude AS lon, MapCenterLatitude AS lat, MapDisplayResolution AS res, MapProvider AS prov, MapWidth AS width, MapHeight AS height, MapName AS name ";
        $queryStr_common .= "FROM Maps ";

        $queryStr_art = $queryStr_common;
        $queryStr_art .= "WHERE fk_article_number = ? AND MapUsage = 1 ORDER BY MapRank, id LIMIT 1";

        $queryStr_map = $queryStr_common;
        $queryStr_map .= "WHERE id = ?";

        $queryStr = "";
        $sql_params = array();
        $sql_params[] = $p_id;
        if ("map" == $p_type)
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
                    $map_info["id"] = $row['id'];
                    $map_info["lon"] = $row['lon'];
                    $map_info["lat"] = $row['lat'];
                    $map_info["res"] = $row['res'];
                    $map_info["prov"] = $row['prov'];
                    $map_info["width"] = $row['width'];
                    $map_info["height"] = $row['height'];
                    $map_info["name"] = $row['name'];
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
        $map_data = $geo_map_info["json_obj"];

        $map_info = array();
        $map_info["id"] = 0;
        $map_info["lon"] = $map_data["longitude"];
        $map_info["lat"] = $map_data["latitude"];
        $map_info["res"] = $map_data["resolution"];
        $map_info["prov"] = $map_data["default"];
        $map_info["width"] = $map_data["width"];
        $map_info["height"] = $map_data["height"];
        $map_info["name"] = "Multimap";

        return $map_info;
    }


    /**
     * Provides information on map's points
     *
     * @param int $p_mapId
     * @param int $p_languageId
     * @param bool $p_preview
     * @param bool $p_textOnly
     *
     * @return array
     */
/*
	public static function ReadMapPoints($p_mapId, $p_languageId, $p_preview = false, $p_textOnly = false, $p_mapCons = null)
	{
        if (0 == $p_mapId && (!$p_mapCons)) {return array();}

		global $g_ado_db;

		$sql_params = array();

        $list_fill = "%%id_list%%";

		$queryStr = "SELECT ml.id AS ml_id, mll.id as mll_id, ml.fk_location_id AS loc_id, mll.fk_content_id AS con_id, ";
        $queryStr .= "ml.poi_style AS poi_style, ml.rank AS rank, mll.poi_display AS poi_display, ";

        $queryStr .= "AsText(l.poi_location) AS loc, l.poi_type AS poi_type, l.poi_type_style AS poi_type_style, ";

        $queryStr .= "c.poi_name AS poi_name, c.poi_link AS poi_link, c.poi_perex AS poi_perex, ";
        $queryStr .= "c.poi_content_type AS poi_content_type, c.poi_content AS poi_content, c.poi_text AS poi_text ";

        $queryStr .= "FROM MapLocations AS ml INNER JOIN MapLocationLanguages AS mll ON ml.id = mll.fk_maplocation_id ";
        $queryStr .= "INNER JOIN Locations AS l ON l.id = ml.fk_location_id ";
        $queryStr .= "INNER JOIN LocationContents AS c ON c.id = mll.fk_content_id ";

        $query_mcons = "";
        $article_mcons = false;
        $mc_limit = false;
        $to_filter = false;
        $mc_filter_mm = false;
        $mc_filter_image = false;
        $mc_filter_video = false;

        if ($p_mapCons)
        {
            $queryStr .= "INNER JOIN Maps AS m ON m.id = ml.fk_map_id ";
            $queryStr .= "INNER JOIN Articles AS a ON m.fk_article_number = a.Number ";
            $query_mcons = "";
            $article_mcons = false;

            $mc_order_type = strtolower($p_mapCons["order"]);
            $mc_order = "DESC";
            if ("asc" == $mc_order_type) {
                $mc_order = "ASC";
            }
            $mc_limit = 0 + $p_mapCons["limit"];
            if (0 > $mc_limit) {$mc_limit = 0;}

            $mc_multimedia = $p_mapCons["multimedia"];
            $mc_articles = $p_mapCons["articles"];
            $mc_issues = $p_mapCons["issues"];
            $mc_sections = $p_mapCons["sections"];
            $mc_dates = $p_mapCons["dates"];
            $mc_topics = $p_mapCons["topics"];
            $mc_areas = $p_mapCons["areas"];
            $mc_correct = true;

            if (0 < count($mc_multimedia)) {
                $mc_filter_mm = $mc_multimedia["any"];
                $mc_filter_image = $mc_multimedia["image"];
                $mc_filter_video = $mc_multimedia["video"];
                if ($mc_filter_mm || $mc_filter_image || $mc_filter_video) {$to_filter = true;}
            }

            if (0 < count($mc_articles)) {
                $mc_correct = true;
                foreach ($mc_articles as $val) {
                    if (!is_numeric($val)) {$mc_articles = false;}
                }
                if ($mc_articles) {
                    $query_mcons .= "a.Number IN (" . implode(", ", $mc_articles) . ") AND ";
                    $article_mcons = true;
                }
            }
            if (0 < count($mc_issues)) {
                $mc_correct = true;
                foreach ($mc_issues as $val) {
                    if (!is_numeric($val)) {$mc_correct = false;}
                }
                if ($mc_correct) {
                    $query_mcons .= "a.NrIssue IN (" . implode(", ", $mc_issues) . ") AND ";
                    $article_mcons = true;
                }
            }
            if (0 < count($mc_sections)) {
                $mc_correct = true;
                foreach ($mc_sections as $val) {
                    if (!is_numeric($val)) {$mc_correct = false;}
                }
                if ($mc_correct) {
                    $query_mcons .= "a.NrSection IN (" . implode(", ", $mc_sections) . ") AND ";
                    $article_mcons = true;
                }
            }
            if (2 == count($mc_dates)) {
                $date_start = str_replace("'", "\"", $mc_dates[0]);
                $date_stop = str_replace("'", "\"", $mc_dates[1]);
    
                $query_mcons .= "a.PublishDate >= '$date_start' AND a.PublishDate <= '$date_stop' AND ";
                $article_mcons = true;
            }
            if (0 < count($mc_topics))
            {
                $mc_correct = true;
                foreach ($mc_topics as $val) {
                    if (!is_numeric($val)) {$mc_correct = false;}
                }
                if ($mc_correct) {
                    $queryStr .= "INNER JOIN ArticleTopics AS at ON a.Number = at.NrArticle ";
                    $query_mcons .= "at.TopicId IN (" . implode(", ", $mc_topics) . ") AND ";
                    $article_mcons = true;
                }

            }

            if ($mc_areas) {
                $mc_rectangle = $mc_areas["rectangle"];
                $mc_clockwise = $mc_areas["clockwise"];
                $mc_counterclockwise = $mc_areas["counterclockwise"];

                if ($mc_rectangle && (2 == count($mc_rectangle))) {
                    $area_cons_res = Geo_Map::GetGeoSearchSQLCons($mc_rectangle, "rectangle", "l");
                    if (!$area_cons_res["error"]) {
                        $query_mcons .= $area_cons_res["cons"] . " AND ";
                        $article_mcons = true;
                    }    
                }

                if ($mc_clockwise && (3 <= count($mc_clockwise))) {
                    $area_cons_res = Geo_Map::GetGeoSearchSQLCons($mc_clockwise, "clockwise", "l");
                    if (!$area_cons_res["error"]) {
                        $query_mcons .= $area_cons_res["cons"] . " AND ";
                        $article_mcons = true;
                    }    
                }

                if ($mc_counterclockwise && (3 <= count($mc_counterclockwise))) {
                    $area_cons_res = Geo_Map::GetGeoSearchSQLCons($mc_counterclockwise, "counterclockwise", "l");
                    if (!$area_cons_res["error"]) {
                        $query_mcons .= $area_cons_res["cons"] . " AND ";
                        $article_mcons = true;
                    }    
                }
            }

            $mmu_test_join = "%%mmu_test_join%%";
            $mmu_test_spec = "%%mmu_test_spec%%";
            $multimedia_test_common = "EXISTS (SELECT mlmu.id FROM MapLocationMultimedia AS mlmu $mmu_test_join WHERE mlmu.fk_maplocation_id = ml.id $mmu_test_spec) AND ";

            $multimedia_test_basic = $multimedia_test_common;
            $multimedia_test_basic = str_replace($mmu_test_join, "", $multimedia_test_basic);
            $multimedia_test_basic = str_replace($mmu_test_spec, "", $multimedia_test_basic);

            $multimedia_test_spec = $multimedia_test_common;
            $multimedia_test_spec = str_replace($mmu_test_join, "INNER JOIN Multimedia AS mu ON mlmu.fk_multimedia_id = mu.id ", $multimedia_test_spec);

            $multimedia_test_image = $multimedia_test_spec;
            $multimedia_test_image = str_replace($mmu_test_spec, "AND mu.media_type = 'image'", $multimedia_test_image);
            $multimedia_test_video = $multimedia_test_spec;
            $multimedia_test_video = str_replace($mmu_test_spec, "AND mu.media_type = 'video'", $multimedia_test_video);


            if ($mc_filter_image) {
                $query_mcons .= $multimedia_test_image;
            }
            if ($mc_filter_video) {
                $query_mcons .= $multimedia_test_video;
            }
            if ($mc_filter_mm) {
                $query_mcons .= $multimedia_test_basic;
            }


            $queryStr .= "WHERE ";
            if ($article_mcons) {
                $queryStr .= $query_mcons;
            }

            $queryStr .= "a.Published = 'Y' AND a.IdLanguage = ? ";
            $sql_params[] = $p_languageId;

        }
        else
        {
            $queryStr .= "WHERE ml.fk_map_id = ? ";
            $sql_params[] = $p_mapId;
        }

        $queryStr .= "AND mll.fk_language_id = ? ";
        $sql_params[] = $p_languageId;

        if ($p_preview)
        {
            $queryStr .= "AND mll.poi_display = 1 ";
        }

        $queryStr .= "ORDER BY ";
        if ($p_mapCons)
        {
            $queryStr .= "a.Number $mc_order, m.id $mc_order, ";
        }
        $queryStr .= "ml.rank, ml.id, mll.id";

        //if ($p_mapCons && $mc_limit && (!$to_filter))
        if ($p_mapCons && $mc_limit)
        {
            $queryStr .= " LIMIT ?";
            $sql_params[] = $mc_limit;
        }

        $tmp_name = "tmp_poi_ids_" . mt_rand();

        $queryStr_mm = "SELECT m.id AS m_id, mlm.id AS mlm_id, ml.id AS ml_id, ";
        $queryStr_mm .= "m.media_type AS media_type, m.media_spec AS media_spec, ";
        $queryStr_mm .= "m.media_src AS media_src, m.media_height AS media_height, m.media_width AS media_width ";
        $queryStr_mm .= "FROM Multimedia AS m INNER JOIN MapLocationMultimedia AS mlm ON m.id = mlm.fk_multimedia_id ";
        $queryStr_mm .= "INNER JOIN MapLocations AS ml ON ml.id = mlm.fk_maplocation_id ";
        $queryStr_mm .= "INNER JOIN $tmp_name AS p ON p.id = ml.id ";

        $queryStr_tt_cr = "CREATE TEMPORARY TABLE $tmp_name (id int(10) unsigned) ENGINE=MEMORY;";
        $queryStr_tt_in = "INSERT INTO $tmp_name (id) VALUES (?);";
        $queryStr_tt_rm = "DROP TABLE $tmp_name;";

        //if ($to_filter || (!$p_textOnly)) {
        if (!$p_textOnly) {
            $success = $g_ado_db->Execute($queryStr_tt_cr);
        }

		$dataArray = array();

		$rows = $g_ado_db->GetAll($queryStr, $sql_params);

		if (is_array($rows)) {
			foreach ($rows as $row) {
                $tmp_loc = trim(strtolower($row['loc']));
                $loc_matches = array();
                if (!preg_match('/^point\((?P<latitude>[\d.-]+)\s(?P<longitude>[\d.-]+)\)$/', $tmp_loc, $loc_matches)) {continue;}
                $tmp_latitude = $loc_matches['latitude'];
                $tmp_longitude = $loc_matches['longitude'];

                $tmpPoint = array();
				$tmpPoint['latitude'] = $tmp_latitude;
				$tmpPoint['longitude'] = $tmp_longitude;

                $tmpPoint['loc_id'] = $row['ml_id'];
                $tmpPoint['con_id'] = $row['mll_id'];

                $tmpPoint['style'] = $row['poi_style'];
                $tmpPoint['rank'] = $row['rank'];
                $tmpPoint['display'] = $row['poi_display'];

				$tmpPoint['title'] = $row['poi_name'];
				$tmpPoint['link'] = $row['poi_link'];

				$tmpPoint['perex'] = $row['poi_perex'];
				$tmpPoint['content_type'] = $row['poi_content_type'];
				$tmpPoint['content'] = $row['poi_content'];
				$tmpPoint['text'] = $row['poi_text'];

				$tmpPoint['image_mm'] = 0;
				$tmpPoint['image_src'] = "";
				$tmpPoint['image_width'] = "";
				$tmpPoint['image_height'] = "";

				$tmpPoint['video_mm'] = 0;
				$tmpPoint['video_id'] = "";
				$tmpPoint['video_type'] = "";
				$tmpPoint['video_width'] = "";
				$tmpPoint['video_height'] = "";

                $dataArray[] = $tmpPoint;

                //if ($to_filter || (!$p_textOnly)) {
                if (!$p_textOnly) {
                    $success = $g_ado_db->Execute($queryStr_tt_in, array($row['ml_id']));
                }

            }
        }

        if (0 == count($dataArray)) {return $dataArray;}
        //if ((!$to_filter) && $p_textOnly) {return $dataArray;}
        if ($p_textOnly) {return $dataArray;}

        {
            $imagesArray = array();
            $videosArray = array();
    
            $rows = $g_ado_db->GetAll($queryStr_mm);
    
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $tmpPoint = array();
                    $tmpPoint["m_id"] = $row["m_id"];
                    $tmpPoint["mlm_id"] = $row["mlm_id"];
                    $tmpPoint["ml_id"] = $row["ml_id"];
                    $tmpPoint["type"] = $row["media_type"];
                    $tmpPoint["spec"] = $row["media_spec"];
                    $tmpPoint["src"] = $row["media_src"];
                    $tmpPoint["width"] = $row["media_width"];
                    $tmpPoint["height"] = $row["media_height"];
    
                    $tmp_id = $row["ml_id"];
                    $tmp_type = $row["media_type"];
                    if ("image" == $tmp_type)
                    {
                        $imagesArray[$tmp_id] = $tmpPoint;
                    }
                    if ("video" == $tmp_type)
                    {
                        $videosArray[$tmp_id] = $tmpPoint;
                    }
                }
            }

        }

        //$mm_filter = array();
        //$required_mm = ($mc_filter_mm) ? true : false;
        //$required_image = ($mc_filter_image) ? true : false;
        //$required_video = ($mc_filter_video) ? true : false;

        foreach ($dataArray AS $index => $poi)
        {
            //$with_image = false;
            //$with_video = false;

            $ml_id = $poi["loc_id"];
            if (array_key_exists($ml_id, $imagesArray))
            {
                //$with_image = true;
                //if (!$p_textOnly) {
                    $dataArray[$index]["image_mm"] = $imagesArray[$ml_id]["mlm_id"];
                    $dataArray[$index]["image_src"] = $imagesArray[$ml_id]["src"];
                    $dataArray[$index]["image_width"] = $imagesArray[$ml_id]["width"];
                    $dataArray[$index]["image_height"] = $imagesArray[$ml_id]["height"];
                //}
            }
            if (array_key_exists($ml_id, $videosArray))
            {
                //$with_video = true;
                //if (!$p_textOnly) {
                    $dataArray[$index]["video_mm"] = $videosArray[$ml_id]["mlm_id"];
                    $dataArray[$index]["video_id"] = $videosArray[$ml_id]["src"];
                    $dataArray[$index]["video_type"] = $videosArray[$ml_id]["spec"];
                    $dataArray[$index]["video_width"] = $videosArray[$ml_id]["width"];
                    $dataArray[$index]["video_height"] = $videosArray[$ml_id]["height"];
                //}
            }


            //if ($to_filter)
            //{
            //    $mm_satis = true;
            //    if ($required_mm) {
            //        if ((!$with_image) && (!$with_video)) {$mm_satis = false;}
            //    }
            //    if ($required_image) {
            //        if (!$with_image) {$mm_satis = false;}
            //    }
            //    if ($required_video) {
            //        if (!$with_video) {$mm_satis = false;}
            //    }
            //    if ($mm_satis) {$mm_filter[$index] = true;}
            //}

        }

        //if ($to_filter) {
        //    $dataArray = array_intersect_key($dataArray, $mm_filter);
        //}

        //if (!$p_textOnly) {
            $success = $g_ado_db->Execute($queryStr_tt_rm);
        //}

        //if ($mc_limit) {
        //    $dataArray = array_splice($dataArray, 0, $mc_limit);
        //}

        $dataArrayObj = array();
        $dataArray_tmp = $dataArray;
        $dataArray = array();
        foreach ($dataArray_tmp as $one_poi)
        {
            $dataArrayObj[] = new self((array) $one_poi);
            $dataArray[] = $one_poi;
        }

		return $dataArray;

	} // fn ReadMapPoints
*/

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

        $queryStr_langs = "SELECT mll.fk_language_id AS lang FROM MapLocationLanguages AS mll ";
        $queryStr_langs .= "INNER JOIN MapLocations AS ml ON mll.fk_maplocation_id = ml.id ";
        $queryStr_langs .= "INNER JOIN Maps AS m ON ml.fk_map_id = m.id ";
        $queryStr_langs .= "WHERE m.id = ?";

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

        $queryStr_langs = "SELECT IdLanguage AS lang FROM Articles WHERE Number = ?";

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
        $queryStr_map_id = "SELECT id AS map FROM Maps WHERE fk_article_number = ? AND MapRank = ? ORDER BY id LIMIT 1";

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
        $queryStr_map_new = "INSERT INTO Maps (MapCenterLongitude, MapCenterLatitude, MapDisplayResolution, MapProvider, MapWidth, MapHeight, MapName, MapRank, fk_article_number, IdUser) ";
        $queryStr_map_new .= "VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, %%user_id%%)";

        // update the map, if it already exists
        $queryStr_map_up = "UPDATE Maps SET MapCenterLongitude = ?, MapCenterLatitude = ?, MapDisplayResolution = ?, MapProvider = ?, MapWidth = ?, MapHeight = ?, MapName = ? WHERE id = ?";


        $map_val_params = array();
        $map_val_params[] = $p_map["cen_lon"];
        $map_val_params[] = $p_map["cen_lat"];
        $map_val_params[] = $p_map["zoom"];
        $map_val_params[] = $p_map["provider"];
        $map_val_params[] = $p_map["width"];
        $map_val_params[] = $p_map["height"];
        $map_val_params[] = $p_map["name"];
        if (0 == $p_mapId)
        {
            // the way for a new map
            try
            {
                $map_val_params[] = $p_articleNumber;
                // create the new map

                // it has to be safe to use the user id directly
                $queryStr_map_new = str_replace("%%user_id%%", $g_user->getUserId(), $queryStr_map_new);

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

            $val_rem = $one_rem["location_id"];
            if (is_numeric($val_rem))
            {
                $id_rem = 0 + $val_rem;
                if (is_int($id_rem)) {$map_loc_ids[] = $id_rem;}
            }
        }
        if (0 == count($map_loc_ids)) {return 0;}

        $map_loc_list = implode(", ", $map_loc_ids);

        $list_fill = "%%id_list%%";
        // ad B 1)
        $queryStr_maploc_sel = "SELECT fk_location_id AS loc FROM MapLocations WHERE id IN (%%id_list%%)";
        // ad B 2)
        $queryStr_maploclan_sel = "SELECT fk_content_id AS con FROM MapLocationLanguages WHERE fk_maplocation_id IN (%%id_list%%)";
        // ad B 3)
        $queryStr_maplocmed_sel = "SELECT fk_multimedia_id AS med FROM MapLocationMultimedia WHERE fk_maplocation_id IN (%%id_list%%)";


        // ad C 1)
        $queryStr_maploc_del = "DELETE FROM MapLocations WHERE id IN (%%id_list%%)";
        // ad C 2)
        $queryStr_maploclan_del = "DELETE FROM MapLocationLanguages WHERE fk_maplocation_id IN (%%id_list%%)";
        // ad C 3)
        $queryStr_maplocmed_del = "DELETE FROM MapLocationMultimedia WHERE fk_maplocation_id IN (%%id_list%%)";

        // ad D 1)
        $queryStr_locpos_del = "DELETE FROM Locations WHERE id = ? AND NOT EXISTS (SELECT id FROM MapLocations WHERE fk_location_id = ?)";
        // ad D 2)
        $queryStr_loccon_del = "DELETE FROM LocationContents WHERE id = ? AND NOT EXISTS (SELECT id FROM MapLocationLanguages WHERE fk_content_id = ?)";
        // ad D 3)
        $queryStr_locmed_del = "DELETE FROM Multimedia WHERE id = ? AND NOT EXISTS (SELECT id FROM MapLocationMultimedia WHERE fk_multimedia_id = ?)";

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
		$queryStr_loc_in = "INSERT INTO Locations (poi_location, poi_type, poi_type_style, poi_center, poi_radius, IdUser) VALUES (";
        $queryStr_loc_in .= "GeomFromText('POINT(? ?)'), 'point', 0, PointFromText('POINT(? ?)'), 0, %%user_id%%";
        $queryStr_loc_in .= ")";
        // ad B 3)
        // ad B 5)
        $queryStr_maploc = "INSERT INTO MapLocations (fk_map_id, fk_location_id, poi_style, rank) ";
        $queryStr_maploc .= "VALUES (?, ?, ?, 0)";
        // ad B 7)
        $queryStr_maploclan = "INSERT INTO MapLocationLanguages (fk_maplocation_id, fk_language_id, fk_content_id, poi_display) ";
        $queryStr_maploclan .= "VALUES (?, ?, ?, ?)";


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
                $new_loc[] = array('latitude' => $poi["latitude"], 'longitude' => $poi["longitude"]);
                $new_cen = array('latitude' => $poi["latitude"], 'longitude' => $poi["longitude"]);
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
                    $loc_in_params[] = $poi["latitude"];
    
                    $loc_in_params[] = $poi["longitude"];
                    $loc_in_params[] = $poi["latitude"];
                    $loc_in_params[] = $poi["longitude"];
    
                    // the POI itself insertion
                    $queryStr_loc_in = str_replace("%%user_id%%", $g_user->getUserId(), $queryStr_loc_in);

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
                $maploc_params[] = "" . $poi["style"];
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
                $maploclan_params[] = 0 + $poi["display"];
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
     * Gives the header part for the map front end presentation
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @param int $p_mapWidth
     * @param int $p_mapHeight
     *
     * @return string
     */
    public static function GetMapTagHeader($p_articleNumber, $p_languageId, $p_mapWidth = 0, $p_mapHeight = 0, $p_autoFocus = null)
    {
        global $Campsite;
        $tag_string = "";

        $f_article_number = $p_articleNumber;
        $f_language_id = $p_languageId;

        $map_suffix = "_" . $f_article_number . "_" . $f_language_id;

        $cnf_html_dir = $Campsite['HTML_DIR'];
        $cnf_website_url = $Campsite['WEBSITE_URL'];
        
        $geo_map_usage = Geo_Map::ReadMapInfo("article", $f_article_number);
        if (0 < $p_mapWidth)
        {
            $geo_map_usage['width'] = $p_mapWidth;
        }
        if (0 < $p_mapHeight)
        {
            $geo_map_usage['height'] = $p_mapHeight;
        }

        $geo_map_usage_json = "";
        $geo_map_usage_json .= json_encode($geo_map_usage);

        $geo_map_info = Geo_Preferences::GetMapInfo($cnf_html_dir, $cnf_website_url, $geo_map_usage['prov']);
        $geo_map_incl = Geo_Preferences::PrepareMapIncludes($geo_map_info["incl_obj"]);
        $geo_map_json = "";
        $geo_map_json .= json_encode($geo_map_info["json_obj"]);

        $geo_icons_info = Geo_Preferences::GetIconsInfo($cnf_html_dir, $cnf_website_url);
        $geo_icons_json = "";
        $geo_icons_json .= json_encode($geo_icons_info["json_obj"]);
        
        $geo_popups_info = Geo_Preferences::GetPopupsInfo($cnf_html_dir, $cnf_website_url);
        $geo_popups_json = "";
        $geo_popups_json .= json_encode($geo_popups_info["json_obj"]);

        $geo_focus_info = Geo_Preferences::GetFocusInfo($cnf_html_dir, $cnf_website_url);
        if (null !== $p_autoFocus)
        {
            $geo_focus_info["json_obj"]["auto_focus"] = $p_autoFocus;
        }
        $geo_focus_json = "";
        $geo_focus_json .= json_encode($geo_focus_info["json_obj"]);
        
        $map_id = Geo_Map::GetMapIdByArticle($f_article_number);

        $preview = true;
        $poi_info = Geo_Map::LoadMapData($map_id, $f_language_id, $f_article_number, $preview);
        
        //$poi_info_json = str_replace("'", "\\'", json_encode($poi_info));
        $poi_info_json = json_encode($poi_info);
        
        $geocodingdir = $Campsite['WEBSITE_URL'] . '/javascript/geocoding/';

        $include_files = Geo_Preferences::GetIncludeCSS($cnf_html_dir, $cnf_website_url);
        $include_files_css = $include_files["css_files"];
        $include_files_tags = "";
        foreach ($include_files_css as $css_file)
        {
            $include_files_tags .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$css_file\" />\n";
        }

        $tag_string .= $geo_map_incl;
        $tag_string .= "\n";

        $tag_string .= $include_files_tags;

        $tag_string .= '

	<script type="text/javascript" src="' . $Campsite["WEBSITE_URL"] . '/javascript/geocoding/map_popups.js"></script>
	<script type="text/javascript" src="' . $Campsite["WEBSITE_URL"] . '/javascript/geocoding/openlayers/OpenLayers.js"></script>
	<script type="text/javascript" src="' . $Campsite["WEBSITE_URL"] . '/javascript/geocoding/openlayers/OLlocals.js"></script>
	<script type="text/javascript" src="' . $Campsite["WEBSITE_URL"] . '/javascript/geocoding/map_preview.js"></script>

<script type="text/javascript">
    geo_object'. $map_suffix .' = new geo_locations();
var geo_on_load_proc_map' . $map_suffix . ' = function()
{

    var map_obj = document.getElementById ? document.getElementById("geo_map_mapcanvas' . $map_suffix . '") : null;
    if (map_obj)
    {
        map_obj.style.width = "' . $geo_map_usage["width"] . 'px";
        map_obj.style.height = "' . $geo_map_usage["height"] . 'px";
';

    $article_spec_arr = array("language_id" => $f_language_id, "article_number" => $f_article_number);
    $article_spec = json_encode($article_spec_arr);

    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_article_spec($article_spec);";
    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_auto_focus($geo_focus_json);";
    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_map_info($geo_map_json);";
    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_map_usage($geo_map_usage_json);";
    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_icons_info($geo_icons_json);";
    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_popups_info($geo_popups_json);";
    $tag_string .= "\n";

        $tag_string .= '

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

};

    $(document).ready(function()
    {
        setTimeout("geo_on_load_proc_map' . $map_suffix . '();", 0);
    });
</script>
';

        return $tag_string;

    } // fn GetMapTagHeader

    /**
     * Gives the body map-placement part for the map front end presentation
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     *
     * @return string
     */
    public static function GetMapTagBody($p_articleNumber, $p_languageId)
    {
        global $Campsite;
        $tag_string = "";

        $f_article_number = $p_articleNumber;
        $f_language_id = $p_languageId;

        $map_suffix = "_" . $f_article_number . "_" . $f_language_id;

        $tag_string .= "<div id=\"geo_map_mapcanvas$map_suffix\" class=\"geo_map_mapcanvas\"></div>\n";

        return $tag_string;
    } // fn GetMapTagBody

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
        $tag_string = "";

        $f_article_number = $p_articleNumber;
        $f_language_id = $p_languageId;

        $map_suffix = "_" . $f_article_number . "_" . $f_language_id;

        $tag_string .= "geo_object" . $map_suffix . ".map_showview();";

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
        $map_suffix = "_" . $f_article_number . "_" . $f_language_id;
        $map_id = Geo_Map::GetMapIdByArticle($f_article_number);
        $preview = true;
        $text_only = true;

        $poi_info = Geo_Map::LoadMapData($map_id, $f_language_id, $f_article_number, $preview, $text_only);
        $pind = 0;
        foreach ($poi_info["pois"] as $rank => $poi) {
            $cur_lon = $poi["longitude"];
            $cur_lat = $poi["latitude"];
            $center = "geo_object$map_suffix.center_lonlat($cur_lon, $cur_lat);";
            $poi_info["pois"][$rank]["center"] = $center;
            $poi_info["pois"][$rank]["open"] = "OpenLayers.HooksPopups.on_map_feature_select(geo_object$map_suffix, $pind);";
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

        $map_name = $map['name'];
        $map_name = str_replace("&", "&amp;", $map_name);
        $map_name = str_replace("<", "&lt;", $map_name);
        $map_name = str_replace(">", "&gt;", $map_name);

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
            $poi_title = str_replace("&", "&amp;", $poi_title);
            $poi_title = str_replace("<", "&lt;", $poi_title);
            $poi_title = str_replace(">", "&gt;", $poi_title);
            $poi_perex = $poi['perex'];
            $poi_perex = str_replace("&", "&amp;", $poi_perex);
            $poi_perex = str_replace("<", "&lt;", $poi_perex);
            $poi_perex = str_replace(">", "&gt;", $poi_perex);

            $html .= '<div id="poi_seq_' . $poiIdx . '">
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
     * Gives map ids for the multi-map front end presentation
     *
     * @param int $p_languageId
     * @param array $p_issues
     * @param array $p_sections
     * @param array $p_dates
     * @param array $p_topics
     *
     * @return string
     */
/*
    public static function GetMultiMapArticles($p_languageId, $p_issues, $p_sections, $p_dates, $p_topics)
    {
		global $g_ado_db;

        $is_correct = true;
        $article_cons = false;
        $map_ids = array();

        $sql_params = array();

        $sql_inner = "";
        $sql_where = "";

        $sql_inner .= "INNER JOIN Articles AS a ON m.fk_article_number = a.Number ";

        $query_cons = "";
        if (0 < count($p_issues)) {
            foreach ($p_issues as $val) {
                if (!is_numeric($val)) {$is_correct = false;}
            }
            $query_cons .= "NrIssue IN (" . implode(", ", $p_issues) . ") AND ";
            $article_cons = true;
        }
        if (0 < count($p_sections)) {
            foreach ($p_sections as $val) {
                if (!is_numeric($val)) {$is_correct = false;}
            }
            $query_cons .= "NrSection IN (" . implode(", ", $p_sections) . ") AND ";
            $article_cons = true;
        }
        if (2 == count($p_dates)) {
            //$query_cons .= "PublishDate >= ? AND PublishDate <= ? AND ";
            $date_start = str_replace("'", "\"", $p_dates[0]);
            $date_stop = str_replace("'", "\"", $p_dates[1]);

            $query_cons .= "PublishDate >= '$date_start' AND PublishDate <= '$date_stop' AND ";
            //$sql_params[] = $p_dates[0];
            //$sql_params[] = $p_dates[1];
            $article_cons = true;
        }

        $queryStr = "";
        $queryStr .= "SELECT m.id AS id FROM Maps AS m ";
        $queryStr .= "INNER JOIN Articles AS a ON m.fk_article_number = a.Number ";

        if (0 < count($p_topics))
        {
            $article_cons = true;
            $queryStr .= "INNER JOIN ArticleTopics AS at ON a.Number = at.NrArticle ";
            $sql_inner .= "INNER JOIN ArticleTopics AS at ON a.Number = at.NrArticle ";
        }

        $queryStr .= "WHERE $query_cons ";
        $sql_where .= "$query_cons ";

        if (0 < count($p_topics)) {
            foreach ($p_topics as $val) {
                if (!is_numeric($val)) {$is_correct = false;}
            }
            $queryStr .= "TopicId IN (" . implode(", ", $p_topics) . ") AND ";
            $sql_where .= "AND TopicId IN (" . implode(", ", $p_topics) . ") ";
        }

        $queryStr .= "IdLanguage = ? AND Published = 'Y'";
        $sql_params[] = $p_languageId;

        if (!$article_cons) {
            return array('error' => false, 'cons' => false, 'maps' => array());
        }

        try {
            echo "<br>\n$queryStr<br>\n";
            var_dump($sql_params);
            echo "<br>\n";
            $rows = $g_ado_db->GetAll($queryStr, $sql_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $map_ids[] = $row['id'];
                }
            }
        }
        catch (Exception $exc)
        {
            return array('error' => true, 'cons' => $article_cons, 'maps' => array());
        }

        return array('error' => false, 'cons' => true, 'maps' => $map_ids);
    }
*/

    /**
     * Gives the header part for the multi-map front end presentation
     *
     * @param int $p_languageId
     * @param array $p_issues
     * @param array $p_sections
     * @param array $p_dates
     * @param array $p_topics
     * @param array $p_areas
     * @param int $p_mapWidth
     * @param int $p_mapHeight
     *
     * @return string
     */
    public static function GetMultiMapTagHeader($p_languageId, $p_constraints, $p_offset, $p_limit, $p_mapWidth, $p_mapHeight)
    {
        global $Campsite;
        $tag_string = "";

        //$map_set = Geo_Map::GetMultiMapArticles($p_languageId, $p_issues, $p_sections, $p_dates, $p_topics);
        //$map_cons = array("issues" => $p_issues, "sections" => $p_sections, "dates" => $p_dates, "topics" => $p_topics, "areas" => $p_areas);
        //$points = Geo_Map::ReadMapPoints(0, $p_languageId, true, false, $map_set);
        //$points = Geo_Map::ReadMapPoints(0, $p_languageId, true, false, $p_constraints);
        $poi_count = 0;
        $points = Geo_MapLocation::GetListExt(array("asArray" => true, "mapId" => 0, "languageId" => $p_languageId, "preview" => true, "textOnly" => false, "mapCons" => $p_constraints), (array) null, $p_offset, $p_limit, $poi_count);



        $f_language_id = $p_languageId;

        $map_suffix = "_" . "multimap" . "_" . $f_language_id;

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

        $geo_map_usage_json = "";
        $geo_map_usage_json .= json_encode($geo_map_usage);

        $geo_map_info = Geo_Preferences::GetMapInfo($cnf_html_dir, $cnf_website_url, $geo_map_usage['prov']);
        $geo_map_incl = Geo_Preferences::PrepareMapIncludes($geo_map_info["incl_obj"]);
        $geo_map_json = "";
        $geo_map_json .= json_encode($geo_map_info["json_obj"]);

        $geo_icons_info = Geo_Preferences::GetIconsInfo($cnf_html_dir, $cnf_website_url);
        $geo_icons_json = "";
        $geo_icons_json .= json_encode($geo_icons_info["json_obj"]);
        
        $geo_popups_info = Geo_Preferences::GetPopupsInfo($cnf_html_dir, $cnf_website_url);
        $geo_popups_json = "";
        $geo_popups_json .= json_encode($geo_popups_info["json_obj"]);

        $geo_focus_info = Geo_Preferences::GetFocusInfo($cnf_html_dir, $cnf_website_url);

        {
            $geo_focus_info["json_obj"]["auto_focus"] = true;
        }
        $geo_focus_json = "";
        $geo_focus_json .= json_encode($geo_focus_info["json_obj"]);
        
        $preview = true;
        $poi_info = array('pois' => $points, 'map' => $geo_map_usage);
        
        //$poi_info_json = str_replace("'", "\\'", json_encode($poi_info));
        $poi_info_json = json_encode($poi_info);
        
        $geocodingdir = $Campsite['WEBSITE_URL'] . '/javascript/geocoding/';

        $include_files = Geo_Preferences::GetIncludeCSS($cnf_html_dir, $cnf_website_url);
        $include_files_css = $include_files["css_files"];
        $include_files_tags = "";
        foreach ($include_files_css as $css_file)
        {
            $include_files_tags .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$css_file\" />\n";
        }

        $tag_string .= $geo_map_incl;
        $tag_string .= "\n";

        $tag_string .= $include_files_tags;

        $tag_string .= '

	<script type="text/javascript" src="' . $Campsite["WEBSITE_URL"] . '/javascript/geocoding/map_popups.js"></script>
	<script type="text/javascript" src="' . $Campsite["WEBSITE_URL"] . '/javascript/geocoding/openlayers/OpenLayers.js"></script>
	<script type="text/javascript" src="' . $Campsite["WEBSITE_URL"] . '/javascript/geocoding/openlayers/OLlocals.js"></script>
	<script type="text/javascript" src="' . $Campsite["WEBSITE_URL"] . '/javascript/geocoding/map_preview.js"></script>

<script type="text/javascript">
    geo_object'. $map_suffix .' = new geo_locations();
var geo_on_load_proc_map' . $map_suffix . ' = function()
{

    var map_obj = document.getElementById ? document.getElementById("geo_map_mapcanvas' . $map_suffix . '") : null;
    if (map_obj)
    {
        map_obj.style.width = "' . $geo_map_usage["width"] . 'px";
        map_obj.style.height = "' . $geo_map_usage["height"] . 'px";
';

    $article_spec_arr = array("language_id" => $f_language_id, "article_number" => 0);
    $article_spec = json_encode($article_spec_arr);

    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_article_spec($article_spec);";
    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_auto_focus($geo_focus_json);";
    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_map_info($geo_map_json);";
    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_map_usage($geo_map_usage_json);";
    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_icons_info($geo_icons_json);";
    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_popups_info($geo_popups_json);";
    $tag_string .= "\n";

        $tag_string .= '

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

};

    $(document).ready(function()
    {
        setTimeout("geo_on_load_proc_map' . $map_suffix . '();", 0);
    });
</script>
';

        return $tag_string;

    } // fn GetMultiMapTagHeader

    /**
     * Gives the body map-placement part for the map front end presentation
     *
     * @param int $p_languageId
     *
     * @return string
     */
    public static function GetMultiMapTagBody($p_languageId)
    {
        global $Campsite;
        $tag_string = "";

        $f_language_id = $p_languageId;

        $map_suffix = "_" . "multimap" . "_" . $f_language_id;

        $tag_string .= "<div id=\"geo_map_mapcanvas$map_suffix\" class=\"geo_map_mapcanvas\"></div>\n";

        return $tag_string;
    } // fn GetMultiMapTagBody

    /**
     * Gives the body map-centering (js call) part for the map front end presentation
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     *
     * @return string
     */
    public static function GetMultiMapTagCenter($p_languageId)
    {
        global $Campsite;
        $tag_string = "";

        $f_language_id = $p_languageId;

        $map_suffix = "_" . "multimap" . "_" . $f_language_id;

        $tag_string .= "geo_object" . $map_suffix . ".map_showview();";

        return $tag_string;
    } // fn GetMultiMapTagCenter

    /**
     * Gives the body map-info and point-list part for the map front end presentation
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     *
     * @return array
     */
    public static function GetMultiMapTagListData($p_languageId, $p_constraints, $p_offset, $p_limit)
    {
        $f_language_id = (int) $p_languageId;
        $map_suffix = "_" . "multimap" . "_" . $f_language_id;
        $preview = true;
        $text_only = true;

        $geo_map_usage = Geo_Map::ReadMultiMapInfo();

        //$map_set = Geo_Map::GetMultiMapArticles($p_languageId, $p_issues, $p_sections, $p_dates, $p_topics);
        //$points = Geo_Map::ReadMapPoints(0, $p_languageId, true, true, $map_set);
        //$map_cons = array("issues" => $p_issues, "sections" => $p_sections, "dates" => $p_dates, "topics" => $p_topics, "areas" => $p_areas);
        //$points = Geo_Map::ReadMapPoints(0, $p_languageId, true, true, $p_constraints);
        $poi_count = 0;
        $points = Geo_MapLocation::GetListExt(array("asArray" => true, "mapId" => 0, "languageId" => $p_languageId, "preview" => true, "textOnly" => true, "mapCons" => $p_constraints), (array) null, $p_offset, $p_limit, $poi_count);


        $poi_info = array('pois' => $points, 'map' => $geo_map_usage);

        $pind = 0;
        foreach ($poi_info["pois"] as $rank => $poi) {
            $cur_lon = $poi["longitude"];
            $cur_lat = $poi["latitude"];
            $center = "geo_object$map_suffix.center_lonlat($cur_lon, $cur_lat);";
            $poi_info["pois"][$rank]["center"] = $center;
            $poi_info["pois"][$rank]["open"] = "OpenLayers.HooksPopups.on_map_feature_select(geo_object$map_suffix, $pind);";
            $pind += 1;
        }
        return (array) $poi_info;
    } // fn GetMultiMapTagListData

    /**
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @return string
     */
    public static function GetMultiMapTagList($p_languageId, $p_constraints)
    {

        $geo = self::GetMultiMapTagListData((int) $p_languageId, $p_constraints);
        $map = $geo['map'];
        $pois = $geo['pois'];

        $map_name = $map['name'];
        $map_name = str_replace("&", "&amp;", $map_name);
        $map_name = str_replace("<", "&lt;", $map_name);
        $map_name = str_replace(">", "&gt;", $map_name);

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
            $poi_title = str_replace("&", "&amp;", $poi_title);
            $poi_title = str_replace("<", "&lt;", $poi_title);
            $poi_title = str_replace(">", "&gt;", $poi_title);
            $poi_perex = $poi['perex'];
            $poi_perex = str_replace("&", "&amp;", $poi_perex);
            $poi_perex = str_replace("<", "&lt;", $poi_perex);
            $poi_perex = str_replace(">", "&gt;", $poi_perex);

            $html .= '<div id="poi_seq_' . $poiIdx . '">
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
        $tag_string = "";

        $map_suffix = "_search";

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

        $geo_map_incl = Geo_Preferences::PrepareMapIncludes($geo_map_info["incl_obj"]);
        $geo_map_json = "";
        $geo_map_json .= json_encode($geo_map_info["json_obj"]);

        $geo_icons_info = Geo_Preferences::GetSearchInfo($cnf_html_dir, $cnf_website_url);
        $geo_icons_json = "";
        $geo_icons_json .= json_encode($geo_icons_info["json_obj"]);

        $geocodingdir = $Campsite['WEBSITE_URL'] . '/javascript/geocoding/';


        $tag_string .= $geo_map_incl;
        $tag_string .= "\n";

        $tag_string .= '

	<script type="text/javascript" src="' . $Campsite["WEBSITE_URL"] . '/javascript/geocoding/map_popups.js"></script>
	<script type="text/javascript" src="' . $Campsite["WEBSITE_URL"] . '/javascript/geocoding/openlayers/OpenLayers.js"></script>
	<script type="text/javascript" src="' . $Campsite["WEBSITE_URL"] . '/javascript/geocoding/openlayers/OLlocals.js"></script>
	<script type="text/javascript" src="' . $Campsite["WEBSITE_URL"] . '/javascript/geocoding/map_search.js"></script>

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
            $bbox_divs_json = "";
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
        map_obj.style.width = "' . $geo_map_info["width"] . 'px";
        map_obj.style.height = "' . $geo_map_info["height"] . 'px";

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
        $tag_string = "";

        $map_suffix = "_search";

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
        $tag_string = "";

        $map_suffix = "_search";

        $tag_string .= "geo_object" . $map_suffix . ".map_showview();";

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
        $queryStr = "";
        $queryStr_1 = "";
        $queryStr_2 = "";
        $queryStr_end = "";

        $queryStr .= "SELECT DISTINCT m.fk_article_number AS Number FROM Maps AS m INNER JOIN MapLocations AS ml ON m.id = ml.fk_map_id INNER JOIN ";
        $queryStr .= "Locations AS l ON ml.fk_location_id = l.id WHERE ";

        $cons_res = Geo_MapLocation::GetGeoSearchSQLCons($p_coordinates, "rectangle", "l");
        if ($cons_res["error"]) {return "";}

        $queryStr .= $cons_res["cons"];

        $queryStr_end .= "AND m.fk_article_number != 0";
        $queryStr .= $queryStr_end;

        return $queryStr;

    } // fn GetGeoSearchSQLQuery

    public static function GetGeoSearchSQLCons($p_coordinates, $p_polygonType = "rectangle", $p_tableAlias = "l")
    {
        $queryCons = "";
        $paramError = false;

        if (!ctype_alnum($p_tableAlias)) {
            $paramError = true;
            return array("error" => true, "cons" => "");
        }

        $p_polygonType = strtolower("" . $p_polygonType);
        if (!array_key_exists($p_polygonType, array("rectangle" => 1, "clockwise" => 1, "counterclockwise" => 1))) {
            $paramError = true;
            return array("error" => true, "cons" => "");
        }

        if ("rectangle" == $p_polygonType)
        {
            $queryCons_1 = "";
            $queryCons_2 = "";

            $queryCons_1 .= "Intersects(GeomFromText('Polygon((%%x0%% %%y0%%,%%x0%% %%y1%%,%%x1%% %%y1%%,%%x1%% %%y0%%,%%x0%% %%y0%%))'),$p_tableAlias.poi_location) ";
            $queryCons_2 .= "(Intersects(GeomFromText('Polygon((%%x0%% %%y0%%,%%x0%% 180,%%x1%% 180,%%x1%% %%y0%%,%%x0%% %%y0%%))'),$p_tableAlias.poi_location) OR Intersects(GeomFromText('Polygon((%%x0%% -180,%%x0%% %%y1%%,%%x1%% %%y1%%,%%x1%% -180,%%x0%% -180))'),$p_tableAlias.poi_location)) ";
    
            $loc_left = $p_coordinates[0];
            $loc_right = $p_coordinates[1];

            $left_lon = "" . $loc_left["longitude"];
            $left_lat = "" . $loc_left["latitude"];
            $right_lon = "" . $loc_right["longitude"];
            $right_lat = "" . $loc_right["latitude"];
    
            if (!is_numeric($left_lon)) {$left_lon = "0";}
            if (!is_numeric($left_lat)) {$left_lat = "0";}
            if (!is_numeric($right_lon)) {$right_lon = "0";}
            if (!is_numeric($right_lat)) {$right_lat = "0";}
    
            $south_lat = $right_lat;
            $north_lat = $left_lat;
            if ($south_lat > $north_lat)
            {
                $south_lat = $left_lat;
                $north_lat = $right_lat;
            }
    
            $east_lon = $left_lon;
            $west_lon = $right_lon;
    
            if ($east_lon > $west_lon)
            {
                $queryCons .= $queryCons_2;
            }
            else
            {
                $queryCons .= $queryCons_1;
            }
    
            $queryCons = str_replace("%%y0%%", $east_lon, $queryCons);
            $queryCons = str_replace("%%y1%%", $west_lon, $queryCons);
            $queryCons = str_replace("%%x0%%", $south_lat, $queryCons);
            $queryCons = str_replace("%%x1%%", $north_lat, $queryCons);

        }

        if ((0 < count($p_polygonType)) && (("clockwise" == $p_polygonType) || ("counterclockwise" == $p_polygonType)))
        {
            $polygon_spec = "";

            $ind_start = 0;
            $ind_stop = count($p_coordinates) - 1;
            $ind_step = 1;
            if ("counterclockwise" == $p_polygonType) {
                $ind_start = count($p_coordinates) - 1;
                $ind_stop = 0;
                $ind_step = -1;
            }

            $first_lon = $p_polygonType[$ind_start]["longitude"];
            $first_lat = $p_polygonType[$ind_start]["latitude"];

            for ($ind = $ind_start; ; $ind += $ind_step) {

                $corner = $p_coordinates[$ind];
                $one_lon = $corner["longitude"];
                $one_lat = $corner["latitude"];
                if ((!is_numeric($one_lon)) || (!is_numeric($one_lat))) {
                    $paramError = true;
                    break;
                }

                $polygon_spec .= "$one_lon $one_lat,";

                if ($ind == $ind_stop) {break;}
            }
            $polygon_spec .= "$first_lon $first_lat";

            $queryCons .= "Intersects(GeomFromText('Polygon(($polygon_spec))'),$p_tableAlias.poi_location) ";

        }

        if ($paramError) {
            return array("error" => true, "cons" => "");
        }

        return array("error" => false, "cons" => $queryCons);
    } // fn GetGeoSearchSQLCons


} // class Geo_Map

/* testing:
    // going east to west over the 180/-180, and south to north
    $p_coordinates = array();
    $p_coordinates[] = array("longitude" => "150", "latitude" => "20");
    $p_coordinates[] = array("longitude" => "40", "latitude" => "60");

    // going directly west to east, and north to south
    $p_coordinates = array();
    $p_coordinates[] = array("longitude" => "-10", "latitude" => "60");
    $p_coordinates[] = array("longitude" => "40", "latitude" => "-20");

    $query = Geo_Map::GetGeoSearchSQLQuery($p_coordinates);
    echo $query;
*/
