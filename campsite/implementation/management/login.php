<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/lib_campsite.php");
$globalfile=selectLanguageFile($Campsite['HTML_DIR'] . "/$ADMIN_DIR",'globals');
$localfile=selectLanguageFile("$ADMIN_DIR","locals");
@include_once($globalfile);
@include_once($localfile);
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
?>
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php'); ?>
<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">

	<TITLE><?php  putGS("Login"); ?></TITLE>
</HEAD>
<BODY >

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" >
<TR>
	<TD align="center" style="padding-top: 50px;">
		<img src="/<?php echo $ADMIN; ?>/img/sign_big.gif" border="0">
	</TD>
</tr>
</TABLE>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_login.php" >
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS("Login"); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><?php  putGS('Please enter your user name and password'); ?></TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("User name"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="UserName" SIZE="32" MAXLENGTH="32" class="input_text">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Password"); ?>:</TD>
		<TD>
		<INPUT TYPE="PASSWORD" NAME="UserPassword" SIZE="32" MAXLENGTH="32" class="input_text">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</TD>
		<TD>
		<SELECT name="selectlanguage" class="input_select">
		    <?php 
			foreach($languages as $key=>$larr){
			    //$lcode=key($larr[]);
			    $lval=$larr['name'];
			    print "<option value='$key'>$lval";
			}
		    ?>
		</select>
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="submit" class="button" NAME="Login" VALUE="<?php  putGS('Login'); ?>">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
<P>
<?php  if (file_exists("./guest_include.php")) require("./guest_include.php"); ?>

</BODY>
</HTML>