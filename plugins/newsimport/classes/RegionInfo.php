<?php

/**
 * Providing sub/region info
 */
class RegionInfo {

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

} // class RegionInfo

/*
  NB:
    For the Swiss zipcodes, see http://en.wikipedia.org/wiki/Postal_codes_in_Switzerland_and_Liechtenstein
*/
