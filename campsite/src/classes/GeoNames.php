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

} // class Geo_Names

?>
