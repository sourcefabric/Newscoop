<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/SQLSelectClause.php');
//require_once($GLOBALS['g_campsiteDir'].'/classes/CampCacheList.php');
//require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampTemplate.php');

/**
 * @package Campsite
 */
class Geo_LocationContent extends DatabaseObject {
	var $m_keyColumnNames = array('id');
	var $m_dbTableName = 'LocationContents';
	//var $m_columnNames = array('id', 'city_id', 'city_type', 'population', 'position', 'latitude', 'longitude', 'elevation', 'country_code', 'time_zone', 'modified');

	/**
	 * The geo location contents class is for load/store of POI data.
	 */
	public function Geo_LocationContent()
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
/*
	public static function ReadArticlePoints($p_articleNumber, $p_languageId)
	{
		global $g_ado_db;
		$sql_params = array($p_articleNumber, $p_languageId);

	} // fn ReadArticlePoints
*/

	public static function InsertContent($poi)
    {
		global $g_ado_db;
        global $g_user;

        $queryStr_con_in = "INSERT INTO LocationContents (";
        $queryStr_con_in .= "poi_name, poi_link, poi_perex, ";
        $queryStr_con_in .= "poi_content_type, poi_content, poi_text, IdUser";
        $queryStr_con_in .= ") VALUES (";

        $quest_marks = array();
        for ($ind = 0; $ind < 6; $ind++) {$quest_marks[] = "?";}
        $queryStr_con_in .= implode(", ", $quest_marks);

        $queryStr_con_in .= ", %%user_id%%)";

        $queryStr_con_sl = "SELECT id FROM LocationContents WHERE poi_name = ? AND poi_link = ? ";
        $queryStr_con_sl .= "AND poi_perex = ? AND poi_content_type = ? AND poi_content = ? AND poi_text = ? ";
        $queryStr_con_sl .= "ORDER BY id LIMIT 1";

        // ad B 3)
        $con_in_params = array();

        $con_in_params[] = "" . $poi["name"];
        $con_in_params[] = "" . $poi["link"];

        $con_in_params[] = "" . $poi["perex"];
        $con_in_params[] = 0 + $poi["content_type"];
        $con_in_params[] = "" . $poi["content"];
        $con_in_params[] = "" . $poi["text"];

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
            $queryStr_con_in = str_replace("%%user_id%%", $g_user->getUserId(), $queryStr_con_in);

            $success = $g_ado_db->Execute($queryStr_con_in, $con_in_params);

            // ad B 4)
            $con_id = $g_ado_db->Insert_ID();
        }

        return $con_id;
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
        $queryStr_con_id = "SELECT fk_content_id AS con FROM MapLocationLanguages WHERE id = ?";
        // ad B 2)
        // call InsertContent();
        // ad B 4)
        $queryStr_map_up = "UPDATE MapLocationLanguages SET fk_content_id = ? WHERE id = ?";
        // ad B 6)
        $queryStr_con_rm = "DELETE FROM LocationContents WHERE id = ? AND NOT EXISTS (SELECT id FROM MapLocationLanguages WHERE fk_content_id = ?)";

        {
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
            $con_new_id = Geo_LocationContent::InsertContent($poi);

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

    }

} // class Geo_LocationContents

?>
