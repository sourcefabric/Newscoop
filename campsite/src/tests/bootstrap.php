<?php

define('WWW_DIR', dirname(__FILE__) . '/../');
define('LIBS_DIR', WWW_DIR . '/admin-files/libs');
$GLOBALS['g_campsiteDir'] = WWW_DIR;

require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'include'
.DIRECTORY_SEPARATOR.'campsite_constants.php');
require_once(CS_PATH_CONFIG.DIR_SEP.'install_conf.php');
require_once(CS_PATH_SITE.DIR_SEP.'include'.DIR_SEP.'campsite_init.php');
require_once(CS_PATH_CONFIG.DIR_SEP.'liveuser_configuration.php');
require_once(CS_PATH_SITE.DIR_SEP.'classes'.DIR_SEP.'CampTemplateCache.php');

set_include_path(get_include_path() . PATH_SEPARATOR . CS_PATH_PEAR_LOCAL);

require_once WWW_DIR . '/db_connect.php';
