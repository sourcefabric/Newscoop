<?php  
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files();
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
list($validUser, $user) = User::login($_REQUEST["UserName"], $_REQUEST["UserPassword"]);
$selectLanguage = isset($_REQUEST["selectlanguage"])?$_REQUEST["selectlanguage"]:"";
if ($selectlanguage == "") {
	$selectlanguage='en';
}
if ($validUser) {
	if (function_exists ("incModFile")) {
		incModFile ();
	}
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>

<HEAD>
   	<LINK rel="stylesheet" type="text/css" href="<?php $Campsite["website_url"] ?>/stylesheet.css">
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<?php  
	if (!$validUser) { 
		?> 
		<TITLE><?php  putGS("Login failed"); ?></TITLE>
		<?php  
	} 
	else {
		// Successful login.
		// Redirect the user to the admin home page.
    	?> 
    	<TITLE><?php putGS("Login"); ?></TITLE>
 		<META HTTP-EQUIV="Set-Cookie" CONTENT="TOL_UserId=<?php print $user->getId() ?>; path=/">
 		<META HTTP-EQUIV="Set-Cookie" CONTENT="TOL_UserKey=<?php print $user->getKeyId() ?>; path=/">
 		<META HTTP-EQUIV="Set-Cookie" CONTENT="TOL_Language=<?php p($selectLanguage); ?>; path=/">
 		<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/priv/index.php">
		<?php  
	} ?>
	</HEAD>

	<?php  
	if (!$validUser) { 
		?>
		<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
		<TR>
			<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
			<TD>
			    <DIV STYLE="font-size: 12pt"><B><?php  putGS("Login failed"); ?></B></DIV>
			    <HR NOSHADE SIZE="1" COLOR="BLACK">
			</TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT>
				<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
				<TR>
					<TD><A HREF="/priv/login.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Login"); ?>"></A></TD>
					<TD><A HREF="/priv/login.php" ><B><?php  putGS("Login");  ?></B></A></TD>
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

		<HR NOSHADE SIZE="1" COLOR="BLACK">
		<A STYLE='font-size:8pt;color:#000000' href='http://www.campware.org' target='campware'>CAMPSITE  2.1.5 &copy 1999-2004 MDLF, maintained and distributed under GNU GPL by CAMPWARE</a>
		</BODY>
		<?php  
	} //if (!$validUser) 
	?>
</HTML>

