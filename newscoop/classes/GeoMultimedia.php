<?php
/**
 * @package Campsite
 */

require_once dirname(__FILE__) . '/DatabaseObject.php';
require_once dirname(__FILE__) . '/IGeoMultimedia.php';
require_once dirname(__FILE__) . '/IGeoMapLocation.php';

/**
 * @package Campsite
 */
class Geo_Multimedia extends DatabaseObject implements IGeoMultimedia
{
    const TABLE = 'Multimedia';
    const TABLE_JOIN = 'MapLocationMultimedia';

    /** @var string */
    public $m_dbTableName = self::TABLE;

    /** @var array */
    public $m_keyColumnNames = array('id');

    /** @var array */
    public $m_columnNames = array(
        'id',
        'media_type',
        'media_spec',
        'media_src',
        'media_height',
        'media_width',
        'options',
        'IdUser',
    );


    /**
     * @param mixed $arg
     */
    public function __construct($arg = NULL)
    {
        parent::__construct($this->m_columnNames);

        if (is_array($arg)) {
            $this->fetch($arg);
        } else if (is_numeric($arg)) {
            $this->m_data['id'] = (int) $arg;
            $this->fetch();
        }
    }

    /**
     * Get height
     * @return int
     */
    public function getHeight()
    {
        return (int) $this->m_data['media_height'];
    }

    /**
     * Get spec
     * @return string
     */
    public function getSpec()
    {
        return (string) $this->m_data['media_spec'];
    }

    /**
     * Get src
     * @return string
     */
    public function getSrc()
    {
        return (string) $this->m_data['media_src'];
    }

    /**
     * Get type
     * @return string
     */
    public function getType()
    {
        return (string) $this->m_data['media_type'];
    }

    /**
     * Get width
     * @return int
     */
    public function getWidth()
    {
        return (int) $this->m_data['media_width'];
    }

    /**
     * Get multimedia for map location
     * @param IGeoMapLocation $p_mapLocation
     * @return array of IGeoMultimedia
     */
    public static function GetByMapLocation(IGeoMapLocation $p_mapLocation)
    {
        global $g_ado_db;

        $queryStr = 'SELECT m.*
            FROM ' . self::TABLE . ' m
                INNER JOIN ' . self::TABLE_JOIN . ' mlm
                    ON m.id = mlm.fk_multimedia_id
            WHERE mlm.fk_maplocation_id = ' . $p_mapLocation->getId();
        $rows = $g_ado_db->GetAll($queryStr);

        $items = array();
        foreach ((array) $rows as $row) {
            $items[] = new self((array) $row);
        }
        return $items;
    } // fn GetByMapLocation

    /**
     * Inserts multimedia of a point
     *
     * @param int $ml_id
     * @param array $poi
     *
     * @return void
     */
    public static function InsertMultimedia($ml_id, $poi)
    {
        global $g_ado_db;
        global $g_user;

        $queryStr_mm = 'INSERT INTO ' . self::TABLE . ' (';
        $queryStr_mm .= 'media_type, media_spec, media_src, media_width, media_height, IdUser';
        $queryStr_mm .= ') VALUES (';

        $quest_marks = array();
        for ($ind = 0; $ind < 5; $ind++) {$quest_marks[] = '?';}
        $queryStr_mm .= implode(', ', $quest_marks);

        $queryStr_mm .= ', %%user_id%%)';

        $queryStr_loc_mm = 'INSERT INTO ' . self::TABLE_JOIN . ' (';
        $queryStr_loc_mm .= 'fk_maplocation_id, fk_multimedia_id';
        $queryStr_loc_mm .= ') VALUES (';
        $queryStr_loc_mm .= '?, ?';
        $queryStr_loc_mm .= ')';

        if ('' != $poi['image_src'])
        {
            $mm_params = array();
            $mm_params[] = 'image';
            $mm_params[] = '';
            $mm_params[] = '' . $poi['image_src'];
            $mm_params[] = 0 + $poi['image_width'];
            $mm_params[] = 0 + $poi['image_height'];


            $mm_options = ''; // currently no options used
            $reuse_id = Geo_Multimedia::FindMedia('image', '', $poi['image_src'], $poi['image_width'], $poi['image_height'], $mm_options);

            $mm_id = 0;
            if ($reuse_id && (0 < $reuse_id))
            {
                $mm_id = $reuse_id;
            }
            else
            {
                $queryStr_mm = str_replace('%%user_id%%', $g_user->getUserId(), $queryStr_mm);

                $success = $g_ado_db->Execute($queryStr_mm, $mm_params);

                $mm_id = $g_ado_db->Insert_ID();
            }

            $loc_mm_params = array();
            $loc_mm_params[] = $ml_id;
            $loc_mm_params[] = $mm_id;

            $success = $g_ado_db->Execute($queryStr_loc_mm, $loc_mm_params);
        }

        if ('' != $poi['video_id'])
        {
            $mm_params = array();
            $mm_params[] = 'video';
            $mm_params[] = '' . $poi['video_type'];
            $mm_params[] = '' . $poi['video_id'];
            $mm_params[] = 0 + $poi['video_width'];
            $mm_params[] = 0 + $poi['video_height'];

            $mm_options = ''; // currently no options used
            $reuse_id = Geo_Multimedia::FindMedia('video', $poi['video_type'], $poi['video_id'], $poi['video_width'], $poi['video_height'], $mm_options);

            $mm_id = 0;
            if ($reuse_id && (0 < $reuse_id))
            {
                $mm_id = $reuse_id;
            }
            else
            {
                $queryStr_mm = str_replace('%%user_id%%', $g_user->getUserId(), $queryStr_mm);

                $success = $g_ado_db->Execute($queryStr_mm, $mm_params);

                $mm_id = $g_ado_db->Insert_ID();
            }

            $loc_mm_params = array();
            $loc_mm_params[] = $ml_id;
            $loc_mm_params[] = $mm_id;

            $success = $g_ado_db->Execute($queryStr_loc_mm, $loc_mm_params);
        }
    } // fn InsertMultimedia

    /**
     * Looks whether a media are already in the database, for the COW work
     *
     * @param string $p_type
     * @param string $p_spec
     * @param string $p_src
     * @param int $p_width
     * @param int $p_height
     * @param string $p_options
     *
     * @return int
     */
    public static function FindMedia($p_type, $p_spec, $p_src, $p_width, $p_height, $p_options)
    {
        global $g_ado_db;

        $queryStr = 'SELECT id FROM ' . self::TABLE . ' WHERE media_type = ? AND media_spec = ? AND media_src = ? AND media_width = ? AND media_height = ? AND options = ?';

        $med_id = 0;
        try
        {
            $sql_params = array();

            $sql_params[] = '' . $p_type;
            $sql_params[] = '' . $p_spec;
            $sql_params[] = '' . $p_src;
            $sql_params[] = 0 + $p_width;
            $sql_params[] = 0 + $p_height;
            $sql_params[] = '' . $p_options;

            $rows = $g_ado_db->GetAll($queryStr, $sql_params);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $med_id = $row['id'];
                }
            }
        }
        catch (Exception $exc)
        {
            return false;
        }

        return $med_id;
    } // fn FindMedia

    /**
     * Updates media associated to a point
     *
     * @param string $poi
     * @param string $mm_type
     *
     * @return void
     */
    public static function UpdateMedia($poi, $mm_type)
    {
        global $g_ado_db;
        global $g_user;

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
        $queryStr_med_id = 'SELECT fk_multimedia_id AS med FROM ' . self::TABLE_JOIN . ' WHERE id = ?';
        // ad B 2)

        $queryStr_med_in = 'INSERT INTO ' . self::TABLE . ' (media_type, media_spec, media_src, media_width, media_height, IdUser) VALUES (';
        $queryStr_med_in .= '?, ?, ?, ?, ?';
        $queryStr_med_in .= ', %%user_id%%)';

        // ad B 4)
        $queryStr_map_up = 'UPDATE ' . self::TABLE_JOIN . ' SET fk_multimedia_id = ? WHERE id = ?';
        $queryStr_map_in = 'INSERT INTO ' . self::TABLE_JOIN . ' (fk_maplocation_id, fk_multimedia_id) VALUES (?, ?)';
        $queryStr_map_rm = 'DELETE FROM ' . self::TABLE_JOIN . ' WHERE id = ?';
        // ad B 6)
        $queryStr_med_rm = 'DELETE FROM ' . self::TABLE . ' WHERE id = ? AND NOT EXISTS (SELECT id FROM ' . self::TABLE_JOIN . ' WHERE fk_multimedia_id = ?)';

        // ad B 1)

        $mm_id = null;
        $mm_spec = '';
        $mm_src = '';
        $mm_width = '';
        $mm_height = '';
        $mm_insert = false;
        if ('image' == $mm_type)
        {
            $mm_id = $poi['image_mm'];
            $mm_spec = '';
            $mm_src = $poi['image_src'];
            $mm_width = $poi['image_width'];
            $mm_height = $poi['image_height'];
            if ('' != $mm_src) {$mm_insert = true;}
        }
        if ('video' == $mm_type)
        {
            $mm_id = $poi['video_mm'];
            $mm_spec = $poi['video_type'];
            $mm_src = $poi['video_id'];
            $mm_width = $poi['video_width'];
            $mm_height = $poi['video_height'];
            if ('' != $mm_src) {$mm_insert = true;}
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

        $med_new_id = 0;

        // insert (and connect) just when there is something to insert
        if ($mm_insert)
        {
            $mm_options = ''; // currently no options used
            $reuse_id = Geo_Multimedia::FindMedia($mm_type, $mm_spec, $mm_src, $mm_width, $mm_height, $mm_options);

            if ($reuse_id && (0 < $reuse_id))
            {
                $med_new_id = $reuse_id;
            }
            else
            {
                $med_ins_params = array();
                $med_ins_params[] = '' . $mm_type;
                $med_ins_params[] = '' . $mm_spec;
                $med_ins_params[] = '' . $mm_src;
                $med_ins_params[] = 0 + $mm_width;
                $med_ins_params[] = 0 + $mm_height;

                $queryStr_med_in = str_replace('%%user_id%%', $g_user->getUserId(), $queryStr_med_in);

                $success = $g_ado_db->Execute($queryStr_med_in, $med_ins_params);

                // ad B 3)
                $med_new_id = $g_ado_db->Insert_ID();
            }

            // ad B 4) -- was no media for this connector, thus create a new one
            if (null === $med_old_id)
            {
                $map_in_params = array();
                $map_in_params[] = $poi['location_id'];
                $map_in_params[] = $med_new_id;

                $success = $g_ado_db->Execute($queryStr_map_in, $map_in_params);

                return;
            }
            else // -- already had a media, thus just update the connector
            {
                $map_up_params = array();
                $map_up_params[] = $med_new_id;
                $map_up_params[] = $mm_id;

                $success = $g_ado_db->Execute($queryStr_map_up, $map_up_params);
            }
        }
        else // here: nothing to left connected, thus deleting the old connector if any
        {
            if ($mm_id) // if a connector was there
            {
                // ad B 4) deleting the old connector;
                $map_rm_params = array();
                $map_rm_params[] = $mm_id;

                $success = $g_ado_db->Execute($queryStr_map_rm, $map_rm_params);
            }
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


    } // fn UpdateMedia

} // class Geo_Multimedia
