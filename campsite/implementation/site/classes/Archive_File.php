<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Archive_FileBase.php');


/**
 * @package Campsite
 */
class Archive_File
{
    /**
     * Class constructor
     */
    public function __construct() {}


    /**
     * @param string p_gunId
     */
    public static function Get($p_gunId)
    {
        $archiveFile = new Archive_FileBase($p_gunId);
        if (!$archiveFile->exists()) {
            return false;
        }

        $className = 'Archive_'.ucwords($archiveFile->getType()).'File';
        require_once($GLOBALS['g_campsiteDir'].'/classes/'.$className.'.php');
        $file = new $className($p_gunId);

        return $file;
    }

} // class Archive_File

?>