<?php

if(!defined('DIR_SEP')) {
    define('DIR_SEP', DIRECTORY_SEPARATOR);
}

require_once($_SERVER['DOCUMENT_ROOT'].DIR_SEP.'install_conf.php');

define('CS_PATH_SITE', $_SERVER['DOCUMENT_ROOT']);
define('CS_PATH_CONFIG', CS_PATH_SITE);
define('CS_PATH_CLASSES', CS_PATH_SITE.DIR_SEP.'classes');
define('CS_PATH_INCLUDES', CS_PATH_SITE.DIR_SEP.'include');
define('CS_PATH_BASE', $Campsite['CAMPSITE_DIR']);
define('CS_PATH_SMARTY', CS_PATH_BASE.DIR_SEP.'var'.DIR_SEP.'smarty');
define('CS_PATH_SMARTY_PLUGINS', CS_PATH_SMARTY.DIR_SEP.'camp_plugins');
define('CS_PATH_SMARTY_TEMPLATES', CS_PATH_SMARTY.DIR_SEP.'templates');
define('CS_PATH_SMARTY_DEFAULT_THEME', CS_PATH_SMARTY_TEMPLATES.DIR_SEP.'default');

define('INVALID_OBJECT_STRING', 'invalid object');
define('INVALID_PROPERTY_STRING', 'invalid property');
define('INVALID_VALUE_STRING', 'invalid value');
define('OF_PROPERTY_STRING', 'of property');
define('OF_OBJECT_STRING', 'of object');

?>