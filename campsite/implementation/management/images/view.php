<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<?php  include ("../lib_campsite.php");
	$globalfile=selectLanguageFile('..','globals');
    $localfile=selectLanguageFile('.','locals');
    @include ($globalfile);
    @include ($localfile);
	include ("../languages.php");   ?>
<?php  require_once("$DOCUMENT_ROOT/db_connect.php"); ?>


<?php 
    todefnum('TOL_UserId');
    todefnum('TOL_UserKey');
    query ("SELECT * FROM Users WHERE Id=$TOL_UserId AND KeyId=$TOL_UserKey", 'Usr');
    $access=($NUM_ROWS != 0);
    if ($NUM_ROWS) {
	fetchRow($Usr);
	query ("SELECT * FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'XPerm');
	 if ($NUM_ROWS){
	 	fetchRow($XPerm);
	 }
	 else $access = 0;						//added lately; a non-admin can enter the administration area; he exists but doesn't have ANY rights
	 $xpermrows= $NUM_ROWS;
    }
    else {
	query ("SELECT * FROM UserPerm WHERE 1=0", 'XPerm');
    }
?>
    


<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("View image"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/priv/logout.php">
<?php  } ?></HEAD>

<?php  if ($access) { ?><STYLE>
	BODY { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
	SMALL { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 8pt; }
	FORM { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
	TH { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
	TD { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
	BLOCKQUOTE { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
	UL { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
	LI { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
	A  { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; text-decoration: none; color: darkblue; }
	ADDRESS { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 8pt; }
</STYLE>

<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<?php
	todefnum('Id');

	require_once('image_functions.inc.php');
	$Link     = cImgLink();
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
		<TD>
		    <DIV STYLE="font-size: 12pt"><B><?php  putGS("View image"); ?></B></DIV>
		    <HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR><TD ALIGN=RIGHT>
	  <TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
		  <TD><A HREF="/priv/images/" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Images"); ?>"></A></TD><TD><A HREF="/priv/images/" ><B><?php  putGS("Images");  ?></B></A></TD>
		  <TD><A HREF="/priv/home.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD><TD><A HREF="/priv/home.php" ><B><?php  putGS("Home");  ?></B></A></TD>
		  <TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD><TD><A HREF="/priv/logout.php" ><B><?php  putGS("Logout");  ?></B></A></TD>
		</TR>
	  </TABLE>
	</TD></TR>
</TABLE>

<?php
$query = "SELECT Id, Description, Photographer, Place, Date, ContentType, Location, URL FROM Images WHERE Id=$Id";

query ($query, 'q_img');
if ($NUM_ROWS) {
	fetchRow($q_img);
	if (getVar($q_img, 'Location') == 'local') {
		$ImageURL = $ImgPrefix.getVar($q_img, 'Id');
	} else {
		$ImageURL = getVar($q_img, 'URL');
	}

?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%">
<TR>
  <TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Image"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  pgetHVar($q_img,'Description'); ?></B></TD>
</TR></TABLE>

<P>
<CENTER><IMG SRC="<?php echo $ImageURL ?>" BORDER="0" ALT="<?php  pgetHVar($q_img,'Description'); ?>">
<br><form><input type=button value="Back" onclick="location.href='index.php?<?php echo $Link['SO']; ?>'"></form></CENTER>
<P>

<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No such image.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<HR NOSHADE SIZE="1" COLOR="BLACK">
<a STYLE='font-size:8pt;color:#000000' href='http://www.campware.org' target='campware'>CAMPSITE  2.1.5 &copy 1999-2004 MDLF, maintained and distributed under GNU GPL by CAMPWARE</a>
</BODY>
<?php  } ?>

</HTML>
