<?php
$cs_dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$GLOBALS['g_campsiteDir'] = $cs_dir;
require_once($cs_dir.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'campsite_constants.php');
require_once(CS_PATH_SITE.DIR_SEP.'include'.DIR_SEP.'campsite_init.php');
require(CS_PATH_CONFIG.DIR_SEP.'liveuser_configuration.php');

$LiveUserAdmin->addRight(array('area_id' => 0, 'right_define_name' => 'ManageBackup', 'has_implied' => 1));
?>