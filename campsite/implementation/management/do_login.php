<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<?php  include ("./lib_campsite.php");
    $globalfile=selectLanguageFile('.','globals');
    $localfile=selectLanguageFile('.','locals');
    @include ($globalfile);
    @include ($localfile);
    include ("./languages.php");   ?>
<?php  require_once("$DOCUMENT_ROOT/db_connect.php"); ?>

<?php
    $ok=0;
    todef ('UserName');
    todef ('UserPassword');
    todef ('selectlanguage');
    query ( "SELECT Id FROM Users WHERE UName='$UserName' AND Password=PASSWORD('$UserPassword') AND Reader='N'", 'q' );
    if ($NUM_ROWS) {
 fetchRow ($q);
 query ( "UPDATE Users SET KeyId=RAND()*1000000000+RAND()*1000000+RAND()*1000 WHERE Id=".getVar($q,'Id'));
 $ok=$AFFECTED_ROWS;
 if ($ok) {
     query ( "SELECT Id, KeyId FROM Users WHERE Id=".getVar($q,'Id'), 'usrs');
 }
    }
?>

<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">

 <META HTTP-EQUIV="Expires" CONTENT="now">
<?php  if ($ok==0) { ?> <TITLE><?php  putGS("Login failed"); ?></TITLE>
<?php  } else {
    fetchRow($usrs);?> <TITLE><?php  putGS("Login"); ?></TITLE>
 <META HTTP-EQUIV="Set-Cookie" CONTENT="TOL_UserId=<?php  print getVar ($usrs,'Id'); ?>; path=/">
 <META HTTP-EQUIV="Set-Cookie" CONTENT="TOL_UserKey=<?php  print getVar ($usrs,'KeyId'); ?>; path=/">
 <?php
 if (function_exists ("incModFile"))
   incModFile ();

     if (!isset($selectlanguage))
  $selectlanguage='en';
     
 ?>
 <META HTTP-EQUIV="Set-Cookie" CONTENT="TOL_Language=<?php  p($selectlanguage); ?>; path=/">
 <META HTTP-EQUIV="Refresh" CONTENT="0; URL=/priv/index.php">
<?php  } ?></HEAD>

<?php  if ($ok==0) { ?><STYLE>
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
		    <DIV STYLE="font-size: 12pt"><B><?php  putGS("Login failed"); ?></B></DIV>
		    <HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR><TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/priv/login.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Login"); ?>"></A></TD><TD><A HREF="/priv/login.php" ><B><?php  putGS("Login");  ?></B></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<BLOCKQUOTE>
 <LI><?php  putGS('Login failed'); ?></LI>
 <LI><?php  putGS('Pease make sure that you typed the correct user name and password.'); ?></LI>
 <LI><?php  putGS('If your problem persists please contact the site administrator $1','<A HREF="mailto:'.encURL($SERVER_ADMIN).'">'.encHTML($SERVER_ADMIN) );?></A></LI>
</BLOCKQUOTE>

<HR NOSHADE SIZE="1" COLOR="BLACK">
<a STYLE='font-size:8pt;color:#000000' href='http://www.campware.org' target='campware'>CAMPSITE  2.1.5 &copy 1999-2004 MDLF, maintained and distributed under GNU GPL by CAMPWARE</a>
</BODY>
<?php  } ?>

</HTML>

