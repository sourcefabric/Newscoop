<?php
$cs_dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$GLOBALS['g_campsiteDir'] = $cs_dir;
require_once($cs_dir.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'campsite_constants.php');
require_once(CS_PATH_SITE.DIR_SEP.'include'.DIR_SEP.'campsite_init.php');

$import_runner = $cs_dir . DIR_SEP . 'install' . DIR_SEP . 'scripts' . DIR_SEP . 'SQLImporting.php';
require_once($import_runner);

importCheckPointPolygon($g_ado_db, $checkpp_sql);

?>
