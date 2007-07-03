<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/include/adodb/adodb.inc.php');


/**
 * @package Campsite
 */
final class CampDatabase {
    /**
     * Holds instance of the class
     *
     * @var object
     */
    private static $m_instance = null;

    /**
     * Holds the database (adodb) instance
     *
     * @var object
     */
    var $m_db = null;


    /**
     * Class constructor
     */
    final public function __construct()
    {
        // gets the config object from the main class
        $config = CampSite::getConfig();
        // sets the new connection resource
        $this->m_db = ADONewConnection($config->getVar('db_type'));
        $this->m_db->SetFetchMode(ADODB_FETCH_ASSOC);
        $this->m_db->Connect($config->getVar('db_host'),
                             $config->getVar('db_user'),
                             $config->getVar('db_pass'),
                             $config->getVar('db_name'));
    } // fn __construct


    /**
     * Builds an instance object of this class only if there is no one.
     *
     * @return object
     *    m_instance A CampConfig instance
     */
    public static function singleton()
    {
        if (!isset(self::$m_instance)) {
            self::$m_instance = new CampDatabase();
        }
        return self::$m_instance;
    } // fn singleton

} // fn class CampDatabase

?>