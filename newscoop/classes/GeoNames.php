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


    public function FindCitiesByName($cityName, $countryCode = '')
    {
        $loc_found = array();
        $rem_found = array();
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');

        if ($preferencesService->GeoSearchLocalGeonames) {
            $loc_found = $this->FindCitiesByNameLocal($cityName, $countryCode);
        }
        if (!is_array($loc_found)) {
            $loc_found = array();
        }

        if ($preferencesService->GeoSearchMapquestNominatim) {
            $rem_found = $this->FindCitiesByNameRemote($cityName, $countryCode);
        }
        if (!is_array($rem_found)) {
            $rem_found = array();
        }

        return array_merge($loc_found, $rem_found);
    }

    /**
     * Finds cities on given name and country
     *
     * @param string $p_cityName
     * @param string $p_countryCode
     *
     * @return array
     */
    public function FindCitiesByNameLocal($p_cityName, $p_countryCode = '')
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
                $one_town_info = (array) $row;
                $one_town_info['provider'] = 'GN';
                $one_town_info['copyright'] = 'Data © GeoNames.org, CC-BY';
                $cities[] = $one_town_info;
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
                    $one_town_info = (array) $row;
                    $one_town_info['provider'] = 'GN';
                    $one_town_info['copyright'] = 'Data © GeoNames.org, CC-BY';
                    $cities[] = $one_town_info;
                }
            }

            if (sizeof($cities) >= self::LOCATIONS_LIMIT) {
                return $cities;
            }
        }

        return $cities;
    } // fn FindCitiesByPosition

    public function FindCitiesByNameRemote($streetAddress, $countryCode = '') {
        $no_res = array();

        $request_url = 'http://open.mapquestapi.com/nominatim/v1/search.php?format=json&q=';
        //$request_url = 'http://nominatim.openstreetmap.org/search?format=json&q=';
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');

        $streetAddress = trim($streetAddress);
        if (empty($streetAddress)) {
            return $no_res;
        }

        $request_url .= urlencode($streetAddress);

        $countryCode = trim($countryCode);
        if (!empty($countryCode)) {
            $request_url .=  '&countrycodes=' . urlencode($countryCode);
        }

        $geo_preferred_lang = $preferencesService->GeoSearchPreferredLanguage;
        if (empty($geo_preferred_lang)) {
            $geo_preferred_lang = 'en';
        }

        $request_url .= '&limit=40&accept-language=' . urlencode($geo_preferred_lang) . ';q=1,en;q=0.5&addressdetails=1';

        $found_locations = null;
        try {
            $curlHandle = curl_init($request_url);

            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array ('Accept: application/json'));
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curlHandle, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curlHandle, CURLOPT_NOSIGNAL, true);
            curl_setopt($curlHandle, CURLOPT_TIMEOUT_MS, 5000);

            $response = curl_exec($curlHandle);
            curl_close($curlHandle);

            //$found_locations = json_decode($response, true, 512, JSON_BIGINT_AS_STRING);
            $found_locations = json_decode($response, true);

        }
        catch (\Exception $exc) {
            $found_locations = null;
        }

        if (empty($found_locations) || (!is_array($found_locations))) {
            return $no_res;
        }

        $res = array();
        foreach ($found_locations as $one_loc) {
            $cur_id = 0;
            if (isset($one_loc['place_id'])) {
                $cur_id = $one_loc['place_id'];
            }

            if ((!isset($one_loc['lat'])) || (!isset($one_loc['lon'])) || (!isset($one_loc['display_name']))) {
                continue;
            }
            $cur_lat = $one_loc['lat'];
            $cur_lon = $one_loc['lon'];

            $cur_desc = $one_loc['display_name'];

            $cur_name = '';
            foreach(explode(',', $cur_desc) as $name_part) {
                $name_part = trim($name_part);
                if (empty($name_part)) {
                    continue;
                }
                $cur_name = $name_part;
                break;
            }
            if (isset($one_loc['address'])) {
                $addr_name_parts = array();

                $street_name_parts = array();
                foreach(array('road', 'house_number') as $one_street_spec) {
                    if (isset($one_loc['address']) && isset($one_loc['address'][$one_street_spec])) {
                        $street_spec_part = trim($one_loc['address'][$one_street_spec]);
                        if (!empty($street_spec_part)) {
                            if (('road' == $one_street_spec) && is_numeric($street_spec_part)) {
                                continue;
                            }
                            $street_name_parts[] = $street_spec_part;
                        }
                    }
                }
                if (!empty($street_name_parts)) {
                    $addr_name_parts[] = implode(' ', $street_name_parts);
                }

                foreach(array('city', 'city_district', 'suburb', 'county', 'state') as $one_town_spec) {
                    if (isset($one_loc['address']) && isset($one_loc['address'][$one_town_spec])) {
                        $town_spec_part = trim($one_loc['address'][$one_town_spec]);
                        if (!empty($town_spec_part)) {
                            $addr_name_parts[] = $town_spec_part;
                            break;
                        }
                    }
                }

                if (!empty($addr_name_parts)) {
                    $cur_name = implode(', ', $addr_name_parts);
                }
            }

            if ('' == $cur_name) {
                continue;
            }

            $cur_country = '';
            if (isset($one_loc['address']) && isset($one_loc['address']['country_code'])) {
                $cur_country = trim($one_loc['address']['country_code']);
            }
            if (empty($cur_country)) {
                $cur_country = 'un';
            }

            $cur_type = null;
            if (isset($one_loc['type']) && (!empty($one_loc['type'])) && ('administrative' != $one_loc['type'])) {
                $cur_type = $one_loc['type'];
                if (isset($one_loc['address']) && isset($one_loc['address'][$cur_type]) && (!empty($one_loc['address'][$cur_type]))) {
                    $cur_name = str_replace('_', ' ', $one_loc['type']) . ' ' . $one_loc['address'][$cur_type] . ', ' . $cur_name;
                }
            }

            $res[] = array(
                'id' => $cur_id,
                'name' => $cur_name,
                'country' => $cur_country,
                'long_desc' => $cur_desc,
                'population' => '',
                'latitude' => $cur_lat,
                'longitude' => $cur_lon,
                'provider' => 'OSM',
                'copyright' => 'Data © OpenStreetMap contributors, ODbL 1.0',
                'type' => $cur_type,
            );
        }

        return $res;
    } // fn FindCitiesByNameRemote

} // class Geo_Names
