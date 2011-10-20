<?php

/**
 * Storing info on already used images to lower the count of downloading
 */
class EventImage {

    /**
     * Table name for image info
     * @var string
     */
    var $m_table_name = 'image_info';

    /**
     * Path to the sqlite db
     * @var string
     */
    var $m_sqlite_name = '';

    /**
     * Constructor, with db creation
     * @param string $p_dbName
     *
     */
    public function __construct($p_dbName)
    {
        $this->m_sqlite_name = $p_dbName;
        $this->prepareImageInfoCache();
    } // fn __construct

    /**
     * Creates sqlite db for info on already used images
     *
     * @return bool
     */
    public function prepareImageInfoCache()
    {
        $cre_req = 'CREATE TABLE IF NOT EXISTS ' . $this->m_table_name . ' (image_id INTEGER PRIMARY KEY, local INTEGER, url TEXT KEY, label TEXT, provider_id INTEGER)';

        @$db = new PDO ('sqlite:' . $this->m_sqlite_name);
        $stmt = $db->prepare($cre_req);
        $res = $stmt->execute();
        if (!$res) {
            return false;
        }

        return true;
    } // fn prepareImageInfoCache

    /**
     * Search for info on already used images of given url
     *
     * @param string $p_url
     * @return array
     */
    public function checkImageInfoCache($p_url)
    {
        $images = array();

        $sel_req = 'SELECT image_id, local, url, label, provider_id FROM ' . $this->m_table_name . ' WHERE url = :url';

        @$db = new PDO ('sqlite:' . $this->m_sqlite_name);
        $stmt = $db->prepare($sel_req);

        $stmt->bindParam(':url', $p_url, PDO::PARAM_STR);
        $res = $stmt->execute();
        if ($res) {
            while (true) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (empty($row)) {
                    break;
                }
                $images[] = array(
                    'image_id' => $row['image_id'],
                    'local' => $row['local'],
                    'url' => $row['url'],
                    'label' => $row['label'],
                    'provider_id' => $row['provider_id'],
                );
            }
        }
        return $images;
    } // fn checkImageInfoCache

    /**
     * Puts in info on a newly used image
     *
     * @param array $p_imageInfo
     * @return bool
     */
    public function insertImageIntoCache($p_imageInfo)
    {
        $ins_req = 'INSERT OR REPLACE INTO ' . $this->m_table_name . ' (image_id, local, url, label, provider_id) VALUES (:image_id, :local, :url, :label, :provider_id)';

        @$db = new PDO ('sqlite:' . $this->m_sqlite_name);
        $stmt = $db->prepare($ins_req);

        $stmt->bindParam(':image_id', $p_imageInfo['image_id'], PDO::PARAM_INT);
        $use_img_local = ($p_imageInfo['local']) ? 1 : 0;
        $stmt->bindParam(':local', $use_img_local, PDO::PARAM_INT);
        $stmt->bindParam(':url', $p_imageInfo['url'], PDO::PARAM_STR);
        $stmt->bindParam(':label', $p_imageInfo['label'], PDO::PARAM_STR);
        $stmt->bindParam(':provider_id', $p_imageInfo['provider_id'], PDO::PARAM_INT);

        $res = $stmt->execute();
        if (!$res) {
            return false;
        }

        return true;
    } // fn insertImageIntoCache

    /**
     * Removes info on deleted image
     *
     * @param mixed $p_id
     * @return bool
     */
    public function removeImageFromCache($p_id)
    {
        $del_req = 'DELETE FROM ' . $this->m_table_name . ' WHERE image_id = :image_id';

        @$db = new PDO ('sqlite:' . $this->m_sqlite_name);
        $stmt = $db->prepare($del_req);

        $image_id = 0 + $p_id;
        $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);

        $res = $stmt->execute();
        if (!$res) {
            return false;
        }

        return true;
    } // fn removeImageFromCache

} // class EventImage

