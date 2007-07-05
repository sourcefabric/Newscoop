<?php

require_once(CS_PATH_DOCROOT.DIR_SEP.'install_conf.php');

define('CS_PATH_SITE', CS_PATH_DOCROOT);
define('CS_PATH_CONFIG', CS_PATH_SITE);
define('CS_PATH_CLASSES', CS_PATH_SITE.DIR_SEP.'classes');
define('CS_PATH_INCLUDES', CS_PATH_SITE.DIR_SEP.'include');
define('CS_PATH_BASE', $Campsite['CAMPSITE_DIR']);
define('CS_PATH_SMARTY', CS_PATH_BASE.DIR_SEP.'var'.DIR_SEP.'smarty');
define('CS_PATH_SMARTY_PLUGINS', CS_PATH_SMARTY.DIR_SEP.'camp_plugins');
define('CS_PATH_SMARTY_TEMPLATES', CS_PATH_SMARTY.DIR_SEP.'templates');
define('CS_PATH_SMARTY_DEFAULT_THEME', CS_PATH_SMARTY_TEMPLATES.DIR_SEP.'default');

?>