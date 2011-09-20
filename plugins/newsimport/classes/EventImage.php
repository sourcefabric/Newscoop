<?php

class EventImage {

    var $m_table_name = 'image_info';
    var $m_sqlite_name = '';

    public function __construct($p_source)
    {
    } // fn __construct

    public function prepareImageInfoCache()
    {
        $cre_req = 'CREATE TABLE ' . $this->m_table_name . ' (image_id INTEGER PRIMARY KEY, local INTEGER, url TEXT KEY, label TEXT, provider_id INTEGER)';

        @$db = new PDO ('sqlite:' . $this->m_sqlite_name);
        $stmt = $db->prepare($cre_req);
        $res = $stmt->execute();
        if (!$res) {
            echo ' wtf create ';
            return false;
        }

        return true;
    }

    public function checkImageInfoCache()
    {
        $sel_req = 'SELECT image_id, local, url, label, provider_id FROM ' . $this->m_table_name . ' WHERE url = :url';

        ;
    }

    public function updateImageInfoCache()
    {
        $ins_req = 'INSERT INTO ' . $this->m_table_name . ' (image_id, local, url, label, provider_id) VALUES (:image_id, :local, :url, :label, :provider_id)';

        ;
    }

}

