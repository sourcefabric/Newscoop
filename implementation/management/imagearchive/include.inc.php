<?php
define('CAMPSITE_IMAGEARCHIVE_DIR', '/priv/imagearchive/');
define('CAMPSITE_IMAGEARCHIVE_IMAGES_PER_PAGE', 8);

function orE($p_input) {
	if (empty($p_input)) {
		return 'unknown';
	} else {
		return $p_input;
	}
} // fn orE

?>