<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files($ADMIN_DIR);
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/login.php");
	return;
}
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/home.php");
?>
