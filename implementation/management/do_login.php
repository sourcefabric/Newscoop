<?php  
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files($ADMIN_DIR);
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
list($validUser, $user) = User::Login($_REQUEST["UserName"], $_REQUEST["UserPassword"]);
$selectLanguage = isset($_REQUEST["selectlanguage"])?$_REQUEST["selectlanguage"]:"";
if ($selectlanguage == "") {
	$selectlanguage='en';
}
if ($validUser) {
	if (function_exists ("incModFile")) {
		incModFile ();
	}
	setcookie("TOL_UserId", $user->getId());
	setcookie("TOL_UserKey", $user->getKeyId());
	setcookie("TOL_Language", $selectLanguage);
	header("Location: /$ADMIN/index.php");
	exit;
}
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
   	<LINK rel="stylesheet" type="text/css" href="<?php $Campsite["website_url"] ?>/css/admin_stylesheet.css">
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("Login failed"); ?></TITLE>
</HEAD>

<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
<TR>
	<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/<?php echo $ADMIN; ?>/img/sign_big.gif" BORDER="0"></TD>
	<TD>
	    <DIV STYLE="font-size: 12pt"><B><?php  putGS("Login failed"); ?></B></DIV>
	    <HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN=RIGHT>
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
			<TD><A HREF="/<?php echo $ADMIN; ?>/login.php" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Login"); ?>"></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/login.php" ><B><?php  putGS("Login");  ?></B></A></TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>

<BLOCKQUOTE>
	<LI><?php  putGS('Login failed'); ?></LI>
	<LI><?php  putGS('Pease make sure that you typed the correct user name and password.'); ?></LI>
	<LI><?php  putGS('If your problem persists please contact the site administrator $1','<A HREF="mailto:'.encURL($SERVER_ADMIN).'">'.encHTML($SERVER_ADMIN) );?></A></LI>
</BLOCKQUOTE>

<?php CampsiteInterface::CopyrightNotice(); ?>