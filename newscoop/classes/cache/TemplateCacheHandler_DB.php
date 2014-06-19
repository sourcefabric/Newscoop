<?php
require_once  dirname(__FILE__) . DIR_SEP. 'TemplateCacheHandler.php';

class TemplateCacheHandler_DB extends TemplateCacheHandler
{
    private static $m_name = 'DB';
    private static $m_description = "It allows to store template cache in a database.";
    private $_ado_db;

    public function TemplateCacheHandler_DB()
    {
        global $g_ado_db;
        $this->_ado_db = $g_ado_db;
    }

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
     */
    public function clean($tpl_file = null)
    {
        $cache_content = $smrty_obj = null;
        self::handler('clean', $cache_content, $tpl_file);
    }

    /**
     * Updates template cache storage by given campsite vector.
     */
    public function update($campsiteVector, $exactUpdate = false)
    {
        $queryStr = 'UPDATE Cache SET status = "E" WHERE ' . self::vectorToWhereString($campsiteVector);
        $this->_ado_db->execute($queryStr);

        if ($exactUpdate) return;

        if (!empty($campsiteVector['language']) && !empty($campsiteVector['publication'])) {
            $whereStr = "language = {$campsiteVector['language']} AND "
            . "publication = {$campsiteVector['publication']} AND "
            . ($campsiteVector['issue'] ? "issue >= {$campsiteVector['issue']}" : 'issue = 0') . ' AND ';

            // clear language, publication, issue, section, null vector
            if (isset($campsiteVector['section'])) {
                $queryStr = 'UPDATE Cache SET status = "E" WHERE ' . $whereStr . "section = {$campsiteVector['section']} AND "
                . 'article = 0';
                $this->_ado_db->execute($queryStr);
            }

            // clear language, publication, issue, null, null vector
            $queryStr = 'UPDATE Cache SET status = "E" WHERE ' . $whereStr . "section = 0 AND article = 0";
            $this->_ado_db->execute($queryStr);

            // clear language, publication, null, null, null vector
            if (isset($campsiteVector['issue'])) {
                $queryStr = 'UPDATE Cache SET status = "E" WHERE language = '. "{$campsiteVector['language']} AND "
                . "publication = {$campsiteVector['publication']} AND issue = 0 AND section = 0 AND article = 0";
                $this->_ado_db->execute($queryStr);
            }

            // clear language, null, null, null, null vector
            if (isset($campsiteVector['issue'])) {
                $queryStr = 'UPDATE Cache SET status = "E" WHERE language = '. "{$campsiteVector['language']} AND "
                . 'publication = 0 AND issue = 0 AND section = 0 AND article = 0';
                $this->_ado_db->execute($queryStr);
            }

            // clear null, null, null, null, null vector
            if (isset($campsiteVector['issue'])) {
                $queryStr = 'UPDATE Cache SET status = "E" WHERE language = 0 AND publication = 0 AND issue = 0 AND '
                . 'section = 0 AND article = 0';
                $this->_ado_db->execute($queryStr);
            }
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

    static function handler($action, &$cache_content, $tpl_file = null, $cache_id = null,
        $compile_id = null, $exp_time = 0)
    {
        global $g_ado_db;
        static $cacheParams = array();
        $exp_time += time();
        $tpl_file = md5($tpl_file).substr($tpl_file, -15);

        $uri = CampSite::GetURIInstance();
        $smarty = CampTemplate::singleton();
        $campsiteVector = array_merge(
            $uri->getCampsiteVector(),
            $smarty->campsiteVector
        );

        $return = false;
        if ($action != 'clean') {
            if (!isset($campsiteVector['params'])) {
                $campsiteVector['params'] = null;
            }
        }

        switch ($action) {
            case 'read':
                $whereStr = self::vectorToWhereString($campsiteVector) . " AND template = '$tpl_file'";

                $cacheParams[$tpl_file] = array();
                $cacheParams[$tpl_file]['where'] = $whereStr;

                $queryStr = 'SELECT expired, content, status FROM Cache WHERE ' . $whereStr;

                $result = $g_ado_db->GetRow($queryStr);
                if ($result) {
                    if ($result['expired'] > time()) {
                        if ($result['status'] == 'E') {
                            $queryStr = 'UPDATE Cache SET status = "U" WHERE ' . $whereStr
                            . ' AND status = "E"';
                            $g_ado_db->executeUpdate($queryStr);
                            if ($g_ado_db->affected_rows() > 0) {
                                $cacheParams[$tpl_file]['update'] = true;
                                $return = false;
                            } else {
                                $cache_content = $result['content'];
                                $return = $result['expired'];
                            }
                        } else {
                            $cacheParams[$tpl_file]['cached'] = true;
                            $cache_content = $result['content'];
                            $return = $result['expired'];
                        }
                    } else {
                        // clear expired cache
                        $queryStr = 'DELETE FROM Cache WHERE expired <= ' . time();
                        $g_ado_db->execute($queryStr);
                        $return = false;
                    }
                }
                break;

            case 'write':
                // in case template changing the old cached templates should be updated
                if (isset($cacheParams[$tpl_file]['cached']) ) {
                    $queryStr = 'UPDATE Cache SET status = "E" WHERE template = ' . "'$tpl_file'";
                    $g_ado_db->execute($queryStr);
                    $cacheParams[$tpl_file]['update'] = true;
                }
                if ($exp_time > time() + 1) {
                    // update/insert new cached content
                    if (isset($cacheParams[$tpl_file]['update'])) {
                        $queryStr = 'UPDATE Cache SET status = null, expired = ' . $exp_time . ', '
                        . "content = '" . addslashes($cache_content) . "' WHERE "
                        . $cacheParams[$tpl_file]['where'];
                    } else {
                        $queryStr = 'INSERT INTO Cache (' . implode(',', array_keys($campsiteVector))
                        . ',template,expired,content) VALUES (';
                        foreach ($campsiteVector as $key => $value) {
                            $queryStr .= !isset($value) ? ($key == 'params' ? "''" : '0') . ','
                            : ($key == 'params' ? "'" . addslashes($value) . "'" : $value) . ',';
                        }
                        $queryStr .= "'$tpl_file',$exp_time,'" . addslashes($cache_content) . "')";
                    }
                    unset($cacheParams[$tpl_file]);
                    $g_ado_db->executeUpdate($queryStr);
                    $return = $g_ado_db->affected_rows() > 0;
                }
                break;

            case 'clean':
                $queryStr = 'DELETE FROM Cache';
                if ($tpl_file) {
                    $queryStr .= " WHERE template = '$tpl_file'";
                }
                $g_ado_db->execute($queryStr);
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
            if (isset($value)) {
                $output .= $key . ' = ' . ($key == 'params' ? "'" . addslashes($value) . "'" : $value);
            } else {
                $output .= $key . ' = ' . ($key == 'params' ? "''" : '0');
            }
            $output .= ' AND ';
        }
        $output = substr($output, 0, strlen($output) - 4);
        return $output;
    }
}
