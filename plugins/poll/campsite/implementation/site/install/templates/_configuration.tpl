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

global $Campsite;

// sets the PEAR local directory
set_include_path(CS_PATH_PEAR_LOCAL.PATH_SEPARATOR.get_include_path());

/**
 * Includes
 *
 * We indirectly reference the DOCUMENT_ROOT so we can enable
 * scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
 * is not defined in these cases.
 */
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];


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
$Campsite['site']['help_url'] = 'http://code.campware.org/manuals/campsite/2.6/';
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
$Campsite['smarty']['force_compile'] = true;
$Campsite['smarty']['compile_check'] = false;
$Campsite['smarty']['use_subdirs'] = false;
$Campsite['smarty']['left_delimeter'] = '#{#{';
$Campsite['smarty']['right_delimeter'] = '#}#}';

/** Mailer settings **/
$Campsite['smtp']['host'] = SystemPref::Get('SMTPHost');
$Campsite['smtp']['port'] = SystemPref::Get('SMTPPort');
$Campsite['smtp']['default_host'] = 'localhost';
$Campsite['smtp']['default_port'] = 25;

/** Backward compatibility **/
$ADMIN_DIR = "admin-files";
$ADMIN = "admin";

/** Plugins **/
$PLUGIN_DIR = "plugins";

require_once($g_documentRoot.'/conf/database_conf.php');
require_once($g_documentRoot.'/conf/install_conf.php');


/**
 * Try to autoload class definitions before failing. This makes the Campsite
 * class definition file inclusion optional.
 *
 * @param string $p_className
 */
function __autoload($p_className)
{
    global $Campsite, $ADMIN_DIR, $ADMIN, $g_documentRoot;
    
    if (!is_string($p_className)) {
        return;
    }

    if ($fileName = camp_find_class($p_className)) {
        include_once($fileName);
        return;   
    }
}

/**
 * Try to find class definations.
 * Function lookup fpr standard campsite folders, 
 * plus plugin folders which are found by reading the directory.
 *
 * @param string $p_className
 * @param string $p_type optional name of subfolder to search for class defination
 */
function camp_find_class($p_className, $p_type = null)
{
    global $Campsite, $ADMIN_DIR, $ADMIN, $g_documentRoot, $PLUGIN_DIR;
    static $pluginDirectories;

    if (!is_string($p_className)) {
        return;
    }

    if (!is_array($pluginDirectories)) {
        $pluginDirectories = array();
        
        if ($handle = @opendir("$g_documentRoot/$PLUGIN_DIR")) { 
        
            while ($dir = readdir($handle)) { 
                if (is_dir("$g_documentRoot/$PLUGIN_DIR/$dir") && $dir != "." && $dir != ".." && $dir != ".svn") { 
                    $pluginDirectories[] = $dir; 
                } 
            }
            closedir($handle);
        } 
    }
    
    if (is_null($p_type)) {
        $classDirectories = array('classes',
                                  'template_engine',
                                  'template_engine/classes',
                                  'template_engine/metaclasses');
                                  
        foreach ($pluginDirectories as $dir) { 
            $classDirectories[] = "$PLUGIN_DIR/$dir/classes"; 
            $classDirectories[] = "$PLUGIN_DIR/$dir/template_engine";
            $classDirectories[] = "$PLUGIN_DIR/$dir/template_engine/classes";
            $classDirectories[] = "$PLUGIN_DIR/$dir/template_engine/metaclasses";
        };

    } else { 
         $classDirectories = array();
         $classDirectories[] = $p_type;
              
        foreach ($pluginDirectories as $dir) {
            $classDirectories[] = "$PLUGIN_DIR/$dir/$p_type"; 
        } 
    }
    
    foreach ($classDirectories as $dirName) {
        $fileName = "$g_documentRoot/$dirName/$p_className.php";
        if (file_exists($fileName)) {
            return $fileName;
        }
    }      
    return false;
}

?>