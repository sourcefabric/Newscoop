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
    


    <?php  if ($access) {
	query ("SELECT AddImage FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'Perm');
	 if ($NUM_ROWS) {
		fetchRow($Perm);
		$access = (getVar($Perm,'AddImage') == "Y");
	}
	else $access = 0;
	} ?>
    
 

<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("Add new image"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/priv/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to add images" )); ?>">
<?php  } ?></HEAD>

<?php  if ($access) {
?><STYLE>
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

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
		<TD>
		    <DIV STYLE="font-size: 12pt"><B><?php  putGS("Add new image"); ?></B></DIV>
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

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add.php" ENCTYPE="multipart/form-data">
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS("Add new image"); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Description"); ?>:</TD>
		<TD align="left">
		<INPUT TYPE="TEXT" NAME="cDescription" VALUE="Image <?php  p($nr); ?>" SIZE="32" MAXLENGTH="128">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Photographer"); ?>:</TD>
		<TD align="left">
		<INPUT TYPE="TEXT" NAME="cPhotographer" VALUE="<?php  pgetHVar($Usr,'Name'); ?>" SIZE="32" MAXLENGTH="64">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Place"); ?>:</TD>
		<TD align="left">
		<INPUT TYPE="TEXT" NAME="cPlace" SIZE="32" MAXLENGTH="64">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Date"); ?>:</TD>
		<TD align="left">
<?php  
    query ("SELECT LEFT(NOW(), 10)", 'q_now');
    fetchRowNum($q_now);
?>		<INPUT TYPE="TEXT" NAME="cDate" VALUE="<?php  pgetNumVar($q_now,0); ?>" SIZE="10" MAXLENGTH="10"> <?php  putGS('YYYY-MM-DD'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("URL"); ?>:</TD>
		<TD align="left">
		<INPUT TYPE="TEXT" NAME="cURL" SIZE="32" MAXLENGTH="64">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Image"); ?>:</TD>
		<TD align="left">
		<INPUT TYPE="FILE" NAME="cImage" SIZE="32" MAXLENGTH="64">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='index.php'">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
<P>


<HR NOSHADE SIZE="1" COLOR="BLACK">
<a STYLE='font-size:8pt;color:#000000' href='http://www.campware.org' target='campware'>CAMPSITE  2.1.5 &copy 1999-2004 MDLF, maintained and distributed under GNU GPL by CAMPWARE</a>

</BODY>
<?php  } ?>

</HTML>
