<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files('home');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

list($validUser, $user) = User::Login($_REQUEST["f_user_name"], $_REQUEST["f_password"]);

$f_login_language = isset($_REQUEST["f_login_language"])?$_REQUEST["f_login_language"]:"";

if ($f_login_language == "") {
	$f_login_language='en';
}
if ($validUser) {
	if (function_exists ("incModFile")) {
		incModFile ();
	}
	setcookie("LoginUserId", $user->getUserId());
	setcookie("LoginUserKey", $user->getKeyId());
	setcookie("TOL_Language", $f_login_language);

    // Most of the time its easier to use the language ID
    // rather than the Language Code.
	$language = Language::GetLanguages(null, $f_login_language);
	$language = array_pop($language);
	camp_session_get("LoginLanguageId", $language->getLanguageId());

	Article::UnlockByUser($user->getUserId());
	header("Location: /$ADMIN/index.php");
	exit;
}
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
   	<LINK rel="stylesheet" type="text/css" href="<?php $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("Login failed"); ?></TITLE>
</HEAD>

<BODY>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" align="center">
<TR>
	<TD align="center" style="padding-top: 50px;">
		<IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/sign_big.gif" BORDER="0">
	</TD>
</TR>
</TABLE>
<table align="center" style="margin-top: 25px; 	background-color: #d5e2ee; border: 1px solid #8baed1;" cellpadding="6">
<tr>
	<td align="center">
		<DIV STYLE="font-size: 12pt"><B><?php  putGS("Login failed"); ?></B></DIV><br>
		<?php  putGS('Please make sure that you typed the correct user name and password.'); ?><br>
		<?php  putGS('If your problem persists please contact the site administrator $1','');?></A>
		<p>
		<A HREF="/<?php echo $ADMIN; ?>/login.php" ><B><?php  putGS("Login");  ?></B></A>
	</td>
</tr>
</table>
</body>
</html>
