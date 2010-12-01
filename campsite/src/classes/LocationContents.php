<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/SQLSelectClause.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/CampCacheList.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampTemplate.php');

/**
 * @package Campsite
 */
class Geo_LocationContents extends DatabaseObject {
	var $m_keyColumnNames = array('id');
	var $m_dbTableName = 'LocationContents';
	//var $m_columnNames = array('id', 'city_id', 'city_type', 'population', 'position', 'latitude', 'longitude', 'elevation', 'country_code', 'time_zone', 'modified');

	/**
	 * The geo location contents class is for load/store of POI data.
	 */
	public function Geo_LocationContents()
	{
	} // constructor


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


	/**
	 * Finds POIs on given article and language
	 *
	 * @param string $p_articleNumber
	 * @param string $p_languageId
	 *
	 * @return array
	 */
/*
	public static function ReadArticlePoints($p_articleNumber, $p_languageId)
	{
		global $g_ado_db;
		$sql_params = array($p_articleNumber, $p_languageId);

	} // fn ReadArticlePoints
*/

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

        $p_map = get_object_vars($p_map);

        // creating a new map, if the map does not exist yet
        $queryStr_map_new = "INSERT INTO Maps (MapCenterLongitude, MapCenterLatitude, MapDisplayResolution, MapProvider, MapWidth, MapHeight, MapName, MapRank, fk_article_number) ";
        $queryStr_map_new .= "VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)";

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

	public static function InsertMultimedia($ml_id, $poi)
    {
		global $g_ado_db;

        $queryStr_mm = "INSERT INTO Multimedia (";
        $queryStr_mm .= "media_type, media_spec, media_src, media_width, media_height";
        $queryStr_mm .= ") VALUES (";

        $quest_marks = array();
        for ($ind = 0; $ind < 5; $ind++) {$quest_marks[] = "?";}
        $queryStr_mm .= implode(", ", $quest_marks);

        $queryStr_mm .= ")";

        $queryStr_loc_mm = "INSERT INTO MapLocationMultimedia (";
        $queryStr_loc_mm .= "fk_maplocation_id, fk_multimedia_id";
        $queryStr_loc_mm .= ") VALUES (";
        $queryStr_loc_mm .= "?, ?";
        $queryStr_loc_mm .= ")";

        if ("" != $poi["image_src"])
        {
            $mm_params = array();
            $mm_params[] = "image";
            $mm_params[] = "";
            $mm_params[] = "" . $poi["image_src"];
            $mm_params[] = 0 + $poi["image_width"];
            $mm_params[] = 0 + $poi["image_height"];

            $success = $g_ado_db->Execute($queryStr_mm, $mm_params);

            $mm_id = $g_ado_db->Insert_ID();

            $loc_mm_params = array();
            $loc_mm_params[] = $ml_id;
            $loc_mm_params[] = $mm_id;

            $success = $g_ado_db->Execute($queryStr_loc_mm, $loc_mm_params);
        }

        if ("" != $poi["video_id"])
        {
            $mm_params = array();
            $mm_params[] = "video";
            $mm_params[] = "" . $poi["video_type"];
            $mm_params[] = "" . $poi["video_id"];
            $mm_params[] = 0 + $poi["video_width"];
            $mm_params[] = 0 + $poi["video_height"];

            $success = $g_ado_db->Execute($queryStr_mm, $mm_params);

            $mm_id = $g_ado_db->Insert_ID();

            $loc_mm_params = array();
            $loc_mm_params[] = $ml_id;
            $loc_mm_params[] = $mm_id;

            $success = $g_ado_db->Execute($queryStr_loc_mm, $loc_mm_params);
        }
    }


	public static function InsertContent($poi)
    {
		global $g_ado_db;

        $queryStr_con_in = "INSERT INTO LocationContents (";
        $queryStr_con_in .= "poi_name, poi_link, poi_perex, ";
        $queryStr_con_in .= "poi_content_type, poi_content, poi_text";
        $queryStr_con_in .= ") VALUES (";

        $quest_marks = array();
        for ($ind = 0; $ind < 6; $ind++) {$quest_marks[] = "?";}
        $queryStr_con_in .= implode(", ", $quest_marks);

        $queryStr_con_in .= ")";

        // ad B 3)
        $con_in_params = array();

        $con_in_params[] = "" . $poi["name"];
        $con_in_params[] = "" . $poi["link"];

        $con_in_params[] = "" . $poi["perex"];
        $con_in_params[] = 0 + $poi["content_type"];
        $con_in_params[] = "" . $poi["content"];
        $con_in_params[] = "" . $poi["text"];
        // insert the POI content on the used language
        $success = $g_ado_db->Execute($queryStr_con_in, $con_in_params);

        // ad B 4)
        $con_id = $g_ado_db->Insert_ID();

        return $con_id;
    }

	public static function InsertPoints($p_mapId, $p_languageId, $p_articleNumber, $p_insertion, &$p_indices)
    {
		global $g_ado_db;

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
		$queryStr_loc_in = "INSERT INTO Locations (poi_location, poi_type, poi_type_style, poi_center, poi_radius) VALUES (";
        $queryStr_loc_in .= "GeomFromText('POINT(? ?)'), 'point', 0, PointFromText('POINT(? ?)'), 0";
        $queryStr_loc_in .= ")";
        // ad B 3)
        // ad B 5)
        $queryStr_maploc = "INSERT INTO MapLocations (fk_map_id, fk_location_id, poi_style, rank) ";
        $queryStr_maploc .= "VALUES (?, ?, ?, 0)";
        // ad B 7)
        $queryStr_maploclan = "INSERT INTO MapLocationLanguages (fk_maplocation_id, fk_language_id, fk_content_id, poi_display) ";
        $queryStr_maploclan .= "VALUES (?, ?, ?, ?)";


        $languages = Geo_LocationContents::ReadLanguages($p_articleNumber);


        foreach ($p_insertion as $poi_obj)
        {
            $poi = get_object_vars($poi_obj);
            {
                // ad B 1)
                $loc_in_params = array();
                $loc_in_params[] = $poi["latitude"];

                $loc_in_params[] = $poi["longitude"];
                $loc_in_params[] = $poi["latitude"];
                $loc_in_params[] = $poi["longitude"];

                // the POI itself insertion
                $success = $g_ado_db->Execute($queryStr_loc_in, $loc_in_params);
                // ad B 2)
                // taking its ID for the next processing
                $loc_id = $g_ado_db->Insert_ID();

                // ad B 3/4)
                $con_id = Geo_LocationContents::InsertContent($poi);

                // ad B 5)
                $maploc_params = array();
                $maploc_params[] = $p_mapId;
                $maploc_params[] = $loc_id;
                $maploc_params[] = "" . $poi["style"];
                // the map-point link insertion
                $success = $g_ado_db->Execute($queryStr_maploc, $maploc_params);

                // ad B 6)
                $maploc_id = $g_ado_db->Insert_ID();

                Geo_LocationContents::InsertMultimedia($maploc_id, $poi);

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

	public static function UpdateLocations($p_mapId, $p_locations)
    {
		global $g_ado_db;
/*
    A)
        1) given article_number, language_id, map_id, list of map_loc_id / new locations

    B)
        cycle:
            1) read location_id (as old_loc_id) of the map_loc_id
            2) insert new location with new positions
            3) get the inserted id into new_loc_id
            4) update maplocations into the new_loc_id for the map_loc_id
            6) delete location of old_loc_id if none maplocation with a link into the old_loc_id

*/

        // ad B 1)
        $queryStr_loc_id = "SELECT fk_location_id AS loc FROM MapLocations WHERE id = ?";
        // ad B 2)
		$queryStr_loc_in = "INSERT INTO Locations (poi_location, poi_type, poi_type_style, poi_center, poi_radius) VALUES (";
        $queryStr_loc_in .= "GeomFromText('POINT(? ?)'), 'point', 0, PointFromText('POINT(? ?)'), 0";
        $queryStr_loc_in .= ")";
        // ad B 4)
        $queryStr_map_up = "UPDATE MapLocations SET fk_location_id = ? WHERE id = ?";
        // ad B 6)
        $queryStr_loc_rm = "DELETE FROM Locations WHERE id = ? AND NOT EXISTS (SELECT id FROM MapLocations WHERE fk_location_id = ?)";

        // updating current POIs, inserting new POIs
        foreach ($p_locations as $poi_obj)
        {
            $poi = get_object_vars($poi_obj);

            // ad B 1)
            $loc_old_id = null;
            try
            {
                $maploc_sel_params = array();
                $maploc_sel_params[] = $poi["id"];

                $rows = $g_ado_db->GetAll($queryStr_loc_id, $maploc_sel_params);
                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        $loc_old_id = $row['loc'];
                    }
                }
            }
            catch (Exception $exc)
            {
                return false;
            }

            if (null === $loc_old_id) {continue;}

            // ad B 2)
            {
                $loc_in_params = array();
                $loc_in_params[] = $poi["latitude"];
                $loc_in_params[] = $poi["longitude"];
                $loc_in_params[] = $poi["latitude"];
                $loc_in_params[] = $poi["longitude"];

                $success = $g_ado_db->Execute($queryStr_loc_in, $loc_in_params);
            }

            // ad B 3)
            // taking its ID for the next processing
            $loc_new_id = $g_ado_db->Insert_ID();

            // ad B 4)
            {
                $map_up_params = array();
                $map_up_params[] = $loc_new_id;
                $map_up_params[] = $poi["id"];

                $success = $g_ado_db->Execute($queryStr_map_up, $map_up_params);
            }

            // ad B 6)
            try
            {
                $loc_rm_params = array();
                $loc_rm_params[] = $loc_old_id;
                $loc_rm_params[] = $loc_old_id;

                $success = $g_ado_db->Execute($queryStr_loc_rm, $loc_rm_params);
            }
            catch (Exception $exc)
            {
                return false;
            }

        }

        ;
        return true;
    }

    public static function UpdateIcon($poi)
    {
		global $g_ado_db;

        $queryStr = "UPDATE MapLocations SET poi_style = ? WHERE id = ?";

        $sql_params = array();
        $sql_params[] = $poi["style"];
        $sql_params[] = $poi["location_id"];

        $success = $g_ado_db->Execute($queryStr, $sql_params);
    }

    public static function UpdateState($poi)
    {
		global $g_ado_db;

        $queryStr = "UPDATE MapLocationLanguages SET poi_display = ? WHERE id = ?";

        $sql_params = array();
        $sql_params[] = $poi["display"];
        $sql_params[] = $poi["id"];

        $success = $g_ado_db->Execute($queryStr, $sql_params);
    }

    public static function UpdateMedia($poi, $mm_type)
    {
		global $g_ado_db;

/*
    A)
        1) given article_number, language_id, map_id, list of map_loc_lan_id / new data

    B)
        //cycle:
            1) read multimedia_id (as old_med_id) of the map_loc_med_id
            2) insert new multimedia with new data
            3) get the inserted id into new_med_id
            4) update maplocationmultimedia into the new_med_id for the map_loc_med_id
            6) delete multimedia of old_med_id if none maplocationmultimedia with a link into the old_med_id

*/

        // ad B 1)
        $queryStr_med_id = "SELECT fk_multimedia_id AS med FROM MapLocationMultimedia WHERE id = ?";
        // ad B 2)

		$queryStr_med_in = "INSERT INTO Multimedia (media_type, media_spec, media_src, media_height, media_width) VALUES (";
        $queryStr_med_in .= "?, ?, ?, ?, ?";
        $queryStr_med_in .= ")";

        // ad B 4)
        $queryStr_map_up = "UPDATE MapLocationMultimedia SET fk_multimedia_id = ? WHERE id = ?";
        $queryStr_map_in = "INSERT INTO MapLocationMultimedia (fk_maplocation_id, fk_multimedia_id) VALUES (?, ?)";
        // ad B 6)
        $queryStr_med_rm = "DELETE FROM Multimedia WHERE id = ? AND NOT EXISTS (SELECT id FROM MapLocationMultimedia WHERE fk_multimedia_id = ?)";

        // ad B 1)

        $mm_id = null;
        $mm_spec = "";
        $mm_src = "";
        $mm_width = "";
        $mm_height = "";
        if ("image" == $mm_type)
        {
            $mm_id = $poi["image_mm"];
            $mm_spec = "";
            $mm_src = $poi["image_source"];
            $mm_width = $poi["image_width"];
            $mm_height = $poi["image_height"];
        }
        if ("video" == $mm_type)
        {
            $mm_id = $poi["video_mm"];
            $mm_spec = $poi["video_type"];
            $mm_src = $poi["video_id"];
            $mm_width = $poi["video_width"];
            $mm_height = $poi["video_height"];
        }
        if (null === $mm_id) {return;}

        $med_old_id = null;
        if ($mm_id)
        {
            try
            {
                $mapmed_sel_params = array();

                $mapmed_sel_params[] = $mm_id;
    
                $rows = $g_ado_db->GetAll($queryStr_med_id, $mapmed_sel_params);
                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        $med_old_id = $row['med'];
                    }
                }
            }
            catch (Exception $exc)
            {
                return false;
            }
    
            if (null === $med_old_id) {return;}
        }

        // ad B 2)
        $med_ins_params = array();
        $med_ins_params[] = "" . $mm_type;
        $med_ins_params[] = "" . $mm_spec;
        $med_ins_params[] = "" . $mm_src;
        $med_ins_params[] = 0 + $mm_width;
        $med_ins_params[] = 0 + $mm_height;

        $success = $g_ado_db->Execute($queryStr_med_in, $med_ins_params);

        // ad B 3)
        $med_new_id = $g_ado_db->Insert_ID();

        if (null === $med_old_id)
        {
            $map_in_params = array();
            $map_in_params[] = $poi["location_id"];
            $map_in_params[] = $med_new_id;

            $success = $g_ado_db->Execute($queryStr_map_in, $map_in_params);

            return;
        }

        // ad B 4)
        {
            $map_up_params = array();
            $map_up_params[] = $med_new_id;
            $map_up_params[] = $mm_id;

            $success = $g_ado_db->Execute($queryStr_map_up, $map_up_params);
        }

        // ad B 6)
        try
        {
            $med_rm_params = array();
            $med_rm_params[] = $med_old_id;
            $med_rm_params[] = $med_old_id;

            $success = $g_ado_db->Execute($queryStr_med_rm, $med_rm_params);
        }
        catch (Exception $exc)
        {
            return false;
        }


    }



	public static function UpdateContents($p_mapId, $p_contents)
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
        $queryStr_con_id = "SELECT fk_content_id AS con FROM MapLocationLanguages WHERE id = ?";
        // ad B 2)
        // call InsertContent();
        // ad B 4)
        $queryStr_map_up = "UPDATE MapLocationLanguages SET fk_content_id = ? WHERE id = ?";
        // ad B 6)
        $queryStr_con_rm = "DELETE FROM LocationContents WHERE id = ? AND NOT EXISTS (SELECT id FROM MapLocationLanguages WHERE fk_content_id = ?)";


        foreach ($p_contents as $poi_obj)
        {
            $poi = get_object_vars($poi_obj);

            if ($poi["icon_changed"])
            {
                Geo_LocationContents::UpdateIcon($poi);
            }
            if ($poi["state_changed"])
            {
                Geo_LocationContents::UpdateState($poi);
            }
            if ($poi["image_changed"])
            {
                Geo_LocationContents::UpdateMedia($poi, "image");
            }
            if ($poi["video_changed"])
            {
                Geo_LocationContents::UpdateMedia($poi, "video");
            }

            if (!$poi["text_changed"]) {continue;}

            // ad B 1)
            $con_old_id = null;
            try
            {
                $mapcon_sel_params = array();

                $mapcon_sel_params[] = $poi["id"];

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
            $con_new_id = Geo_LocationContents::InsertContent($poi);

            // ad B 4)
            {
                $map_up_params = array();
                $map_up_params[] = $con_new_id;

                $map_up_params[] = $poi["id"];

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

        ;
        return true;
    }

	public static function UpdateOrder($p_mapId, $p_reorder, $p_indices)
    {
		global $g_ado_db;

/*
    A)
        1) given article_number, language_id, map_id, list of content_ids with new contents

    B)
        cycle:
            point order is shared between langages
                1 a) read location_id of the content_id, if an old point
                1 b) or get location_id from the new indices for new points
                2) update rank on map_id/location_id

*/

        // ad B 2)
        $queryStr_rnk_up = "UPDATE MapLocations SET rank = ? WHERE id = ?";

        $rank = 0;
        foreach ($p_reorder as $poi_obj)
        {
            $rank += 1;
            $db_id = 0;

            $poi = get_object_vars($poi_obj);

            try
            {
                $state = $poi['state'];
                if ('new' == $state)
                {
                    // ad B 1 b)
                    $tmp_key = $poi['index'];
                    $db_id = $p_indices[$tmp_key]['maploc'];
                }
                else
                {
                    //ad B 1 a)
                    $db_id = 0 + $poi['location'];

                }

                // ad B 2)
                {
                    $rnk_up_params = array();
                    $rnk_up_params[] = $rank;

                    $rnk_up_params[] = $db_id;


                    $success = $g_ado_db->Execute($queryStr_rnk_up, $rnk_up_params);
                }
            }
            catch (Exception $exc)
            {
                return false;
            }

        }

        return true;
    }

} // class Geo_LocationContents

?>
