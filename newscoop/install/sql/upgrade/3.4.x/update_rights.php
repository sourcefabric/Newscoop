<?php
//echo "\n<br>update_rights: ";
//var_dump(__FILE__);
//echo "<br>\n";
//if (!$GLOBALS['g_campsiteDir']) {
$cs_dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$GLOBALS['g_campsiteDir'] = $cs_dir;
//}
//$cs_dir = $GLOBALS['g_campsiteDir'];

require_once($cs_dir.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'campsite_constants.php');
require_once(CS_PATH_SITE.DIR_SEP.'include'.DIR_SEP.'campsite_init.php');
//require_once(CS_PATH_CONFIG.DIR_SEP.'liveuser_configuration.php');
require(CS_PATH_CONFIG.DIR_SEP.'liveuser_configuration.php');

//echo "\n<br>liveuseradmin: ";
//var_dump($LiveUserAdmin);
//echo "<br>\n";

$rightId = $LiveUserAdmin->addRight(array('area_id' => 0, 'right_define_name' => 'EditAuthors', 'has_implied' => 1));
if (!is_numeric($rightId)) {
    $filter = array('filters' => array('right_define_name' => 'EditAuthors'));
    $rightArray = $LiveUserAdmin->perm->getRights($filter);
    if (is_array($rightArray)) {
        $rightId = $rightArray[0]['right_id'];
    }
}
$data = array(
    'right_id' => $rightId,
    'group_id' => 1
);
$LiveUserAdmin->perm->grantGroupRight($data);
?>
