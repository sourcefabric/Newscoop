<?php
// Delete the cookies
setcookie("TOL_UserId", "", time() - 3600);
setcookie("TOL_UserKey", "", time() - 3600);

## added by sebastian
if (function_exists ("incModFile")) {
	incModFile ();
}
header("Location: /$ADMIN/login.php");
exit;
?>