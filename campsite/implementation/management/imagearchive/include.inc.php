<?php
require_once($_SERVER['DOCUMENT_ROOT']."/configuration.php");
define('CAMPSITE_IMAGEARCHIVE_DIR', "/$ADMIN_DIR/imagearchive/");
define('CAMPSITE_IMAGEARCHIVE_IMAGES_PER_PAGE', 8);

function orE($p_input) {
	if (empty($p_input)) {
		return 'unknown';
	} else {
		return $p_input;
	}
} // fn orE

?>