<?php
/**
 * Load include files.
 */
function load_common_include_files($current_dir) {
	global $ADMIN_DIR;
	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/config.php');
	require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/lib_campsite.php");
	$globalfile = selectLanguageFile($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR",'globals');
	$localfile = selectLanguageFile($_SERVER['DOCUMENT_ROOT']."/$current_dir",'locals');
	require_once($globalfile);
	require_once($localfile);
	require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/languages.php");
} // fn load_common_include_files

/**
 * Check if user has access to this screen.
 *
 * @return array
 *
 */
function check_basic_access($request) {
	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
	$access = false;
	$XPerm = array();
	$user = array();
	
	// Check for required info.
	if (!isset($request['TOL_UserId']) || !isset($request['TOL_UserKey'])) {
		return array($access, $user, $XPerm);
	}
    
	// Check if user exists in the table.
	$queryStr = 'SELECT * FROM Users '
				.' WHERE Id='.$request['TOL_UserId']
				.' AND KeyId='.$request['TOL_UserKey'];
	$query = mysql_query($queryStr);
	if ($query && (mysql_num_rows($query) > 0)) {
		// User exists.
		$access = true;
		$userRow = mysql_fetch_array($query,MYSQL_ASSOC);
		$user =& new User();
		$user->fetch($userRow);
		if (!$user->isAdmin()) {
			// A non-admin can enter the administration area;
			// they exist but do not have ANY rights.
			$access = 0;
		}
	}
	return array($access, $user);
} // fn check_basic_access

?>