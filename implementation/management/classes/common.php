<?php
/**
 * Load include files.
 */
function load_common_include_files() {
	require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/priv/lib_campsite.php');
	$globalfile=selectLanguageFile($_SERVER['DOCUMENT_ROOT'].'/priv','globals');
	$localfile=selectLanguageFile('.','locals');
	@include ($globalfile);
	@include ($localfile);
	include ($_SERVER['DOCUMENT_ROOT'].'/priv/languages.php');
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