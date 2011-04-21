<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

// Defines directory separator sign
if(!defined('DIR_SEP')) {
    define('DIR_SEP', DIRECTORY_SEPARATOR);
}
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = null;
}
$scheme = (!isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS'])) ? 'http://' : 'https://';
// Campsite paths (used by template engine)
define('CS_PATH_BASE_URL', $scheme.$_SERVER['HTTP_HOST'].'/');
define('CS_PATH_SITE', dirname(dirname(__FILE__)));
define('CS_PATH_CONFIG', CS_PATH_SITE.DIR_SEP.'conf');
define('CS_PATH_CLASSES', CS_PATH_SITE.DIR_SEP.'classes');
define('CS_PATH_INCLUDES', CS_PATH_SITE.DIR_SEP.'include');
define('CS_PATH_PEAR_LOCAL', CS_PATH_INCLUDES);
define('CS_PATH_SMARTY', CS_PATH_INCLUDES.DIR_SEP.'smarty');
define('CS_TEMPLATES_DIR', 'templates');
define('CS_DEMO_ASSETS_DIR', 'sample_data');
define('CS_PATH_TEMPLATES', CS_PATH_SITE.DIR_SEP.CS_TEMPLATES_DIR);
define('CS_PATH_DEMO_ASSETS', CS_PATH_SITE.DIR_SEP.CS_TEMPLATES_DIR);
define('CS_SYS_TEMPLATES_DIR', 'system_templates');
define('CS_PATH_SYS_TEMPLATES', CS_PATH_SITE.DIR_SEP.CS_TEMPLATES_DIR.DIR_SEP.CS_SYS_TEMPLATES_DIR);
define('CS_PLUGINS_DIR', 'plugins');
define('CS_PATH_PLUGINS', CS_PATH_SITE.DIR_SEP.CS_PLUGINS_DIR);
define('CS_INSTALL_DIR', CS_PATH_SITE.DIR_SEP.'install');

$tmpDir = @ini_get('upload_tmp_dir') ? @ini_get('upload_tmp_dir') : '/tmp';
define('CS_TMP_DIR', $tmpDir);
define('CS_TMP_TPL_DIR', CS_TMP_DIR.DIR_SEP.'mtupload');

// Campsite exception strings
define('INVALID_OBJECT_STRING', 'invalid object');
define('INVALID_PROPERTY_STRING', 'invalid property');
define('INVALID_VALUE_STRING', 'invalid value');
define('OF_PROPERTY_STRING', 'of property');
define('OF_OBJECT_STRING', 'of object');

// Campsite error codes
define('CAMP_SUCCESS', 1);
define('CAMP_ERROR', 0);
define('CAMP_ERROR_MKDIR', -100);
define('CAMP_ERROR_RMDIR', -200);
define('CAMP_ERROR_WRITE_DIR', -300);
define('CAMP_ERROR_READ_DIR', -400);
define('CAMP_ERROR_CREATE_FILE', -500);
define('CAMP_ERROR_READ_FILE', -600);
define('CAMP_ERROR_WRITE_FILE', -700);
define('CAMP_ERROR_DELETE_FILE', -800);
define('CAMP_ERROR_UPLOAD_FILE', -900);

?>
