<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2009 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

global $Campsite;

$GLOBALS['g_campsiteDir'] = dirname(dirname(__FILE__));

require_once($GLOBALS['g_campsiteDir'].'/include/campsite_constants.php');
// sets the PEAR local directory
set_include_path(CS_PATH_PEAR_LOCAL.PATH_SEPARATOR.get_include_path());

require_once($GLOBALS['g_campsiteDir'].'/classes/SystemPref.php');


/** System settings **/
$Campsite['campsite']['url_default_type'] = 2;
$Campsite['campsite']['secret_key'] = SystemPref::Get('SiteSecretKey');
$Campsite['campsite']['session_lifetime'] = SystemPref::Get('SiteSessionLifeTime');

/** General settings **/
$Campsite['site']['online'] = SystemPref::Get('SiteOnline');
$Campsite['site']['title'] = SystemPref::Get('SiteTitle');
$Campsite['site']['keywords'] = SystemPref::Get('SiteMetaKeywords');
$Campsite['site']['description'] = SystemPref::Get('SiteMetaDescription');
$Campsite['site']['charset'] = 'utf-8';
$Campsite['site']['help_url'] = 'http://code.campware.org/manuals/campsite/3.3/';
$Campsite['site']['about_url'] = 'http://www.campware.org/en/camp/campsite_news/';
$Campsite['site']['email'] = 'campsite-support@lists.campware.org';

$Campsite['campware']['url'] = 'http://www.campware.org/';

/** Locale settings **/
$Campsite['locale']['lang_id'] = 1;
$Campsite['locale']['lang_iso'] = 'en-US';
$Campsite['locale']['lang_code'] = 'en';
$Campsite['locale']['lang_name'] = 'english';

/** Cache settings **/
$Campsite['cache']['enabled'] = true;
$Campsite['cache']['expiration_time'] = 900;
$Campsite['cache']['path'] = null;

/** Smarty settings **/
$Campsite['smarty']['caching'] = false;
$Campsite['smarty']['debugging'] = false;
$Campsite['smarty']['force_compile'] = false;
$Campsite['smarty']['compile_check'] = true;
$Campsite['smarty']['use_subdirs'] = false;
$Campsite['smarty']['left_delimeter'] = '#{#{';
$Campsite['smarty']['right_delimeter'] = '#}#}';

/** File Archive Storage Server settings **/
$Campsite['xmlrpc_storage']['path'] = '/mma/xmlrpc/';
$Campsite['xmlrpc_storage']['file'] = 'xrLocStor.php';

/** Mailer settings **/
$Campsite['smtp']['host'] = SystemPref::Get('SMTPHost');
$Campsite['smtp']['port'] = SystemPref::Get('SMTPPort');
$Campsite['smtp']['default_host'] = 'localhost';
$Campsite['smtp']['default_port'] = 25;

/** Backward compatibility **/
$ADMIN_DIR = "admin-files";

require_once($GLOBALS['g_campsiteDir'].'/conf/database_conf.php');
require_once($GLOBALS['g_campsiteDir'].'/conf/install_conf.php');


/**
 * Try to autoload class definitions before failing.
 * This makes the Campsite class definition file inclusion optional.
 *
 * @param string $p_className
 */
function __autoload($p_className)
{
    global $ADMIN, $ADMIN_DIR;
    require_once($GLOBALS['g_campsiteDir'].'/classes/CampPlugin.php');

    static $classDirectories = array('classes',
                              'template_engine/classes',
                              'template_engine/metaclasses');
                      
    if (!is_string($p_className)) {
        return;
    }

    foreach ($classDirectories as $dirName) {
        $fileName = $GLOBALS['g_campsiteDir']."/$dirName/$p_className.php";
        if (file_exists($fileName)) {
            require_once($fileName);
            return;
        }
    }
    
    $basePaths = array();
    foreach (CampPlugin::GetEnabled() as $CampPlugin) {
        $basePaths[] = $CampPlugin->getBasePath();  
    }
    foreach ($basePaths as $basePath) {                       
        foreach ($classDirectories as $dirName) {
            $fileName = $GLOBALS['g_campsiteDir']."/$basePath/$dirName/$p_className.php";
            if (file_exists($fileName)) {
                require_once($fileName);
                return;
            }
        }
    }
}

?>