<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/SQLSelectClause.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/GeoLocation.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/GeoMapLocation.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/GeoMapLocationContent.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/GeoMultimedia.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/IGeoMap.php');

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
     * @param Article
     * @return int
     */
    public static function GetArticleMapId($p_articleObj)
    {
		global $g_ado_db;

        $article_number = $p_articleObj->getArticleNumber();
        $map_id = self::GetMapIdByArticle($article_number);
        return $map_id;
    }

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
    }

	/**
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
	}


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
    }

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
    }

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

/*
		global $g_ado_db;

		$queryStr = "UPDATE Maps SET fk_article_number = 0 WHERE fk_article_number = ?";

        $sql_params = array();
        $sql_params[] = $p_articleNumber;

		$g_ado_db->Execute($queryStr, $sql_params);
*/
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
    }


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
        return array(
            'pois' => Geo_Map::ReadMapPoints((int) $p_mapId, (int) $p_languageId, $p_preview, $p_textOnly),
            'map' => Geo_Map::ReadMapInfo('map', (int) $p_mapId),
        );
    }

/*
	public static function LoadMapDataPreview($p_mapId, $p_languageId, $p_articleNumber)
	{
        $preview = true;

        return array(
            'pois' => Geo_Map::ReadMapPoints((int) $p_mapId, (int) $p_languageId, $preview),
            'map' => Geo_Map::ReadMapInfo('map', (int) $p_mapId),
        );
    }

	public static function LoadMapDataPreviewText($p_mapId, $p_languageId, $p_articleNumber)
	{
        $preview = true;
        $text_only = true;

        return array(
            'pois' => Geo_Map::ReadMapPoints((int) $p_mapId, (int) $p_languageId, $preview, $text_only),
            'map' => Geo_Map::ReadMapInfo('map', (int) $p_mapId),
        );
    }
*/

	public static function StoreMapData($p_mapId, $p_languageId, $p_articleNumber, $p_map = "", $p_remove = "", $p_insert = "", $p_locations = "", $p_contents = "", $p_order = "")
	{
        //$security_problem = '{"status":"403","description":"Invalid security token!"}';
        //$unknown_request = '{"status":"404","description":"Unknown request!"}';
        //$data_wrong = '{"status":"404","description":"Wrong data."}';
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
            //exit();
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
            //exit();
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
            //exit();
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
            //exit();
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
            //exit();
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
            //exit();
        }
    
        $geo_map_usage = Geo_Map::ReadMapInfo("map", $p_mapId);
    
    
        $found_list = Geo_Map::ReadMapPoints($p_mapId, $p_languageId);
    
        $res_array = array("status" => "200", "pois" => $found_list, "map" => $geo_map_usage);

        return $res_array;
    }

    // the functions for map editing are below

	//public static function ReadMapInfo($p_articleNumber, $p_languageId)
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
    }


	public static function ReadMapPoints($p_mapId, $p_languageId, $p_preview = false, $p_textOnly = false)
	{
        if (0 == $p_mapId) {return array();}

		global $g_ado_db;

		$sql_params = array($p_mapId, $p_languageId);

        $list_fill = "%%id_list%%";

		$queryStr = "SELECT ml.id AS ml_id, mll.id as mll_id, ml.fk_location_id AS loc_id, mll.fk_content_id AS con_id, ";
        $queryStr .= "ml.poi_style AS poi_style, ml.rank AS rank, mll.poi_display AS poi_display, ";

        $queryStr .= "AsText(l.poi_location) AS loc, l.poi_type AS poi_type, l.poi_type_style AS poi_type_style, ";

        $queryStr .= "c.poi_name AS poi_name, c.poi_link AS poi_link, c.poi_perex AS poi_perex, ";
        $queryStr .= "c.poi_content_type AS poi_content_type, c.poi_content AS poi_content, c.poi_text AS poi_text ";

        $queryStr .= "FROM MapLocations AS ml INNER JOIN MapLocationLanguages AS mll ON ml.id = mll.fk_maplocation_id ";
        $queryStr .= "INNER JOIN Locations AS l ON l.id = ml.fk_location_id ";
        $queryStr .= "INNER JOIN LocationContents AS c ON c.id = mll.fk_content_id ";

        $queryStr .= "WHERE ml.fk_map_id = ? AND mll.fk_language_id = ? ";

        if ($p_preview)
        {
            $queryStr .= "AND mll.poi_display = 1 ";
        }

        $queryStr .= "ORDER BY ml.rank, ml.id, mll.id";

        $queryStr_mm = "SELECT m.id AS m_id, mlm.id AS mlm_id, ml.id AS ml_id, ";
        $queryStr_mm .= "m.media_type AS media_type, m.media_spec AS media_spec, ";
        $queryStr_mm .= "m.media_src AS media_src, m.media_height AS media_height, m.media_width AS media_width ";
        $queryStr_mm .= "FROM Multimedia AS m INNER JOIN MapLocationMultimedia AS mlm ON m.id = mlm.fk_multimedia_id ";
        $queryStr_mm .= "INNER JOIN MapLocations AS ml ON ml.id = mlm.fk_maplocation_id ";
        $queryStr_mm .= "WHERE ml.id IN ($list_fill)";

		$dataArray = array();
        $maploc_ids = array();

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
                //$tmpPoint['maploc_id'] = $row['ml_id'];
                //$tmpPoint['maploclan_id'] = $row['mll_id'];
                //$tmpPoint['loc_id'] = $row['loc_id'];
                //$tmpPoint['con_id'] = $row['con_id'];
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

                $maploc_ids[] = $row['ml_id'];
            }
        }

        if (0 == count($maploc_ids)) {return $dataArray;}
        if ($p_textOnly) {return $dataArray;}

        $loc_ids_list = implode(", ", $maploc_ids);

        $queryStr_mm = str_replace($list_fill, $loc_ids_list, $queryStr_mm);

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

        foreach ($dataArray AS $index => $poi)
        {
            $ml_id = $poi["loc_id"];
            if (array_key_exists($ml_id, $imagesArray))
            {
                $dataArray[$index]["image_mm"] = $imagesArray[$ml_id]["mlm_id"];
                $dataArray[$index]["image_src"] = $imagesArray[$ml_id]["src"];
                $dataArray[$index]["image_width"] = $imagesArray[$ml_id]["width"];
                $dataArray[$index]["image_height"] = $imagesArray[$ml_id]["height"];
            }
            if (array_key_exists($ml_id, $videosArray))
            {
                $dataArray[$index]["video_mm"] = $videosArray[$ml_id]["mlm_id"];
                $dataArray[$index]["video_id"] = $videosArray[$ml_id]["src"];
                $dataArray[$index]["video_type"] = $videosArray[$ml_id]["spec"];
                $dataArray[$index]["video_width"] = $videosArray[$ml_id]["width"];
                $dataArray[$index]["video_height"] = $videosArray[$ml_id]["height"];
            }
        }

		return $dataArray;

	} // fn ReadMapPoints

	public static function ReadLanguagesByMap($p_mapId)
    {
		global $g_ado_db;

        $queryStr_langs = "SELECT mll.fk_language_id AS lang FROM MapLocationLanguages AS mll ";
        $queryStr_langs .= "INNER JOIN MapLocations AS ml ON mll.fk_maplocation_id = ml.id ";
        $queryStr_langs .= "INNER JOIN Maps AS m ON ml.fk_map_id = m.id ";
        $queryStr_langs .= "WHERE m.id = ?";

        // first, read ids of languages of the article
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
    } // ReadLanguagesByMap

	public static function ReadLanguagesByArticle($p_articleNumber)
    {
		global $g_ado_db;

        $queryStr_langs = "SELECT IdLanguage AS lang FROM Articles WHERE Number = ?";

        // first, read ids of languages of the article
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
    }

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
    }

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
    }

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

    }


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
                //$reuse_id = 0;

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
        //return true;
    }



    // presentation functions
    public static function GetMapTagHeader($p_articleNumber, $p_languageId, $p_mapWidth = 0, $p_mapHeight = 0)
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
        //print_r($geo_map_usage);

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
        
        $map_id = Geo_Map::GetMapIdByArticle($f_article_number);
        //$poi_info = Geo_Map::LoadMapData($map_id, $f_language_id, $f_article_number);
        //$poi_info = Geo_Map::LoadMapDataPreview($map_id, $f_language_id, $f_article_number);
        $preview = true;
        $poi_info = Geo_Map::LoadMapData($map_id, $f_language_id, $f_article_number, $preview);
        
        $poi_info_json = json_encode($poi_info);
        
        $geocodingdir = $Campsite['WEBSITE_URL'] . '/javascript/geocoding/';


        $tag_string .= $geo_map_incl;
        $tag_string .= "\n";

        $tag_string .= '

	<script type="text/javascript" src="' . $Campsite["WEBSITE_URL"] . '/javascript/geocoding/openlayers/OpenLayers.js"></script>
	<script type="text/javascript" src="' . $Campsite["WEBSITE_URL"] . '/javascript/geocoding/map_preview.js"></script>

<script type="text/javascript">
    //alert("0123");
    geo_object'. $map_suffix .' = new geo_locations();
    //alert("1234");
    //geo_obj = geo_object' . $map_suffix . ';

var useSystemParameters = function()
{
';

    $article_spec_arr = array("language_id" => $f_language_id, "article_number" => $f_article_number);
    $article_spec = json_encode($article_spec_arr);

    $tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_article_spec($article_spec);";
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
};
var on_load_proc = function()
{

    //alert(123);
    var map_obj = document.getElementById ? document.getElementById("geo_map_mapcanvas' . $map_suffix . '") : null;
    if (map_obj)
    {
        //alert(456);
        //map_obj.style.width = "800px";
        //map_obj.style.height = "200px";
        map_obj.style.width = "' . $geo_map_usage["width"] . 'px";
        map_obj.style.height = "' . $geo_map_usage["height"] . 'px";
        //alert("001");
        geo_main_selecting_locations(geo_object' . $map_suffix . ', "' . $geocodingdir. '", "geo_map_mapcanvas' . $map_suffix. '", "map_sidedescs", "", "", true);
        //alert("002");
        geo_object' . $map_suffix . '.got_load_data(\'' . $poi_info_json . '\');
        //alert("003");
    }
};
    $(document).ready(function()
    {
        on_load_proc();
    });
</script>
';

        return $tag_string;

    }

    public static function GetMapTagBody($p_articleNumber, $p_languageId)
    {
        global $Campsite;
        $tag_string = "";

        $f_article_number = $p_articleNumber;
        $f_language_id = $p_languageId;

        $map_suffix = "_" . $f_article_number . "_" . $f_language_id;

#        $tag_string .='
#<div class="map_mapmenu">
#<a href="#" onClick="geo_object' . $map_suffix . '.map_showview(); return false;">';
#putGS("show initial map view");
#        $tag_string .='</a>
#</div><!-- end of map_mapmenu -->
#';

        $tag_string .= "<div id=\"geo_map_mapcanvas$map_suffix\"></div>\n";

        return $tag_string;
    }

    public static function GetMapTagCenter($p_articleNumber, $p_languageId)
    {
        global $Campsite;
        $tag_string = "";

        $f_article_number = $p_articleNumber;
        $f_language_id = $p_languageId;

        $map_suffix = "_" . $f_article_number . "_" . $f_language_id;

        $tag_string .= "geo_object" . $map_suffix . ".map_showview();";

        return $tag_string;
    }

    public static function GetMapTagList($p_articleNumber, $p_languageId)
    {
        global $Campsite;

        $f_article_number = $p_articleNumber;
        $f_language_id = $p_languageId;

        $map_suffix = "_" . $f_article_number . "_" . $f_language_id;

        $map_id = Geo_Map::GetMapIdByArticle($f_article_number);
        //$poi_info = Geo_Map::LoadMapData($map_id, $f_language_id, $f_article_number);
        //$poi_info = Geo_Map::LoadMapDataPreview($map_id, $f_language_id, $f_article_number);
        //$poi_info = Geo_Map::LoadMapDataPreviewText($map_id, $f_language_id, $f_article_number);
        $preview = true;
        $text_only = true;
        $poi_info = Geo_Map::LoadMapData($map_id, $f_language_id, $f_article_number, $preview, $text_only);

        //print_r($poi_info);

        //$geo_map_usage = Geo_Map::ReadMapInfo("article", $f_article_number);

        $pind = 0;
        foreach ($poi_info["pois"] as $rank => $poi)
        {
            $cur_lon = $poi["longitude"];
            $cur_lat = $poi["latitude"];
            $center = "geo_object$map_suffix.center_lonlat($cur_lon, $cur_lat);";

            $poi_info["pois"][$rank]["center"] = $center;

            $poi_info["pois"][$rank]["open"] = "geo_hook_on_map_feature_select(geo_object$map_suffix, $pind);";

            $pind += 1;
        }

        return $poi_info;
    }

    // search functions
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

        //$geo_map_usage_json = "";
        //$geo_map_usage_json .= json_encode($geo_map_usage);

/*
        
        $geo_popups_info = Geo_Preferences::GetPopupsInfo($cnf_html_dir, $cnf_website_url);
        $geo_popups_json = "";
        $geo_popups_json .= json_encode($geo_popups_info["json_obj"]);
*/

/*
        
        $map_id = Geo_Map::GetMapIdByArticle($f_article_number);
        //$poi_info = Geo_Map::LoadMapData($map_id, $f_language_id, $f_article_number);
        //$poi_info = Geo_Map::LoadMapDataPreview($map_id, $f_language_id, $f_article_number);
        $preview = true;
        $poi_info = Geo_Map::LoadMapData($map_id, $f_language_id, $f_article_number, $preview);
        
        $poi_info_json = json_encode($poi_info);
*/
        
        $geocodingdir = $Campsite['WEBSITE_URL'] . '/javascript/geocoding/';


        $tag_string .= $geo_map_incl;
        $tag_string .= "\n";

        $tag_string .= '

	<script type="text/javascript" src="' . $Campsite["WEBSITE_URL"] . '/javascript/geocoding/openlayers/OpenLayers.js"></script>
	<script type="text/javascript" src="' . $Campsite["WEBSITE_URL"] . '/javascript/geocoding/map_search.js"></script>

<script type="text/javascript">
    //alert("0123");
    geo_object'. $map_suffix .' = new geo_locations();
    //alert("1234");
    //geo_obj = geo_object' . $map_suffix . ';

var useSystemParameters = function()
{
';

    //$article_spec_arr = array("language_id" => $f_language_id, "article_number" => $f_article_number);
    //$article_spec = json_encode($article_spec_arr);

    $tag_string .= "\n";
    //$tag_string .= "geo_object$map_suffix.set_article_spec($article_spec);";
    //$tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_map_info($geo_map_json);";
    $tag_string .= "\n";
    //$tag_string .= "geo_object$map_suffix.set_map_usage($geo_map_usage_json);";
    //$tag_string .= "\n";
    $tag_string .= "geo_object$map_suffix.set_icons_info($geo_icons_json);";
    $tag_string .= "\n";
    //$tag_string .= "geo_object$map_suffix.set_popups_info($geo_popups_json);";
    //$tag_string .= "\n";


        //geo_object' . $map_suffix . '.got_load_data(\'' . $poi_info_json . '\');
        //alert("003");


//    $bbox_divs = array("tl_lon" => 'top_left_longitude', "tl_lat" => 'top_left_latitude', "br_lon" => 'bottom_right_longitude', "br_lat" => 'bottom_right_latitude')

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

    //alert(123);
    var map_obj = document.getElementById ? document.getElementById("geo_map_mapcanvas' . $map_suffix . '") : null;
    if (map_obj)
    {
        //alert(456);
        //map_obj.style.width = "800px";
        //map_obj.style.height = "200px";
        map_obj.style.width = "' . $geo_map_info["width"] . 'px";
        map_obj.style.height = "' . $geo_map_info["height"] . 'px";
        //alert("001");
        geo_main_selecting_locations(geo_object' . $map_suffix . ', "' . $geocodingdir. '", "geo_map_mapcanvas' . $map_suffix. '", "map_sidedescs", "", "", true);
        //alert("002");
    }
};
    $(document).ready(function()
    {
        on_load_proc();
    });
</script>
';

        return $tag_string;

    }

    public static function GetMapSearchBody()
    {
        global $Campsite;
        $tag_string = "";

        $map_suffix = "_search";

        $tag_string .= "<div id=\"geo_map_mapcanvas$map_suffix\"></div>\n";

        return $tag_string;
    }

    public static function GetMapSearchCenter()
    {
        global $Campsite;
        $tag_string = "";

        $map_suffix = "_search";

        $tag_string .= "geo_object" . $map_suffix . ".map_showview();";

        return $tag_string;
    }

    public static function GetGeoSearchSQLQuery($p_coordinates)
    {
        $queryStr = "";
        $queryStr_1 = "";
        $queryStr_2 = "";
        $queryStr_end = "";
        //$use_single = true;

        $queryStr .= "SELECT DISTINCT m.fk_article_number AS Number FROM Maps AS m INNER JOIN MapLocations AS ml ON m.id = ml.fk_map_id INNER JOIN ";
        $queryStr .= "Locations AS l ON ml.fk_location_id = l.id WHERE ";

        $queryStr_1 .= "MBRIntersects(GeomFromText('Polygon((%%x0%% %%y0%%,%%x0%% %%y1%%,%%x1%% %%y1%%,%%x1%% %%y0%%,%%x0%% %%y0%%))'),l.poi_location) ";
        $queryStr_2 .= "MBRIntersects(GeomFromText('MultiPolygon(((%%x0%% %%y0%%,%%x0%% 180,%%x1%% 180,%%x1%% %%y0%%,%%x0%% %%y0%%)),((%%x0%% -180,%%x0%% %%y1%%,%%x1%% %%y1%%,%%x1%% -180,%%x0%% -180)))'),l.poi_location) ";

        $queryStr_end .= "AND m.fk_article_number != 0";

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
            //$use_single = false;
            $queryStr .= $queryStr_2;
        }
        else
        {
            $queryStr .= $queryStr_1;
        }
        $queryStr .= $queryStr_end;

        $queryStr = str_replace("%%y0%%", $east_lon, $queryStr);
        $queryStr = str_replace("%%y1%%", $west_lon, $queryStr);
        $queryStr = str_replace("%%x0%%", $south_lat, $queryStr);
        $queryStr = str_replace("%%x1%%", $north_lat, $queryStr);

        return $queryStr;
    }



} // class GeoMap

/* testing:
    $art = new Article(1, 35);
    $locs = Geo_map::GetLocationsByArticle($art);
    print_r($locs);

    $art = new Article(2, 35);
    $map_id = Geo_map::GetArticleMapId($art);
    echo "map_id: $map_id";

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
