<?php
global $ADMIN, $ADMIN_DIR;

//define('_DB_USER_',         'root');
//define('_DB_PWD_',          '');
//define('_DB_HOST_',         'localhost');
//define('_DB_NAME_',         'cs_campware');

define('LOCALIZER_MAINTAINANCE', true);
define('LOCALIZER_DEFAULT_LANG', 'en_English');
define('LOCALIZER_PREFIX', 'locals');
define('LOCALIZER_PREFIX_GLOBAL', 'globals');
define('LOCALIZER_PREFIX_HIDE', '.');
define('LOCALIZER_LANG_BASE', 'campsite');
define('LOCALIZER_DENY_HTML', false);
define('LOCALIZER_ENCODING', 'UTF-8');

define('LOCALIZER_PANEL_FRAME', '');
define('LOCALIZER_MENU_FRAME', '');
define('LOCALIZER_PANEL_SCRIPT', 'index.php');
define('LOCALIZER_MENU_SCRIPT', 'menu.php');
define('LOCALIZER_ICONS_DIR', "/$ADMIN/img/icon");
define('LOCALIZER_START_DIR', '..');
define('LOCALIZER_BASE_DIR', $_SERVER['DOCUMENT_ROOT']);
define('LOCALIZER_ADMIN_DIR', '/admin-files');

define('LOCALIZER_INPUT_SIZE', 70);
?>
