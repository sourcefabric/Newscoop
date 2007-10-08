<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

/**
 * Includes
 *
 * We indirectly reference the DOCUMENT_ROOT so we can enable
 * scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
 * is not defined in these cases.
 */
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/include/adodb/adodb.inc.php');
require_once($g_documentRoot.'/template_engine/classes/CampSite.php');


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
        $config = CampSite::GetConfig();

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