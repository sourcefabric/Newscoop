<?
// Generic include files
require_once("$DOCUMENT_ROOT/db_connect.php");
require_once('../lib_campsite.php');
$globalfile=selectLanguageFile('..','globals');
$localfile=selectLanguageFile('.','locals');
@include ($globalfile);
@include ($localfile);
include ("../languages.php");

// Check basic access
function check_basic_access($TOL_UserId, $TOL_UserKey) {
	query ("SELECT * FROM Users WHERE Id=$TOL_UserId AND KeyId=$TOL_UserKey", 'Usr');
	$access=($NUM_ROWS != 0);
	if ($NUM_ROWS) {
		fetchRow($Usr);
		query ("SELECT * FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'XPerm');
		if ($NUM_ROWS){
			fetchRow($XPerm);
		}
		else {
			//added lately; a non-admin can enter the administration area;
			// he exists but doesn't have ANY rights
			$access = 0;
		}
		$xpermrows= $NUM_ROWS;
	}
	else {
		query ("SELECT * FROM UserPerm WHERE 1=0", 'XPerm');
	}
	return $access;
} // fn check_basic_access

?>