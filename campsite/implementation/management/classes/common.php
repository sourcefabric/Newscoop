<?php
/**
 * Load include files.
 */
function load_common_include_files() {
	require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/priv/lib_campsite.php");
	$globalfile=selectLanguageFile($_SERVER['DOCUMENT_ROOT']."/priv",'globals');
	$localfile=selectLanguageFile('.','locals');
	@include ($globalfile);
	@include ($localfile);
	include ($_SERVER['DOCUMENT_ROOT']."/priv/languages.php");
} // fn load_common_include_files

/**
 * Check if user has access to this screen.
 */
function check_basic_access($request) {
	$access = false;
	$XPerm = array();
	$user = array();
	
	// Check for required info.
    if (!isset($request['TOL_UserId']) || !isset($request["TOL_UserKey"])) {
    	return array($access, $user, $XPerm);
    }
    
    // Check if user exists in the table.
    $queryStr = "SELECT * FROM Users "
    			." WHERE Id=".$request["TOL_UserId"]
    			." AND KeyId=".$request["TOL_UserKey"];
    $query = mysql_query($queryStr);
    if ($query && (mysql_num_rows($query) > 0)) {
    	// User exists.
		$access = true;
		$user = mysql_fetch_array($query,MYSQL_ASSOC);

		// Fetch the user's permissions.
		$queryStr = "SELECT * FROM UserPerm "
					." WHERE IdUser=".$user['Id'];
		$query2 = mysql_query($queryStr);
		if ($query2 && (mysql_num_rows($query2) > 0)) {
			$tmpXPerm = mysql_fetch_array($query2, MYSQL_ASSOC);
			// Make XPerm a boolean array.
			$XPerm = array();
			foreach ($tmpXPerm as $key => $value) {
				$XPerm[$key] = ($value == 'Y');
			}
		}
		else {
			// A non-admin can enter the administration area;
			// they exist but do not have ANY rights.
			$access = 0;
		}
	}
	return array($access, $user, $XPerm);
} // fn check_basic_access

?>