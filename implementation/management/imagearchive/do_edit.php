<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<?php
	require_once("../lib_campsite.php");
	require_once ("../languages.php");
	require_once('include.inc.php');
	require_once("$DOCUMENT_ROOT/db_connect.php");

	$globalfile=selectLanguageFile('..','globals');
	$localfile=selectLanguageFile('.','locals');
	@include ($globalfile);
	@include ($localfile);
?>

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
	<TITLE><?php  putGS("Updating image information"); ?></TITLE>
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
    todef('cDescription');
    todef('cPhotographer');
    todef('cPlace');
	todef('cDate');
	todef('cURL');
	todef('cImageName', $_FILES['cImage']['name']);
	todef('cImageTemp', $_FILES['cImage']['tmp_name']);
	todef('cImageType', $_FILES['cImage']['type']);
	todef('cImageError', $_FILES['cImage']['error']);
	todef('cImageSize', $_FILES['cImage']['size']);

	$Link = cImgLink();
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
		<TD>
			<DIV STYLE="font-size: 12pt"><B><?php  putGS("Updating image information"); ?></B></DIV>
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
query ("SELECT Id FROM Images WHERE Id=$Id", 'q_img');
if ($NUM_ROWS) {
	fetchRow($q_img);
?>
<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <font color="black"><?php  putGS("Edit image information"); ?> </font></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php
	if ($cURL) {
		// remote image
        echo handleRemoteImg ($cDescription, $cPhotographer, $cPlace, $cDate, $cURL, $Id);
	} else {
		// local image
		echo handleLocalImage($cImageTemp, $cDescription, $cPhotographer, $cPlace, $cDate, $cURL, $Id);
	}

	if ($AFFECTED_ROWS > 0) { ?>	<LI><?php  putGS('Image information has been updated.'); ?></LI>
<?php  $logtext = getGS('Changed image properties of $1',encHTML($cDescription)); query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=43, User='".getVar($Usr,'UName')."', Text='$logtext'"); ?>
<?php  } else { ?>	<LI><?php  putGS('Image information could not be updated.'); ?></LI>
<?php  } ?>	</BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
<?php  if ($AFFECTED_ROWS > 0) { ?>		<INPUT TYPE="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='index.php?<?php echo $Link['SO']; ?>'">
<?php  } else { ?>		<INPUT TYPE="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='edit.php?Id=<?php echo $Id.$Link['SO']; ?>'">
<?php  } ?>		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
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

