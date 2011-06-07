<?php
/**
 * @package Campsite
 */

require_once dirname(__FILE__) . '/DatabaseObject.php';

/**
 * The geo names class is for search on data per request
 */
class Geo_Names extends DatabaseObject
{
    const LOCATIONS_LIMIT = 20;

    /** @var array */
    public $m_keyColumnNames = array('id');

    /** @var string */
    public $m_dbTableName = 'CityLocations cl INNER JOIN CityNames cs ON cl.id = cs.fk_citylocations_id';

    /** @var array */
    public $m_columnNames = array(
        'id',
        'city_type',
        'population',
        'position',
        'elevation',
        'country_code',
        'time_zone',
        'city_name',
        'name_type',
    );

    /**
     */
    public function __construct()
    {
    }

    /**
     * Finds cities on given name and country
     *
     * @param string $p_cityName
     * @param string $p_countryCode
     *
     * @return array
     */
    public function FindCitiesByName($p_cityName, $p_countryCode = '')
    {
        global $g_ado_db;

        $cityName_changed = str_replace(' ', '%', trim($p_cityName));
        $is_exact = !strchr($p_cityName, '%');

        $queryStr = 'SELECT DISTINCT id, city_name as name, country_code as country, population, X(position) AS latitude, Y(position) AS longitude
            FROM ' . $this->m_dbTableName . ' WHERE city_name LIKE ?';

        if (!empty($p_countryCode)) {
            $queryStr .= ' AND cl.country_code = ?';
        }

        $sql_params = array(
            $p_cityName,
            $cityName_changed,
            $cityName_changed . '%',
            '%' . $cityName_changed . '%',
        );
        $queryStr .= ' GROUP BY id ORDER BY population DESC, id LIMIT 100';

        $cities = array();
        foreach ($sql_params as $param) {
            $params = array($param);
            if (!empty($p_countryCode)) {
                $params[] = (string) $p_countryCode;
            }
            $rows = $g_ado_db->GetAll($queryStr, $params);

            foreach ((array) $rows as $row) {
                $cities[] = (array) $row;
            }

            if (!empty($cities)) {
                break;
            }
        }

        return $cities;
    } // fn FindCitiesByName

    /**
     * Finds cities on given position
     *
     * @param string $p_longitude
     * @param string $p_latitude
     *
     * @return array
     */
    public function FindCitiesByPosition($p_longitude, $p_latitude)
    {
        global $g_ado_db;

        $lon = (float) $p_longitude;
        $lat = (float) $p_latitude;
        $used = array();
        $cities = array();
        for ($x = 0.04, $y = 0.08; $x <= 2.0; $x *= 2, $y *= 2) {
            $queryStr = 'SELECT DISTINCT id, city_name as name, name_type, X(position) AS latitude, Y(position) AS longitude, population, country_code AS country
                FROM ' . $this->m_dbTableName . "
                WHERE Contains(PolygonFromText('POLYGON((";
            $queryStr .= sprintf('%f %f, %f %f, %f %f, %f %f, %f %f',
                $lat - $x, $lon - $y,
                $lat + $x, $lon - $y,
                $lat - $x, $lon + $y,
                $lat + $x, $lon + $y,
                $lat - $x, $lon - $y);
            $queryStr .= "))'), position) AND name_type = 'main'
                ORDER BY population DESC, id LIMIT 100";

            $rows = $g_ado_db->GetAll($queryStr);
            foreach ((array) $rows as $row) {
                if (empty($used[$row['id']])) {
                    $used[$row['id']] = true;
                    $cities[] = (array) $row;
                }
            }

            if (sizeof($cities) >= self::LOCATIONS_LIMIT) {
                return $cities;
            }
        }
    } // fn FindCitiesByPosition
} // class Geo_Names
