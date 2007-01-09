<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/XR_CcClient.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');

$sessid = null;
$sessid = camp_session_get('cc_sessid', '');
if (!empty($sessid)) {
    $xrc =& XR_CcClient::Factory($mdefs);
    if (!PEAR::isError($xrc) && !PEAR::isError($xrc->ping($sessid))) {
        $xrc->xr_logout($sessid);
    }
}
$LiveUser->logout();
// Delete the cookies
setcookie("LoginUserId", "", time() - 86400);
setcookie("LoginUserKey", "", time() - 86400);
session_destroy();

// Unlock all articles that are locked by this user
Article::UnlockByUser($g_user->getUserId());

camp_html_goto_page("/$ADMIN/login.php");
?>
