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

	/**
	 * Finds POIs on given article and language
	 *
	 * @param string $p_articleNumber
	 * @param string $p_languageId
	 *
	 * @return array
	 */
	public static function ReadArticlePoints($p_articleNumber, $p_languageId)
	{
		global $g_ado_db;
		$sql_params = array($p_articleNumber, $p_languageId);

		$queryStr = "SELECT ";
		$queryStr .= "l.id AS l_id, AsText(l.poi_location) AS l_loc, l.poi_type AS l_type, l.poi_type_style AS l_style, l.poi_center AS l_cen, l.poi_radius AS l_rad, l.fk_user_id AS l_usr, l.last_modified AS l_mod, l.time_created AS l_ini, ";
		//$queryStr .= "c.id AS c_id, c.fk_location_id AS c_lid, c.publish_date AS c_pub, c.poi_display AS p_disp, c.poi_popup AS p_popup, c.poi_popup_size_width AS p_width, c.poi_popup_size_height AS p_height, c.poi_name AS p_name, c.poi_content_usage AS i_use, c.poi_content AS i_con, c.poi_perex AS i_perex, c.poi_link AS i_link, c.poi_text AS i_text, c.poi_image_src AS i_img, c.poi_video_id AS v_id, c.poi_video_type AS v_type, c.poi_video_height AS v_height, c.poi_video_width AS v_width, c.poi_audio_usage AS a_use, c.poi_audio_type AS a_type, c.poi_audio_site AS a_site, c.poi_audio_track AS a_track, c.poi_audio_auto AS a_auto, c.fk_user_id AS c_usr, c.last_modified AS c_mod, c.time_created AS c_ini ";
		$queryStr .= "c.id AS c_id, c.fk_location_id AS c_lid, c.publish_date AS c_pub, c.poi_display AS c_disp, c.poi_style AS c_style, c.poi_popup AS p_popup, ";
        $queryStr .= "c.poi_name AS p_name, c.poi_content_type AS i_type, c.poi_content AS i_con, c.poi_perex AS i_perex, ";
        $queryStr .= "c.poi_link AS i_link, c.poi_text AS i_text, c.poi_image_src AS i_img_src, c.poi_image_width AS i_img_width, c.poi_image_height AS i_img_height, ";
        $queryStr .= "c.poi_video_id AS v_id, c.poi_video_type AS v_type, c.poi_video_height AS v_height, c.poi_video_width AS v_width";
        $queryStr .= ", c.fk_user_id AS c_usr, c.last_modified AS c_mod, c.time_created AS c_ini";
		$queryStr .= ", c.rank AS c_rank ";
		//$queryStr .= "c.rank AS c_rank, c.id_hash AS c_hash ";

		$queryStr .= "FROM Locations AS l INNER JOIN LocationContents AS c ON l.id = c.fk_location_id ";
		$queryStr .= "WHERE c.fk_article_number = ? AND c.fk_language_id = ? ";

		$queryStr .= "ORDER BY c.rank, l_id";
		//$queryStr .= "ORDER BY l_id";

        #echo "$queryStr -- " . print_r($sql_params) . " -- ";
		$rows = $g_ado_db->GetAll($queryStr, $sql_params);

		$some_rows = false;

		$returnArray = array();
		if (is_array($rows)) {
            #print_r($rows);
			foreach ($rows as $row) {
                $tmp_loc = trim(strtolower($row['l_loc']));
                //echo "-- $tmp_loc --";
                $loc_matches = array();
                if (!preg_match('/^point\((?P<latitude>[\d.-]+)\s(?P<longitude>[\d.-]+)\)$/', $tmp_loc, $loc_matches)) {continue;}
                $tmp_latitude = $loc_matches['latitude'];
                $tmp_longitude = $loc_matches['longitude'];
                //echo print_r($loc_matches);

				$some_rows = true;
				$tmpPoint = array();
				$tmpPoint['loc_id'] = $row['l_id'];
				$tmpPoint['con_id'] = $row['c_id'];
				$tmpPoint['latitude'] = $tmp_latitude;
				$tmpPoint['longitude'] = $tmp_longitude;
				$tmpPoint['rank'] = $row['c_rank'];
				$tmpPoint['display'] = $row['c_disp'];
				$tmpPoint['style'] = $row['c_style'];
				$tmpPoint['title'] = $row['p_name'];
				$tmpPoint['perex'] = $row['i_perex'];
				$tmpPoint['content_type'] = $row['i_type'];
				$tmpPoint['content'] = $row['i_con'];
				$tmpPoint['link'] = $row['i_link'];
				$tmpPoint['text'] = $row['i_text'];
				$tmpPoint['image_src'] = $row['i_img_src'];
				$tmpPoint['image_width'] = $row['i_img_width'];
				$tmpPoint['image_height'] = $row['i_img_height'];
				$tmpPoint['video_id'] = $row['v_id'];
				$tmpPoint['video_type'] = $row['v_type'];
				$tmpPoint['video_width'] = $row['v_width'];
				$tmpPoint['video_height'] = $row['v_height'];
				array_push($returnArray, $tmpPoint);
			}
		}

        //print_r($returnArray);
		return $returnArray;

	} // fn ReadArticlePoints



	public static function ReadLanguages($p_article_number)
    {
		global $g_ado_db;

        $queryStr_langs = "SELECT IdLanguage AS lang FROM Articles WHERE Number = ?";

        // first, read ids of languages of the article
        $art_langs_arr = array();
        {
            $langs_params = array();
            $langs_params[] = $p_article_number;

            $ros = $g_ado_db->GetAll($queryStr_langs, $langs_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $art_langs_arr[] = $row['lang'];
                }
            }
        }

        return $art_langs_arr;
    }

	public static function UpdateMap($p_article_number, $p_language_id, $p_map)
    {
		global $g_ado_db;

        // map updates; no map insert/removal; map non/usage is set elsewhere
        $queryStr_map_up = "UPDATE Articles SET MapCenterLongitude = ?, MapCenterLatitude = ?, MapDisplayResolution = ?, MapProvider = ? WHERE Number = ?";

        // if a map setting change, set it for all languages
        if ($p_map)
        {
            try
            {
                $map_up_params = array();
                $map_up_params[] = $p_map["cen_lon"];
                $map_up_params[] = $p_map["cen_lat"];
                $map_up_params[] = $p_map["resolution"];
                $map_up_params[] = $p_map["provider"];
                $map_up_params[] = $p_article_number;

                $success = $g_ado_db->Execute($queryStr_map_up, $map_up_params);
            }
            catch (Exception $exc)
            {
                return false;
            }

        }

        return true;
    }

	public static function RemovePoints($p_article_number, $p_language_id, $p_removal)
    {
		global $g_ado_db;

        // removal: it removes this from all languages of the article
        // the POI itself is removed if not used at other articles
        $queryStr_con_re = "DELETE FROM LocationContents WHERE id = ?";
        $queryStr_loc_re = "DELETE FROM Locations WHERE id = ? AND NOT EXISTS (SELECT id FROM LocationContents WHERE fk_location_id = ?)";

        // if a POI removal, delete it from languages
        foreach ($p_removal as $poi_obj)
        {
            $poi = get_object_vars($poi_obj);

            try
            {
                $poi_re_params = array();
                $poi_re_params[] = $poi["content_id"];
                $poi_re_params[] = $p_article_number;
    
                // content removal from all article's languages
                $success = $g_ado_db->Execute($queryStr_con_re, $poi_re_params);
    
                $poi_re_params = array();
                $poi_re_params[] = $poi["location_id"];
                $poi_re_params[] = $poi["location_id"];
    
                // removal of the POI location
                $success = $g_ado_db->Execute($queryStr_loc_re, $poi_re_params);
            }
            catch (Exception $exc)
            {
                return false;
            }
        }
        return true;
    }

	public static function InsertPoints($p_article_number, $p_language_id, $p_insertion, &$indices)
    {
		global $g_ado_db;

        // insertion: just for newly added POIs, next changes are just updates/removals
		$queryStr_loc_in = "INSERT INTO Locations (poi_location, poi_type, poi_type_style, poi_center, poi_radius) VALUES (";
        $queryStr_loc_in .= "GeomFromText('POINT(? ?)'), 'point', 0, PointFromText('POINT(? ?)'), 0";
        $queryStr_loc_in .= ")";

        //$queryStr_con_in = "INSERT INTO LocationContents (fk_article_number, fk_language_id, fk_location_id, fk_event_id, publish_date,
        // poi_display, poi_style, poi_name, poi_content_usage, poi_content, poi_perex, poi_link, poi_text, poi_image_src,
        // poi_video_id, poi_video_type, poi_video_height, poi_video_width) VALUES (";

        $queryStr_con_in = "INSERT INTO LocationContents (fk_article_number, fk_language_id, fk_location_id";
        $queryStr_con_in .= ", poi_display, poi_style, poi_name, poi_perex";
        $queryStr_con_in .= ", poi_content_type, poi_content, ";
        $queryStr_con_in .= "poi_text, poi_link, poi_image_src, poi_image_width, poi_image_height";
        $queryStr_con_in .= ", poi_video_id, poi_video_type, poi_video_width, poi_video_height";
        $queryStr_con_in .= ") VALUES (";

        $quest_marks = array();
        for ($ind = 0; $ind < 18; $ind++) {$quest_marks[] = "?";}
        $queryStr_con_in .= implode(", ", $quest_marks);

        $queryStr_con_in .= ")";

        $languages = Geo_LocationContents::ReadLanguages($p_article_number);

        //$indices = array();

        foreach ($p_insertion as $poi_obj)
        {
            $poi = get_object_vars($poi_obj);
            //print_r($poi);

            // insert POI location, then rank and content for all languages, but the other languages have usage set to false
            //if ("new" == $poi_state)
            {
                //print_r($poi);
                $loc_in_params = array();
                $loc_in_params[] = $poi["latitude"];
                //$loc_in_params[] = $poi->{"latitude"};

                $loc_in_params[] = $poi["longitude"];
                $loc_in_params[] = $poi["latitude"];
                $loc_in_params[] = $poi["longitude"];

                // the POI itself insertion
                $success = $g_ado_db->Execute($queryStr_loc_in, $loc_in_params);
                // taking its ID for the next processing
                $loc_id = $g_ado_db->Insert_ID();

                $con_in_params = array();
                $con_in_params[] = 0 + $p_article_number;
                $con_in_params[] = 0 + $p_language_id;
                $con_in_params[] = 0 + $loc_id;

                $con_in_params[] = 0 + $poi["display"];
                $con_in_params[] = "" . $poi["style"];

                $con_in_params[] = "" . $poi["name"];
                $con_in_params[] = "" . $poi["perex"];
                $con_in_params[] = 0 + $poi["content_type"];
                $con_in_params[] = "" . $poi["content"];
                $con_in_params[] = "" . $poi["text"];
                $con_in_params[] = "" . $poi["link"];

                $con_in_params[] = "" . $poi["image_src"];
                $con_in_params[] = "" . $poi["image_width"];
                $con_in_params[] = "" . $poi["image_height"];

                $con_in_params[] = "" . $poi["video_id"];
                $con_in_params[] = "" . $poi["video_type"];
                $con_in_params[] = "" . $poi["video_width"];
                $con_in_params[] = "" . $poi["video_height"];

                // insert the POI content on the used language
                //echo "$queryStr_con_in;";
                //print_r($con_in_params);
                $success = $g_ado_db->Execute($queryStr_con_in, $con_in_params);

                $con_id = $g_ado_db->Insert_ID();

                $poi_index = $poi['index'];
                $indices[$poi_index] = array('loc' => $loc_id, 'con' => $con_id);

                // insert the POI content for the other article's languages
                foreach ($languages as $one_lang)
                {
                    if ($one_lang == $p_language_id) {continue;}

                    $con_in_params[1] = $one_lang;
                    $con_in_params[3] = false; // display;

                    $success = $g_ado_db->Execute($queryStr_con_in, $con_in_params);
                }

                // if a new POI, then that's all for it here
                continue;
            }

        }


        //return $indices;
        return true;
    }

	public static function UpdateLocations($p_article_number, $p_language_id, $p_locations)
    {
		global $g_ado_db;

		$queryStr_loc_up = "UPDATE Locations SET poi_location = GeomFromText('POINT(? ?)'), poi_center = GeomFromText('POINT(? ?)') ";
        $queryStr_loc_up .= "WHERE id = ?";


        // updating current POIs, inserting new POIs
        $poi_rank = 0;
        foreach ($p_locations as $poi_obj)
        {
            $poi = get_object_vars($poi_obj);

            //$poi_state = $poi["state"];
            //$poi_rank += 1;
            // for the current POIs below

            // if something (location/center) on the location table changed, set it
            //if ($poi['location_changed'])
            {
                $loc_up_params = array();
                $loc_up_params[] = $poi["latitude"];
                $loc_up_params[] = $poi["longitude"];
                $loc_up_params[] = $poi["latitude"];
                $loc_up_params[] = $poi["longitude"];

                $loc_up_params[] = $poi["id"];

                $success = $g_ado_db->Execute($queryStr_loc_up, $loc_up_params);
            }

        }

        ;
        return true;
    }

	public static function UpdateContents($p_article_number, $p_language_id, $p_contents)
    {
		global $g_ado_db;

        // updates: even for setting a non-usage of a POI at a article/language
        $queryStr_con_up = "UPDATE LocationContents SET poi_display = ?, poi_style = ?, poi_name = ?, poi_perex = ?, ";
        $queryStr_con_up .= "poi_content_type = ?, poi_content = ?, ";
        $queryStr_con_up .= "poi_text = ?, poi_link = ?, poi_image_src = ?, poi_image_width = ?, poi_image_height = ?, ";
        $queryStr_con_up .= "poi_video_id = ?, poi_video_type = ?, poi_video_width = ?, poi_video_height = ? ";
        $queryStr_con_up .= "WHERE id = ?";

        // updating current POIs, inserting new POIs
        //$poi_rank = 0;
        foreach ($p_contents as $poi_obj)
        {
            $poi = get_object_vars($poi_obj);
            //print_r($poi);

            //$poi_state = $poi["state"];
            //$poi_rank += 1;
            // for the current POIs below
            // if the POI content, icon have not changed, then that's all for it here

            //if (!$poi['content_changed'])
            //{
            //    continue;
            //}

            // preparing all the (rest) parameters
            $sql_params = array();
            $sql_params[] = 0 + $poi['display']; // poi display
            $sql_params[] = "" . $poi['style']; // poi style
            $sql_params[] = "" . $poi['name'];
            $sql_params[] = "" . $poi['perex'];
            $sql_params[] = 0 + $poi['content_type'];
            $sql_params[] = "" . $poi['content'];
            $sql_params[] = "" . $poi['text'];
            $sql_params[] = "" . $poi['link'];
            $sql_params[] = "" . $poi['image_src'];
            $sql_params[] = "" . $poi['image_width'];
            $sql_params[] = "" . $poi['image_height'];
            $sql_params[] = "" . $poi['video_id'];
            $sql_params[] = "" . $poi['video_type'];
            $sql_params[] = "" . $poi['video_width'];
            $sql_params[] = "" . $poi['video_height'];
            $sql_params[] = 0 + $poi['id'];

            //$queryStr_con_up = "UPDATE LocationContents SET poi_display = ? WHERE id = ?";
            //echo "$queryStr_con_up;";
            //print_r($sql_params);
            //$success = $g_ado_db->Execute($queryStr_con_up);
            $success = $g_ado_db->Execute($queryStr_con_up, $sql_params);
        }

        ;
        return true;
    }

	public static function UpdateOrder($p_article_number, $p_language_id, $p_reorder, $p_indices)
    {
		global $g_ado_db;

        //echo "$p_article_number, $p_language_id;";
        //print_r($p_reorder);
        //print_r($p_indices);

        // the rank is to be updated on all article's languages
        $queryStr_rnk_up = "UPDATE LocationContents SET rank = ? WHERE fk_article_number = ? AND fk_location_id = ?";

        $rank = 0;
        foreach ($p_reorder as $poi_obj)
        {
            $rank += 1;
            $db_id  = 0;

            $poi = get_object_vars($poi_obj);

            try
            {
                $state = $poi['state'];
                if ('new' == $state)
                {
                    $tmp_key = $poi['index'];
                    //if (!array_key_exists($tmp_key, $p_indices) {continue;}
                    $db_id = $p_indices[$tmp_key]['loc'];
                }
                else
                {
                    //if (!array_key_exists('location', $poi) {continue;}
                    $db_id = $poi['location'];
                }
    
                // if a POIs' reorder requested, set ranks for all article's languages
                //if ($p_to_reorder)
                {
                    $rnk_up_params = array();
                    $rnk_up_params[] = $rank;
                    $rnk_up_params[] = $p_article_number;
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


	/**
	 * Finds POIs on given article and language
	 *
	 * @param array $p_insert
	 * @param array $p_update
	 * @param array $p_remove
	 *
	 * @return array
	 */
/*
	public static function WriteArticlePoints($p_article_number, $p_language_id, $p_map, $p_remove, $p_insert, $p_update, $p_poi_reorder)
	{
		global $g_ado_db;

        // map updates; no map insert/removal; map non/usage is set elsewhere
        $queryStr_map_up = "UPDATE Articles SET MapCenterLongitude = ?, MapCenterLatitude = ?, MapDisplayResolution = ?, MapProvider = ? WHERE Number = ?";

        // removal: it removes this from all languages of the article
        // the POI itself is removed if not used at other articles
        $queryStr_con_re = "DELETE FROM LocationContents WHERE fk_location_id = ? AND fk_article_number = ?";
        $queryStr_loc_re = "DELETE FROM Locations WHERE id = ? AND NOT EXISTS (SELECT id FROM LocationContents WHERE fk_location_id = ?)";

        // when doing inserts/updates, then it goes through all the POIs, and either inserts or updates rank, and possibly updates the whole content

        // insertion: just for newly added POIs, next changes are just updates/removals
		$queryStr_loc_in = "INSERT INTO Locations (poi_location, poi_type, poi_type_style, poi_center, poi_radius) VALUES (";
        $queryStr_loc_in .= "GeomFromText('POINT(? ?)'), 'point', 0, PointFromText('POINT(? ?)'), 0";
        $queryStr_loc_in .= ")";

        $queryStr_con_in = "INSERT INTO LocationContents (fk_article_number, fk_language_id, fk_location_id, fk_event_id, publish_date, poi_display, poi_style, poi_name, poi_content_usage, poi_content, poi_perex, poi_link, poi_text, poi_image_src, poi_video_id, poi_video_type, poi_video_height, poi_video_width) VALUES (";
        $quest_marks = array();
        for ($ind = 0; $ind < 18; $ind++) {$quest_marks[] = "?";}
        $queryStr_con_in .= implode(", ", $quest_marks);
        $queryStr_con_in .= ")";

        // updates: even for setting a non-usage of a POI at a article/language
		$queryStr_loc_up = "UPDATE Locations SET poi_location = GeomFromText('POINT(? ?)'), poi_center = GeomFromText('POINT(? ?)') ";
        $queryStr_loc_up .= "WHERE id = ?";

        // the rank is to be updated on all article's languages
        $queryStr_con_up = "UPDATE LocationContents SET poi_display = ?, poi_style = ?, poi_name = ?, poi_content_usage = ?, poi_content = ?, poi_perex = ?, poi_link = ?, poi_text = ?, poi_image_src = ?, poi_video_id = ?, poi_video_type = ?, poi_video_height = ?, poi_video_width = ? ";
        $queryStr_con_up .= "WHERE id = ?";

        $queryStr_rnk_up = "UPDATE LocationContents SET rank = ? WHERE fk_article_number = ? AND fk_location_id = ?";

        $queryStr_langs = "SELECT IdLanguage AS lang FROM Articles WHERE Number = ?";

        // first, read ids of languages of the article
        $art_langs_arr = array();
        {
            $langs_params = array();
            $langs_params[] = $p_article_number;

            $ros = $g_ado_db->GetAll($queryStr_langs, $langs_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $art_langs_arr[] = $row['lang'];
                }
            }
        }

        //$art_langs_str = "(" . implode(", ", $art_langs_arr) . ")";

        // if a map setting change, set it for all languages
        if ($p_map)
        {
            $map_up_params = array();
            $map_up_params[] = $p_map["cen_lon"];
            $map_up_params[] = $p_map["cen_lat"];
            $map_up_params[] = $p_map["resolution"];
            $map_up_params[] = $p_map["provider"];
            $map_up_params[] = $p_article_number;

            $success = $g_ado_db->Execute($queryStr_map_up, $map_up_params);
        }

        // if a POI removal, delete it from languages
        foreach ($p_remove as $poi)
        {
            $poi_re_params = array();
            $poi_re_params[] = $poi["id"];
            $poi_re_params[] = $p_article_number;

            // content removal from all article's languages
            $success = $g_ado_db->Execute($queryStr_con_re, $poi_re_params);

            $poi_re_params = array();
            $poi_re_params[] = $poi["id"];
            $poi_re_params[] = $poi["id"];

            // removal of the POI location
            $success = $g_ado_db->Execute($queryStr_loc_re, $poi_re_params);
        }

        // updating current POIs, inserting new POIs
        $poi_rank = 0;
        foreach ($p_upsert as $poi)
        {
            $poi_state = $poi["state"];
            $poi_rank += 1;

            // insert POI location, then rank and content for all languages, but the other languages have usage set to false
            if ("new" == $poi_state)
            {
                $loc_in_params = array();
                $loc_in_params[] = $poi["latitude"];
                $loc_in_params[] = $poi["longitude"];
                $loc_in_params[] = $poi["latitude"];
                $loc_in_params[] = $poi["longitude"];

                // the POI itself insertion
                $success = $g_ado_db->Execute($queryStr_loc_in, $loc_in_params);
                // taking its ID for the next processing
                $poi_id = $g_ado_db->Insert_ID();

                $con_in_params = array();
                $con_in_params[] = $p_article_number;
                $con_in_params[] = $p_language_id;
                $con_in_params[] = $poi_id;
                $con_in_params[] = 0; // event id
                $con_in_params[] = null; // publish date
                $con_in_params[] = $poi["display"];
                $con_in_params[] = $poi["poi_style"];
                $con_in_params[] = $poi["name"];
                $con_in_params[] = $poi["content_usage"];
                $con_in_params[] = $poi["content"];
                $con_in_params[] = $poi["perex"];
                $con_in_params[] = $poi["link"];
                $con_in_params[] = $poi["text"];
                $con_in_params[] = $poi["image"];
                $con_in_params[] = $poi["video_id"];
                $con_in_params[] = $poi["video_type"];
                $con_in_params[] = $poi["video_width"];
                $con_in_params[] = $poi["video_height"];

                // insert the POI content on the used language
                $success = $g_ado_db->Execute($queryStr_con_in, $con_in_params);

                // insert the POI content for the other article's languages
                foreach ($art_langs_arr as $one_lang)
                {
                    $con_in_params[0] = $one_lang;
                    $con_in_params[5] = false; // display;

                    $success = $g_ado_db->Execute($queryStr_con_in, $con_in_params);
                }

                // if a new POI, then that's all for it here
                continue;
            }

            // for the current POIs below

            // if something (location/center) on the location table changed, set it
            if ($poi['location_changed'])
            {
                $loc_up_params = array();
                $loc_up_params[] = $poi["latitude"];
                $loc_up_params[] = $poi["longitude"];
                $loc_up_params[] = $poi["latitude"];
                $loc_up_params[] = $poi["longitude"];

                $loc_up_params[] = $poi["id"];

                $success = $g_ado_db->Execute($queryStr_loc_up, $loc_up_params);
            }

            // if a POIs' reorder requested, set ranks for all article's languages
            if ($p_to_reorder)
            {
                $rnk_up_params = array();
                $rnk_up_params[] = $poi["rank"];
                $rnk_up_params[] = $p_article_number;
                $rnk_up_params[] = $poi["id"];

                $success = $g_ado_db->Execute($queryStr_rnk_up, $rnk_up_params);
            }

            // if the POI content, icon have not changed, then that's all for it here
            if (!$poi['content_changed'])
            {
                continue;
            }

            // preparing all the (rest) parameters
            $sql_params = array();
            $sql_params[] = $poi['poi_display']; // poi display
            $sql_params[] = $poi['poi_style']; // poi style
            $sql_params[] = $poi['name'];
            $sql_params[] = $poi['content_usage'];
            $sql_params[] = $poi['content'];
            $sql_params[] = $poi['perex'];
            $sql_params[] = $poi['link'];
            $sql_params[] = $poi['text'];
            $sql_params[] = $poi['image_src'];
            $sql_params[] = $poi['video_id'];
            $sql_params[] = $poi['video_type'];
            $sql_params[] = $poi['video_height'];
            $sql_params[] = $poi['video_width'];

            $sql_params[] = $poi['id'];
        }

		//return ReadArticlePoints($p_articleNumber, $p_languageId);

	} // fn WriteArticlePoints
*/

} // class Geo_LocationContents

?>
