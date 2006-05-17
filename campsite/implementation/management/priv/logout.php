<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');

list($access, $User) = check_basic_access($_REQUEST);

// Delete the cookies
setcookie("LoginUserId", "", time() - 86400);
setcookie("LoginUserKey", "", time() - 86400);
session_destroy();

// Unlock all articles that are locked by this user
if ($access) {
	Article::UnlockByUser($User->getUserId());
}

header("Location: /$ADMIN/login.php");
exit;
?>