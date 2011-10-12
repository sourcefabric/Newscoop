<?php

/**
 * Providing sub/region info
 */
class RegionInfo {

    var $region_zipcodes = array();
    var $region_names = array();

    public function __construct($p_zipCodes = '') {
        if (empty($p_zipCodes)) {
            $p_zipCodes = dirname(dirname(__FILE__)) .DIRECTORY_SEPARATOR. 'data' .DIRECTORY_SEPARATOR. 'regions' .DIRECTORY_SEPARATOR. 'zipcodes.csv';
        }

        $col_zip = 2;
        $col_kanton = 6;
        $min_col_count = 7;

        $this->region_names = array(
'AG' => 'aargau',
'AR' => 'appenzell_ausserrhoden',
'AI' => 'appenzell_innerrhoden',
'BL' => 'basel_landschaft',
'BS' => 'basel_stadt',
'BE' => 'bern',
'FR' => 'freiburg',
'GE' => 'genf',
'GL' => 'glarus',
'GR' => 'graubuenden',
'JU' => 'jura',
'LU' => 'luzern',
'NE' => 'neuenburg',
'NW' => 'nidwalden',
'OW' => 'obwalden',
'SH' => 'schaffhausen',
'SZ' => 'schwyz',
'SO' => 'solothurn',
'SG' => 'st_gallen',
'TI' => 'tessin',
'TG' => 'thurgau',
'UR' => 'uri',
'VD' => 'waadt',
'VS' => 'wallis',
'ZG' => 'zug',
'ZH' => 'zuerich',
        );

        $region_zipcodes = array();

        $zip_file = fopen($p_zipCodes, 'r');
        fgets($zip_file);
        while(true) {
            $one_line = fgetcsv($zip_file);
            if (!is_array($one_line)) {
                break;
            }
            if ($min_col_count > count($one_line)) {
                continue;
            }
            $cur_zip = $one_line[$col_zip];
            $cur_kanton = $one_line[$col_kanton];

            if (!isset($this->region_zipcodes[$cur_kanton])) {
                $this->region_zipcodes[$cur_kanton] = array();
            }
            $this->region_zipcodes[$cur_kanton][] = $cur_zip;

        }

        fclose($zip_file);
    }

    public function getZipcodes() {
        return $this->region_zipcodes;
    }
    public function getNames() {
        return $this->region_names;
    }

    /*
     * Creates region info from zipcodes
     * 
     * @param string $p_zipCode
	 * @param string $p_countryCode
	 * @return array
     */
    public static function ZipRegion($p_zipCode, $p_countryCode)
    {
        $regions = array();
        $p_zipCode = '' . $p_zipCode;

        if ('ch' == strtolower($p_countryCode)) {
            $geneva_zip = '1211';
            if (substr($p_zipCode, 0, strlen($geneva_zip)) == $geneva_zip) {
                $p_zipCode = $geneva_zip;
            }

            if (!is_numeric($p_zipCode)) {
                return $regions;
            }
            if (4 != strlen($p_zipCode)) {
                return $regions;
            }
            $start1 = substr($p_zipCode, 0, 1);
            $regions['region'] = $start1 . 'xxx';
            $start2 = substr($p_zipCode, 0, 2);
            $regions['subregion'] = $start2 . 'xx';
        }

        return $regions;
    } // fn ZipRegion

    /*
     * Creates region info from zipcodes
     * 
     * @param string $p_zipCode
	 * @param string $p_countryCode
	 * @return array
     */
    public function ZipRegions($p_zipCode, $p_countryCode)
    {
        $regions = array();
        $p_zipCode = '' . $p_zipCode;

        $region_topics = array();

        if ('ch' == strtolower($p_countryCode)) {
            $geneva_zip = '1211';
            if (substr($p_zipCode, 0, strlen($geneva_zip)) == $geneva_zip) {
                $p_zipCode = $geneva_zip;
            }

            if ('4' == substr($p_zipCode, 0, 1)) {
                $region_topics[] = 'basel_region';
            }

            foreach ($this->region_zipcodes as $region_name => $region_zips) {
                if (in_array($p_zipCode, $region_zips)) {
                    if (isset($this->region_names[$region_name])) {
                        $region_topics[] = $this->region_names[$region_name];
                    }
                }
            }

        }

        return $region_topics;
    } // fn ZipRegions


} // class RegionInfo
