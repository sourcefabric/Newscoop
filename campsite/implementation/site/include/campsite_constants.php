<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

// Defines directory separator sign
if(!defined('DIR_SEP')) {
    define('DIR_SEP', DIRECTORY_SEPARATOR);
}

$scheme = (empty($_SERVER['HTTPS'])) ? 'http://' : 'https://';
// Campsite paths (used by template engine)
define('CS_PATH_BASE_URL', $scheme.$_SERVER['HTTP_HOST'].'/');
define('CS_PATH_SITE', $_SERVER['DOCUMENT_ROOT']);
define('CS_PATH_CONFIG', CS_PATH_SITE.DIR_SEP.'conf');
define('CS_PATH_CLASSES', CS_PATH_SITE.DIR_SEP.'classes');
define('CS_PATH_INCLUDES', CS_PATH_SITE.DIR_SEP.'include');
define('CS_PATH_PEAR_LOCAL', CS_PATH_INCLUDES.DIR_SEP.'pear');
define('CS_PATH_SMARTY', CS_PATH_INCLUDES.DIR_SEP.'smarty');
define('CS_PATH_SMARTY_TEMPLATES', 'templates');
define('CS_PATH_SMARTY_SYS_TEMPLATES', 'sys-templates');
define('CS_PATH_PLUGINS', CS_PATH_SITE.DIR_SEP.'plugins');

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