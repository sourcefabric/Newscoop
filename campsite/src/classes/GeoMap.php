<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/SQLSelectClause.php');
//require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');
//require_once($GLOBALS['g_campsiteDir'].'/classes/CampCacheList.php');
//require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampTemplate.php');

require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/GeoLocation.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/GeoLocationContent.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/GeoMultimedia.php');

/**
 * @package Campsite
 */
class Geo_Map extends DatabaseObject {
    /**
     * The column names used for the primary key.
     * @var array
     */
    var $m_keyColumnNames = array('id');

    var $m_dbTableName = 'Maps';

    var $m_columnNames = array(
        // int - Map ID
        'id',

        // int - link to the respective article
        'fk_article_number',

        // int - rank of the map in the article
        'MapRank',

        // int - map enabled
        'MapUsage',

        // real - initial map center
        'MapCenterLongitude',
        'MapCenterLatitude',

        // int - initial map resolution
        'MapDisplayResolution',

        // string - the map to be used for readers
        'MapProvider',

        // int - the map div size
        'MapWidth',
        'MapHeight',

        // string - the map name
        'MapName',

        // int - management related things
        'IdUser',
        // timestamp
        'time_updated');

	/**
	 * The article attachment table links together articles with Attachments.
	 *
	 * @param int $p_id
	 * @return Map
	 */
	public function Geo_Map($p_id = null)
	{
		if (is_numeric($p_id)) {
			$this->m_data['id'] = $p_id;
		}
	} // constructor

	/**
	 * @return int
	 */
	public function GetMapId()
	{
		return $this->m_data['id'];
	} // fn getMapId

	/**
	 * @return int
	 */
	public static function GetArticleMapId($p_articleObj)
	{
		global $g_ado_db;

        $article_number = $p_articleObj->getArticleNumber();

        $queryStr = "SELECT id FROM Maps WHERE fk_article_number = ? AND MapUsage = 1 ORDER BY MapRank, id LIMIT 1";

        $map_id = null;

        try
        {
            $sql_params = array();

            $sql_params[] = $article_number;

            $rows = $g_ado_db->GetAll($queryStr, $sql_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $map_id = $row['id'];
                }
            }
        }
        catch (Exception $exc)
        {
            return null;
        }

		return $map_id;
	} // fn getMapIdByArticle

	/**
	 * @return array
	 */
	public static function GetMapIdsByArticle($p_articleObj)
	{
		global $g_ado_db;

        $article_number = $p_articleObj->getArticleNumber();

        $queryStr = "SELECT id, MapUsage AS usage FROM Maps WHERE fk_article_number = ? ORDER BY MapRank, id";

        $map_ids = array();

        try
        {
            $sql_params = array();

            $sql_params[] = $article_number;

            $rows = $g_ado_db->GetAll($queryStr, $sql_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $map_ids[] = array("id" => $row['id'], "usage" => $row['usage']);
                }
            }
        }
        catch (Exception $exc)
        {
            return array();
        }

		return $map_ids;
	} // fn getMapIdByArticle

    public static function GetLocationsByArticle($p_articleObj)
    {
		global $g_ado_db;

        $queryStr = "SELECT lc.poi_name AS name FROM Maps AS m INNER JOIN MapLocations AS ml ON ml.fk_map_id = m.id ";
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
                    $poi_names[] = $row['name'];
                }
            }
        }
        catch (Exception $exc)
        {
            return array();
        }

        return $poi_names;
    }


	/**
	 * @return int
	 */
	public function getArticleNumber()
	{
		return $this->m_data['fk_article_number'];
	} // fn getArticleNumber


	/**
	 * Foo.
	 *
	 * @param int $p_bar0
	 * @param int $p_bar1
	 *
	 * @return void
	 */
	public static function Foo($p_bar0, $p_bar1)
	{
        return;

		global $g_ado_db;

		$queryStr = '';

		$g_ado_db->Execute($queryStr);
	} // fn Foo


	/**
	 * This is called when a language is deleted.
	 * It will remove the links on location contents, and the possible free contents.
	 *
	 * @param int $p_articleNumber
	 * @param int $p_languageId
	 * @return void
	 */
	public static function OnLanguageDelete($p_articleNumber, $p_languageId)
	{
        return;

		global $g_ado_db;

		//$queryStr = "DELETE FROM ArticleAttachments WHERE fk_attachment_id=$p_attachmentId";

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

/*
	public static function Delete($p_id)
	{
		global $g_ado_db;


    }
*/

	/**
	 * Remove attachment pointers for the given article.
	 * @param int $p_articleNumber
	 * @return void
	 */
	public static function OnArticleDelete($p_articleNumber)
	{
		global $g_ado_db;

		$queryStr = "UPDATE Map SET fk_article_number = 0 WHERE fk_article_number = ?";

        $sql_params = array();
        $sql_params[] = $p_articleNumber;

		$g_ado_db->Execute($queryStr, $sql_params);
	} // fn OnArticleDelete


	/**
	 * Copy all the pointers for the given article.
	 * @param int $p_srcArticleNumber
	 * @param int $p_destArticleNumber
	 * @return void
	 */
	public static function OnArticleCopy($p_srcArticleNumber, $p_destArticleNumber)
	{
		global $g_ado_db;
/*
		$queryStr = 'SELECT fk_attachment_id FROM ArticleAttachments WHERE fk_article_number='.$p_srcArticleNumber;
		$rows = $g_ado_db->GetAll($queryStr);
		foreach ($rows as $row) {
			$queryStr = 'INSERT IGNORE INTO ArticleAttachments(fk_article_number, fk_attachment_id)'
						." VALUES($p_destArticleNumber, ".$row['fk_attachment_id'].")";
			$g_ado_db->Execute($queryStr);
		}
*/
	} // fn OnArticleCopy
	/**

	 * Copy all the pointers for the given article.
	 * @param int $p_srcArticleNumber
	 * @param int $p_destArticleNumber
	 * @return void
	 */
	public static function OnLanguageCopy($p_articleNumber, $p_srcLanguageId, $p_destLanguageId)
	{
		global $g_ado_db;
/*
		$queryStr = 'SELECT fk_attachment_id FROM ArticleAttachments WHERE fk_article_number='.$p_srcArticleNumber;
		$rows = $g_ado_db->GetAll($queryStr);
		foreach ($rows as $row) {
			$queryStr = 'INSERT IGNORE INTO ArticleAttachments(fk_article_number, fk_attachment_id)'
						." VALUES($p_destArticleNumber, ".$row['fk_attachment_id'].")";
			$g_ado_db->Execute($queryStr);
		}
*/
	} // fn OnArticleCopy


    // ajax processing handlers

	public static function LoadMapData($p_mapId, $p_languageId, $p_articleNumber)
	{
		//global $g_ado_db;

        $found_list = Geo_Map::ReadMapPoints($p_mapId, $p_languageId);

        $geo_map_usage = Geo_Map::ReadMapInfo("map", $p_mapId);

        $res_array = array("status" => "200", "pois" => $found_list, "map" => $geo_map_usage);

        return $res_array;
    }


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


	public static function ReadMapPoints($p_mapId, $p_languageId)
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


	public static function ReadLanguages($p_articleNumber)
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

        $p_map = get_object_vars($p_map);

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
            $one_rem = get_object_vars($one_rem);

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
                    $media_ids[] = $row['con'];
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


        $languages = Geo_Map::ReadLanguages($p_articleNumber);


        foreach ($p_insertion as $poi_obj)
        {
            $poi = get_object_vars($poi_obj);
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
                $con_id = Geo_LocationContent::InsertContent($poi);

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

} // class ArticleAttachment

/* testing:
    $art = new Article(1, 35);
    $locs = Geo_map::GetLocationsByArticle($art);
    print_r($locs);

    $art = new Article(2, 35);
    $map_id = Geo_map::GetArticleMapId($art);
    echo "map_id: $map_id";
*/

?>
