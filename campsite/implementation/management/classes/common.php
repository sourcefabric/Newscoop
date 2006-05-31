<?php
/**
 * Load include files.
 * @param string $p_currentDir
 * @return void
 */
function load_common_include_files($p_currentDir)
{
	global $ADMIN_DIR;
	require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
	require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/lib_campsite.php");
	camp_load_language('globals');
	camp_load_language("$p_currentDir");
} // fn load_common_include_files


/**
 * Check if user has access to the admin.
 * @param array $p_request
 * @return array
 */
function check_basic_access($p_request)
{
	global $ADMIN;
	global $g_ado_db;
	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
	$access = false;
	$XPerm = array();
	$user = array();

	// Check for required info.
	if (!isset($p_request['LoginUserId']) || !isset($p_request['LoginUserKey'])
	 	|| !is_numeric($p_request['LoginUserId']) || !is_numeric($p_request['LoginUserKey'])) {
		return array($access, $user, $XPerm);
	}

	// Check if user exists in the table.
	$queryStr = 'SELECT * FROM Users '
				.' WHERE Id='.$p_request['LoginUserId']
				." AND Reader='N'";
	$row = $g_ado_db->GetRow($queryStr);
	if ($row && $row['KeyId'] == $p_request['LoginUserKey']) {
		// User exists.
		$access = true;
		$user =& new User();
		$user->fetch($row);
	}
	return array($access, $user);
} // fn check_basic_access

?>