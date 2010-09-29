<?php
require_once  dirname(__FILE__) . DIR_SEP. 'TemplateCacheHandler.php';

class TemplateCacheHandler_DB extends TemplateCacheHandler
{
    private static $m_name = 'DB',
                   $m_description = "It allows to store tempalte cache in a database.";

    /**
     * Returns true if the engine was supported in PHP, false otherwise.
     * @return boolean
     */
    public function isSupported()
    {
        return true;
    }

    /**
     * Clears template cache storage.
     * @param $p_campsiteVector
     * @return boolean
     */
    public function clean()
    {
        $content = $smrty_obj = null;
        self::handler('clean', $smarty_obj, $cache_content);
    }

    /**
     * Updates template cache storage by given campsite vector.
     */
    public function update($campsiteVector)
    {
        global $g_ado_db;
        $queryStr = 'DELETE FROM Cache WHERE ' . self::vectorToWhereString($campsiteVector);
        $g_ado_db->Execute($queryStr);

        if ($campsiteVector['article']) {
            $whereStr = "language = {$campsiteVector['language']} AND ";
            $whereStr .= "publication = {$campsiteVector['publication']} AND ";
            $whereStr .= "issue >= {$campsiteVector['issue']} AND ";
            $queryStr = 'DELETE FROM Cache WHERE ' . $whereStr . "section = {$campsiteVector['section']} AND ";
            $queryStr .= "article IS NULL";
            $g_ado_db->Execute($queryStr);
        }
        if ($campsiteVector['issue']) {
            $queryStr = 'DELETE FROM Cache WHERE ' . $whereStr . "section IS NULL AND article IS NULL";
            $g_ado_db->Execute($queryStr);
        }
        return;
    }


    /**
     * Returns a short description of the cache engine.
     * @return string
     */
    public function description()
    {
        return self::$m_description;
    }

    static function handler($action, &$smarty_obj, &$cache_content, $tpl_file = null, $cache_id = null,
        $compile_id = null, $exp_time = null)
    {
        global $g_ado_db;
        static $cacheExists;

        $return = false;
        $uri = CampSite::GetURIInstance();
        $campsiteVector = $uri->getCampsiteVector();

            switch ($action) {
            case 'read':
                $whereStr = self::vectorToWhereString($campsiteVector);
                $whereStr .= " AND template = '$tpl_file'";

                $queryStr = 'SELECT expired, content FROM Cache WHERE ' . $whereStr;
                $result = $g_ado_db->GetRow($queryStr);
                if ($result) {
                    if ($result['expired'] > time()) {
                        $cacheExists = true;
                        $cache_content = $result['content'];
                        $result = true;
                    } else {
                        // clear expired cache
                        $queryStr = 'DELETE FROM Cache WHERE ' . $whereStr;
                        $g_ado_db->Execute($queryStr);
                        $cacheExists = false;
                        $return = false;
                    }
                } else {
                    $cacheExists = false;
                    $return = false;
                }
                break;

            case 'write':
                // in case template changing should delete old cached templates
                if ($cacheExists) {
                    $queryStr = 'DELETE FROM Cache WHERE template = ' . "'$tpl_file'";
                    $g_ado_db->GetOne($queryStr);
                }

                // insert new cached template
                $queryStr = 'INSERT IGNORE INTO Cache ';
                $queryStr .= '(' . implode(',', array_keys($campsiteVector));
                $queryStr .=  ',template,expired,content) VALUES (';
                foreach ($campsiteVector as $key => $value) {
                    $queryStr .= is_null($value) ? 'NULL,' : $value . ',';
                }
                $queryStr .= "'$tpl_file',$exp_time,'" . addslashes($cache_content) . "')";
                $g_ado_db->Execute($queryStr);
                $return = $g_ado_db->Affected_Rows() > 0;
                break;

            case 'clean':
                $queryStr = 'DELETE FROM Cache';
                if ($tpl_file) {
                    $queryStr .= " WHERE template = '$tpl_file'";
                }
                $g_ado_db->Execute($queryStr);
                $return = true;
                break;

            default:
        }
        return $return;
    }

    static function vectorToWhereString($vector)
    {
        $output = null;
        foreach ((array)$vector as $key => $value) {
            if ($value) {
                $output .= $key . ' = ' . $value;
            } else {
                $output .= $key . ' IS NULL';
            }
            if ($key != 'article') {
                $output .= ' AND ';
            }
        }
        return $output;
    }
}

?>