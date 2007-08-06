<?php
/**
 * @package Campsite
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/include/adodb/adodb.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/SystemPref.php');

/**
 * A simple class for Database Replication.
 * TODO: Add more replication functions.
 *
 * @package Campsite
 */
class DbReplication {
    /**
     * Remote (Online) db server connection details.
     */
    var $m_rDbName = null;
    var $m_rDbHost = null;
    var $m_rDbUser = null;
    var $m_rDbPass = null;

    /**
     * Constructor
     *
     * @return void
     */
    function DbReplication() {}

    /**
     * Try to connect the resource based on supplied parameter.
     *
     * @param string (optional)
     *      Host/Server alias [online | local]
     *
     * @return boolean|PEAR_Error
     *
     */
    function connect($host = null)
    {
        global $Campsite;
        global $g_ado_db;

        if ($host == 'local') {
            if (isset($g_ado_db)
                && $g_ado_db->host == $Campsite['DATABASE_SERVER_ADDRESS']) {
                return true;
            } else {
                $g_ado_db = ADONewConnection('mysql');
                $g_ado_db->SetFetchMode(ADODB_FETCH_ASSOC);
                if ($g_ado_db->Connect($Campsite['DATABASE_SERVER_ADDRESS'],
                            $Campsite['DATABASE_USER'],
                            $Campsite['DATABASE_PASSWORD'],
                            $Campsite['DATABASE_NAME'])) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        $g_ado_db_tmp = $g_ado_db;

           $this->m_rDbName = $Campsite['DATABASE_NAME'];
        $this->m_rDbHost = SystemPref::Get('DBReplicationHost')
                           . ':'
                           . SystemPref::Get('DBReplicationPort');
        $this->m_rDbUser = SystemPref::Get('DBReplicationUser');
        $this->m_rDbPass = SystemPref::Get('DBReplicationPass');

        if (isset($g_ado_db) && $g_ado_db->host == $this->m_rDbHost) {
            return true;
        }
        if ($this->m_rDbHost == ':'
                || is_null($this->m_rDbUser)
                || is_null($this->m_rDbPass)) {
            return false;
        }
        $g_ado_db = ADONewConnection('mysql');
        $g_ado_db->SetFetchMode(ADODB_FETCH_ASSOC);
        if ($g_ado_db->Connect($this->m_rDbHost,
                    $this->m_rDbUser,
                    $this->m_rDbPass,
                    $this->m_rDbName) == false) {
            $g_ado_db = $g_ado_db_tmp;
            return false;
        } else {
            return true;
        }
    } // fn connect

} // class DbReplication

?>
