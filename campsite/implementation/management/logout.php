<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');

list($access, $User) = check_basic_access($_REQUEST);

// Delete the cookies
setcookie("TOL_UserId", "", time() - 86400);
setcookie("TOL_UserKey", "", time() - 86400);

// Unlock all articles that are locked by this user
if ($access) {
	Article::UnlockByUser($User->getId());
}

// added by sebastian
if (function_exists ("incModFile")) {
	incModFile ();
}
header("Location: /$ADMIN/login.php");
exit;
?>