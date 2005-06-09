<?php
/**
 * Load include files.
 * @param string p_currentDir
 * @return void
 */
function load_common_include_files($p_currentDir) 
{
	global $ADMIN_DIR;
	require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
	require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/lib_campsite.php");
	selectLanguageFile($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR",'globals');
	selectLanguageFile($_SERVER['DOCUMENT_ROOT']."/$p_currentDir",'locals');
	require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/languages.php");
} // fn load_common_include_files


/**
 * Check if user has access to this screen.
 * @param array p_request
 * @return array
 */
function check_basic_access($p_request, $p_exit = true)
{
	global $ADMIN;
	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
	$access = false;
	$XPerm = array();
	$user = array();
	
	// Check for required info.
	if (!isset($p_request['TOL_UserId']) || !isset($p_request['TOL_UserKey'])
	 	|| !is_numeric($p_request['TOL_UserId']) || !is_numeric($p_request['TOL_UserKey'])) {
		return array($access, $user, $XPerm);
	}
    
	// Check if user exists in the table.
	$queryStr = 'SELECT * FROM Users '
				.' WHERE Id='.$p_request['TOL_UserId'];
				//.' AND KeyId='.$p_request['TOL_UserKey'];
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
//			$access = 0;
//			if ($p_exit) {
//				header("Location: /$ADMIN/logout.php");
//				exit(0);
//			}

		}
	}
	return array($access, $user);
} // fn check_basic_access

?>