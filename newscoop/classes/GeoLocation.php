<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once dirname(__FILE__) . '/DatabaseObject.php';
require_once dirname(__FILE__) . '/IGeoLocation.php';
require_once dirname(__FILE__) . '/GeoMapLocationContent.php';

/**
 * @package Campsite
 */
class Geo_Location extends DatabaseObject implements IGeoLocation
{
    const TABLE = 'Locations';

    /**
     * @var string
     */
    public $m_dbTableName = self::TABLE;

    /**
     * @var array
     */
    public $m_keyColumnNames = array('id');

    /**
     * @var array
     */
    public $m_columnNames = array(
        'id',
        'poi_location',
        'poi_type',
        'poi_type_style',
        'poi_center',
        'poi_radius',
        'IdUser',
        'time_updated',
    );

    /**
     * @var bool
     */
    public $m_keyIsAutoIncrement = true;

    /**
     * @param mixed $arg
     */
    public function __construct($arg = NULL, $p_forceExists = false)
    {
        parent::__construct($this->m_columnNames);

        if (is_numeric($arg)) {
            $this->m_data['id'] = (int) $arg;
            $this->fetch();
        } else {
            $this->fetch($arg, $p_forceExists);
        }
    }

    /**
     * Fetch a single record from the database for the given key.
     *
     * @param array $p_arg
     *      If the record has already been fetched and we just need to
     *      assign the data to the object's internal member variable.
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    public function fetch($arg = null, $p_forceExists = false)
    {
        global $g_ado_db;

        if (is_array($arg)) {
            return parent::fetch($arg, $p_forceExists);
        }

        if (!$this->keyValuesExist()) {
            return false;
        }
        if ($this->readFromCache() !== false) {
            return true;
        }

        $queryStr = 'SELECT *, X(poi_location) as latitude, Y(poi_location) as longitude
                FROM ' . self::TABLE . '
                WHERE id = ' . $this->getId();
        $this->m_data = $g_ado_db->GetRow($queryStr);
        $this->m_exists = count($this->m_data) > 0;

        if ($this->m_exists) {
            // Write the object to cache
            $this->writeCache();
        }

        return $this->m_exists;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->m_data['id'];
    }

    /**
     * Get latitude
     * @return float
     */
    public function getLatitude()
    {
        return (float) $this->m_data['latitude'];
    }

    /**
     * Get longitude
     * @return float
     */
    public function getLongitude()
    {
        return (float) $this->m_data['longitude'];
    }

    /**
     * @return
     */
    public function getPOILocation()
    {
        return $this->m_data['poi_location'];
    }

    /**
     * @return string
     */
    public function getPOIType()
    {
        return (string) $this->m_data['poi_type'];
    }

    /**
     * @return
     */
    public function getPOITypeStyle()
    {
        return (int) $this->m_data['poi_type_style'];
    }

    /**
     * @return
     */
    public function getPOICenter()
    {
        return $this->m_data['poi_center'];
    }

    /**
     * @return double
     */
    public function getPOIRadius()
    {
        return (double) $this->m_data['poi_radius'];
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return (int) $this->m_data['IdUser'];
    }

    /**
     * @return timestamp
     */
    public function getLastModified()
    {
        return $this->m_data['time_updated'];
    }

    /**
     * Looks whether the location is llready at the database
     * NOTE: the 'location' ('center') parameters should be array with points (a point) with lat/lon values
     *
     * @param array $p_location
     * @param string $p_type
     * @param int $p_style
     * @param array $p_center
     * @param int $p_radius
     *
     * @return int
     */
    public static function FindLocation($p_location, $p_type, $p_style, $p_center, $p_radius)
    {
        global $g_ado_db;

        if ('point' != $p_type) {return null;}

        $queryStr_point = 'SELECT id FROM ' . self::TABLE
            . ' WHERE poi_location = GeomFromText(\'POINT(%%poi_lat%% %%poi_lon%%)\') AND poi_type = "point" ';
        $queryStr_point .= "AND poi_type_style = ? AND poi_center = PointFromText('POINT(%%cen_lat%% %%cen_lon%%)') AND poi_radius = ?";

        $loc_id = 0;

        // here checking for points; nothing else yet
        if ('point' == $p_type)
        {
            try
            {
                $loc_latitude = '' . $p_location[0]['latitude'];
                $loc_longitude = '' . $p_location[0]['longitude'];
                $cen_latitude = '' . $p_center['latitude'];
                $cen_longitude = '' . $p_center['longitude'];

                $correct_coords = true;
                if (!is_numeric($loc_latitude)) {$correct_coords = false;}
                if (!is_numeric($loc_longitude)) {$correct_coords = false;}
                if (!is_numeric($cen_latitude)) {$correct_coords = false;}
                if (!is_numeric($cen_longitude)) {$correct_coords = false;}
                if (!$correct_coords) {return 0;}

                $queryStr_point = str_replace('%%poi_lat%%', $loc_latitude, $queryStr_point);
                $queryStr_point = str_replace('%%poi_lon%%', $loc_longitude, $queryStr_point);
                $queryStr_point = str_replace('%%cen_lat%%', $cen_latitude, $queryStr_point);
                $queryStr_point = str_replace('%%cen_lon%%', $cen_longitude, $queryStr_point);

                $sql_params = array();

                $sql_params[] = 0 + $p_style;
                $sql_params[] = 0 + $p_radius;

                $rows = $g_ado_db->GetAll($queryStr_point, $sql_params);
                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        $loc_id = $row['id'];
                        if ($loc_id && (0 < $loc_id)) {break;}
                    }
                }
            }
            catch (Exception $exc)
            {
                return false;
            }
        }

        return $loc_id;
    } // fn FindLocation

    /**
     * Updates the location, the COW way
     *
     * @param int $p_mapId
     * @param array $p_locations
     *
     * @return bool
     */
    public static function UpdateLocations($p_mapId, $p_locations)
    {
        global $g_ado_db;
        global $g_user;

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
        $queryStr_loc_id = 'SELECT fk_location_id AS loc FROM ' . Geo_MapLocation::TABLE . ' WHERE id = ?';
        // ad B 2)
        $queryStr_loc_in = 'INSERT INTO ' . self::TABLE . ' (poi_location, poi_type, poi_type_style, poi_center, poi_radius, IdUser) VALUES (';
        $queryStr_loc_in .= "GeomFromText('POINT(? ?)'), 'point', 0, PointFromText('POINT(? ?)'), 0, %%user_id%%";
        $queryStr_loc_in .= ')';
        // ad B 4)
        $queryStr_map_up = 'UPDATE ' . Geo_MapLocation::TABLE . ' SET fk_location_id = ? WHERE id = ?';
        // ad B 6)
        $queryStr_loc_rm = 'DELETE FROM ' . self::TABLE . ' WHERE id = ? AND NOT EXISTS (SELECT id FROM MapLocations WHERE fk_location_id = ?)';

        // updating current POIs, inserting new POIs
        foreach ($p_locations as $poi_obj)
        {
            $poi = get_object_vars($poi_obj);

            // ad B 1)
            $loc_old_id = null;
            try
            {
                $maploc_sel_params = array();
                $maploc_sel_params[] = $poi['id'];

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

            $loc_new_id = null;

            $new_loc = array();
            $new_loc[] = array('latitude' => $poi['latitude'], 'longitude' => $poi['longitude']);
            $new_cen = array('latitude' => $poi['latitude'], 'longitude' => $poi['longitude']);
            $new_style = 0;
            $new_radius = 0;
            $reuse_id = self::FindLocation($new_loc, 'point', $new_style, $new_cen, $new_radius);

            if ($reuse_id && (0 < $reuse_id))
            {
                $loc_new_id = $reuse_id;
            }
            else
            {
                // ad B 2)
                {
                    $loc_in_params = array();
                    $loc_in_params[] = $poi['latitude'];
                    $loc_in_params[] = $poi['longitude'];
                    $loc_in_params[] = $poi['latitude'];
                    $loc_in_params[] = $poi['longitude'];

                    $queryStr_loc_in = str_replace('%%user_id%%', $g_user->getUserId(), $queryStr_loc_in);

                    $success = $g_ado_db->Execute($queryStr_loc_in, $loc_in_params);
                }

                // ad B 3)
                // taking its ID for the next processing
                $loc_new_id = $g_ado_db->Insert_ID();
            }

            // ad B 4)
            {
                $map_up_params = array();
                $map_up_params[] = $loc_new_id;
                $map_up_params[] = $poi['id'];

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

        return true;
    } // fn UpdateLocations

    /**
     * Updates the point marker icon file name
     *
     * @param array $poi
     *
     * @return void
     */
    public static function UpdateIcon($poi)
    {
        global $g_ado_db;

        $queryStr = 'UPDATE ' . Geo_MapLocation::TABLE . ' SET poi_style = ? WHERE id = ?';

        $sql_params = array();
        $sql_params[] = $poi['style'];
        $sql_params[] = $poi['location_id'];

        $success = $g_ado_db->Execute($queryStr, $sql_params);
    } // fn UpdateIcon

    /**
     * Updates the point text content, the COW way
     *
     * @param int $p_mapId
     * @param array $p_contents
     *
     * @return bool
     */
    public static function UpdateContents($p_mapId, $p_contents)
    {
        global $g_ado_db;

        foreach ($p_contents as $poi_obj)
        {
            $poi = get_object_vars($poi_obj);

            if ($poi['icon_changed'])
            {
                self::UpdateIcon($poi);
            }
            if ($poi['state_changed'])
            {
                Geo_MapLocationContent::UpdateState($poi);
            }
            if ($poi['image_changed'])
            {
                Geo_Multimedia::UpdateMedia($poi, 'image');
            }
            if ($poi['video_changed'])
            {
                Geo_Multimedia::UpdateMedia($poi, 'video');
            }

            if ($poi['text_changed'])
            {
                Geo_MapLocationContent::UpdateText($poi);
            }

        }

        return true;
    } // fn UpdateContents

    /**
     * Updates the point ordering at the map
     * NB: p_indices are used for the newly inserted points from the ajax request
     *
     * @param int $p_mapId
     * @param array $p_reorder
     * @param array $p_indices
     *
     * @return bool
     */
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
        $queryStr_rnk_up = 'UPDATE ' . Geo_MapLocation::TABLE . ' SET rank = ? WHERE id = ?';

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
    } // fn UpdateOrder
} // class Geo_Location

