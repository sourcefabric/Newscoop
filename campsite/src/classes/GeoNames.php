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
class Geo_Names extends DatabaseObject {
	var $m_keyColumnNames = array('id');
	var $m_dbTableName = 'CityLocations';
	var $m_columnNames = array('id', 'city_id', 'city_type', 'population', 'position', 'latitude', 'longitude', 'elevation', 'country_code', 'time_zone', 'modified');

	/**
	 * The geo names class is for search on data per request.
	 */
	public function Geo_Names()
	{
	} // constructor


	/**
	 * Finds cities on given name and country
	 *
	 * @param string $p_cityName
	 * @param string $p_countryCode
	 *
	 * @return array
	 */
	public static function FindCitiesByName($p_cityName, $p_countryCode = "")
	{
		global $g_ado_db;
		$sql_params = null;
		$sql_params2 = null;
		$sql_params3 = null;
		$sql_params4 = null;

		$cityName_changed = implode("%", explode(" ", $p_cityName));

		$is_exact = true;
		if (strchr($p_cityName, "%")) {$is_exact = false;}

		$queryStr = 'SELECT DISTINCT cn.id as name_id, cn.city_name as name, cn.name_type as name_type, cl.city_id, cl.latitude AS lat, cl.longitude AS lon, cl.population AS pop , cl.country_code AS cc FROM CityLocations AS cl ';
		$queryStr .= 'INNER JOIN CityNames AS cn ON cl.city_id = cn.city_id WHERE ';
		if (0 < strlen($p_countryCode)) {
				$queryStr .= 'cl.country_code = ? AND ';
				$sql_params = array($p_countryCode, $p_cityName);
				$sql_params2 = array($p_countryCode, $cityName_changed);
				$sql_params3 = array($p_countryCode, $cityName_changed . "%");
				$sql_params4 = array($p_countryCode, "%" . $cityName_changed . "%");
		}
		else {
				$sql_params = array($p_cityName);
				$sql_params2 = array($cityName_changed);
				$sql_params3 = array($cityName_changed . "%");
				$sql_params4 = array("%" . $cityName_changed . "%");
		}
		$queryStr .= 'cn.city_name LIKE ? ';
		$queryStr .= 'GROUP BY cl.city_id ORDER BY cl.population DESC, cn.id ASC LIMIT 100';

		$rows = $g_ado_db->GetAll($queryStr, $sql_params);

		$some_rows = false;

		$returnArray = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$some_rows = true;
				$tmpCity = array();
				$tmpCity['name'] = $row['name'];
				$tmpCity['country'] = $row['cc'];
				$tmpCity['population'] = $row['pop'];
				$tmpCity['latitude'] = $row['lat'];
				$tmpCity['longitude'] = $row['lon'];
				array_push($returnArray, $tmpCity);
			}
		}

		if ($some_rows) {
				return $returnArray;
		}

		$rows = $g_ado_db->GetAll($queryStr, $sql_params2);

		$returnArray = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$some_rows = true;
				$tmpCity = array();
				$tmpCity['name'] = $row['name'];
				$tmpCity['country'] = $row['cc'];
				$tmpCity['population'] = $row['pop'];
				$tmpCity['latitude'] = $row['lat'];
				$tmpCity['longitude'] = $row['lon'];
				array_push($returnArray, $tmpCity);
			}
		}

		if ($some_rows) {
				return $returnArray;
		}

		$rows = $g_ado_db->GetAll($queryStr, $sql_params3);

		$returnArray = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$some_rows = true;
				$tmpCity = array();
				$tmpCity['name'] = $row['name'];
				$tmpCity['country'] = $row['cc'];
				$tmpCity['population'] = $row['pop'];
				$tmpCity['latitude'] = $row['lat'];
				$tmpCity['longitude'] = $row['lon'];
				array_push($returnArray, $tmpCity);
			}
		}

		if ($some_rows) {
				return $returnArray;
		}

		$rows = $g_ado_db->GetAll($queryStr, $sql_params4);

		$returnArray = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$some_rows = true;
				$tmpCity = array();
				$tmpCity['name'] = $row['name'];
				$tmpCity['country'] = $row['cc'];
				$tmpCity['population'] = $row['pop'];
				$tmpCity['latitude'] = $row['lat'];
				$tmpCity['longitude'] = $row['lon'];
				array_push($returnArray, $tmpCity);
			}
		}

		return $returnArray;

	} // fn FindCitiesByName



	/**
	 * Finds cities on given position
	 *
	 * @param string $p_longitude
	 * @param string $p_latitude
	 *
	 * @return array
	 */
	public static function FindCitiesByPosition($p_longitude, $p_latitude)
	{
		global $g_ado_db;
		$sql_params = null;
		$sql_params2 = null;
		$sql_params3 = null;
		$sql_params4 = null;

        $longitude = 0 + $p_longitude;
        $latitude = 0 + $p_latitude;

        $lon0l = $longitude - 0.08;
        $lon0g = $longitude + 0.08;
        $lat0l = $latitude - 0.04;
        $lat0g = $latitude + 0.04;

        $lon1l = $longitude - 0.15;
        $lon1g = $longitude + 0.15;
        $lat1l = $latitude - 0.075;
        $lat1g = $latitude + 0.075;

        $lon2l = $longitude - 0.30;
        $lon2g = $longitude + 0.30;
        $lat2l = $latitude - 0.15;
        $lat2g = $latitude + 0.15;

        $lon3l = $longitude - 1.0;
        $lon3g = $longitude + 1.0;
        $lat3l = $latitude - 0.5;
        $lat3g = $latitude + 0.5;

        $lon4l = $longitude - 2.0;
        $lon4g = $longitude + 2.0;
        $lat4l = $latitude - 1.0;
        $lat4g = $latitude + 1.0;

        $lonlat0 = "$lat0l $lon0l,$lat0g $lon0l,$lat0g $lon0g,$lat0l $lon0g,$lat0l $lon0l";
        $lonlat1 = "$lat1l $lon1l,$lat1g $lon1l,$lat1g $lon1g,$lat1l $lon1g,$lat1l $lon1l";
        $lonlat2 = "$lat2l $lon2l,$lat2g $lon2l,$lat2g $lon2g,$lat2l $lon2g,$lat2l $lon2l";
        $lonlat3 = "$lat3l $lon3l,$lat3g $lon3l,$lat3g $lon3g,$lat3l $lon3g,$lat3l $lon3l";
        $lonlat4 = "$lat4l $lon4l,$lat4g $lon4l,$lat4g $lon4g,$lat4l $lon4g,$lat4l $lon4l";

		$queryStr = 'SELECT DISTINCT cn.id as name_id, cn.city_name as name, cn.name_type as name_type, cl.city_id, cl.latitude AS lat, cl.longitude AS lon, cl.population AS pop , cl.country_code AS cc FROM CityLocations AS cl ';
		$queryStr .= 'INNER JOIN CityNames AS cn ON cl.city_id = cn.city_id WHERE ';

		$queryStr .= "Contains(PolygonFromText('POLYGON((";

		$queryStr_end = "))'), cl.position) ";
		$queryStr_end .= 'AND cn.name_type = "main" ORDER BY cl.population DESC, cn.id ASC LIMIT 100';

		$queryStr0 = $queryStr . $lonlat0 . $queryStr_end;
		$queryStr1 = $queryStr . $lonlat1 . $queryStr_end;
		$queryStr2 = $queryStr . $lonlat2 . $queryStr_end;
		$queryStr3 = $queryStr . $lonlat3 . $queryStr_end;
		$queryStr4 = $queryStr . $lonlat4 . $queryStr_end;

        $min_count = 20;
        $used_ids = array();

		$rows = $g_ado_db->GetAll($queryStr0);

		$some_rows = false;

		$returnArray = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$some_rows = true;
                $name_id = $row['name_id'];
                $used_ids[$name_id] = true;
				$tmpCity = array();
				$tmpCity['name'] = $row['name'];
				$tmpCity['country'] = $row['cc'];
				$tmpCity['population'] = $row['pop'];
				$tmpCity['latitude'] = $row['lat'];
				$tmpCity['longitude'] = $row['lon'];
				array_push($returnArray, $tmpCity);
			}
		}

		if ($min_count <= count($returnArray)) {
				return $returnArray;
		}

		$rows = $g_ado_db->GetAll($queryStr1);

		if (is_array($rows)) {
			foreach ($rows as $row) {
                $name_id = $row['name_id'];
                if (array_key_exists($name_id, $used_ids)) {continue;}
                $used_ids[$name_id] = true;

				$some_rows = true;
				$tmpCity = array();
				$tmpCity['name'] = $row['name'];
				$tmpCity['country'] = $row['cc'];
				$tmpCity['population'] = $row['pop'];
				$tmpCity['latitude'] = $row['lat'];
				$tmpCity['longitude'] = $row['lon'];
				array_push($returnArray, $tmpCity);
			}
		}

		if ($min_count <= count($returnArray)) {
				return $returnArray;
		}

		$rows = $g_ado_db->GetAll($queryStr2);

		if (is_array($rows)) {
			foreach ($rows as $row) {
                $name_id = $row['name_id'];
                if (array_key_exists($name_id, $used_ids)) {continue;}
                $used_ids[$name_id] = true;

				$some_rows = true;
				$tmpCity = array();
				$tmpCity['name'] = $row['name'];
				$tmpCity['country'] = $row['cc'];
				$tmpCity['population'] = $row['pop'];
				$tmpCity['latitude'] = $row['lat'];
				$tmpCity['longitude'] = $row['lon'];
				array_push($returnArray, $tmpCity);
			}
		}

		if ($min_count <= count($returnArray)) {
				return $returnArray;
		}

		$rows = $g_ado_db->GetAll($queryStr3);

		$returnArray = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
                $name_id = $row['name_id'];
                if (array_key_exists($name_id, $used_ids)) {continue;}
                $used_ids[$name_id] = true;

				$some_rows = true;
				$tmpCity = array();
				$tmpCity['name'] = $row['name'];
				$tmpCity['country'] = $row['cc'];
				$tmpCity['population'] = $row['pop'];
				$tmpCity['latitude'] = $row['lat'];
				$tmpCity['longitude'] = $row['lon'];
				array_push($returnArray, $tmpCity);
			}
		}

		if ($min_count <= count($returnArray)) {
				return $returnArray;
		}

		$rows = $g_ado_db->GetAll($queryStr4);

		$returnArray = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
                $name_id = $row['name_id'];
                if (array_key_exists($name_id, $used_ids)) {continue;}
                $used_ids[$name_id] = true;

				$some_rows = true;
				$tmpCity = array();
				$tmpCity['name'] = $row['name'];
				$tmpCity['country'] = $row['cc'];
				$tmpCity['population'] = $row['pop'];
				$tmpCity['latitude'] = $row['lat'];
				$tmpCity['longitude'] = $row['lon'];
				array_push($returnArray, $tmpCity);
			}
		}

		return $returnArray;

	} // fn FindCitiesByPosition

} // class Geo_Names

?>
