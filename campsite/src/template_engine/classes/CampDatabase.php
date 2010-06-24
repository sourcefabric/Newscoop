<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/include/adodb/adodb.inc.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampSite.php');


/**
 * Class CampDatabase
 */
final class CampDatabase
{
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
        $config = CampSite::GetConfigInstance();

        // sets the new connection resource
        $this->m_db = ADONewConnection($config->getSetting('db.type'));
        $this->m_db->SetFetchMode(ADODB_FETCH_ASSOC);
        $this->m_db->Connect($config->getSetting('db.host'),
                             $config->getSetting('db.user'),
                             $config->getSetting('db.pass'),
                             $config->getSetting('db.name'));
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