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
	<TITLE><?php  putGS("Menu"); ?></TITLE>
</HEAD>

<?php  if ($access) { 

   if (getVar($XPerm,'ManagePub') == "Y")
	$mpa=1;
    else 
	$mpa=0;
    

   if (getVar($XPerm,'ManageUserTypes') == "Y")
	$muta=1;
    else 
	$muta=0;
    

   if (getVar($XPerm,'ManageDictionary') == "Y")
	$mda=1;
    else 
	$mda=0;
    

   if (getVar($XPerm,'ManageClasses') == "Y")
	$mca=1;
    else 
	$mca=0;
    

   if (getVar($XPerm,'ManageCountries') == "Y")
	$mcoa=1;
    else 
	$mcoa=0;
    

   if (getVar($XPerm,'ManageArticleTypes') == "Y")
	$mata=1;
    else 
	$mata=0;
    

   if (getVar($XPerm,'ManageUsers') == "Y")
	$mua=1;
    else 
	$mua=0;
    

   if (getVar($XPerm,'ManageLanguages') == "Y")
	$mla=1;
    else 
	$mla=0;
    

   if (getVar($XPerm,'ManageTempl') == "Y")
	$mta=1;
    else 
	$mta=0;
    

   if (getVar($XPerm,'ViewLogs') == "Y")
	$vla=1;
    else 
	$vla=0;
    

   if (getVar($XPerm,'ManageLocalizer') == "Y")
	$mlza=1;
    else 
	$mlza=0;
    

   if (getVar($XPerm,'ManageIndexer') == "Y")
	$mia=1;
    else 
	$mia=0;
    

   if (getVar($XPerm,'ManageTopics') == "Y")
	$mcta=1;
    else 
	$mcta=0;
    

?>
<STYLE>
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

<BODY BGCOLOR="#D0D0B0" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
    <TR><TD ALIGN="RIGHT"><A HREF="home.php" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD><TD NOWRAP><A HREF="home.php" ONCLICK="" TARGET="fmain"><?php  putGS("Home"); ?></A></TD></TR>
    <TR><TD ALIGN="RIGHT"><A HREF="" ONCLICK="window.open('/priv/popup/', 'fpopup', 'menu=no,width=500,height=410'); return false;" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Quick Menu"); ?>"></A></TD><TD NOWRAP><A HREF="" ONCLICK="window.open('/priv/popup/', 'fpopup', 'menu=no,width=500,height=410'); return false;" TARGET="fmain"><?php  putGS("Quick Menu"); ?></A></TD></TR>
    <TR><TD COLSPAN="2"><HR NOSHADE SIZE="1" COLOR="BLACK"></TD></TR>
    <TR><TD ALIGN="RIGHT"><A HREF="pub/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Publications"); ?>"></A></TD><TD NOWRAP><A HREF="pub/" ONCLICK="" TARGET="fmain"><?php  putGS("Publications"); ?></A></TD></TR>
<?php  if ($mta) { ?>    <TR><TD ALIGN="RIGHT"><A HREF="/look/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Templates"); ?>"></A></TD><TD NOWRAP><A HREF="/look/" ONCLICK="" TARGET="fmain"><?php  putGS("Templates"); ?></A></TD></TR>
<?php  } ?><?php  if ($mcta) { ?>    <TR><TD ALIGN="RIGHT"><A HREF="topics/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Topics"); ?>"></A></TD><TD NOWRAP><A HREF="topics/" ONCLICK="" TARGET="fmain"><?php  putGS("Topics"); ?></A></TD></TR>
<?php  } ?><?php  if ($mua) { ?>    <TR><TD ALIGN="RIGHT"><A HREF="users/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Users"); ?>"></A></TD><TD NOWRAP><A HREF="users/" ONCLICK="" TARGET="fmain"><?php  putGS("Users"); ?></A></TD></TR>
<?php  } ?><?php  if ($muta) { ?>    <TR><TD ALIGN="RIGHT"><A HREF="u_types/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("User Types"); ?>"></A></TD><TD NOWRAP><A HREF="u_types/" ONCLICK="" TARGET="fmain"><?php  putGS("User Types"); ?></A></TD></TR>
<?php  } ?><?php  if ($mata) { ?>    <TR><TD ALIGN="RIGHT"><A HREF="a_types/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Article Types"); ?>"></A></TD><TD NOWRAP><A HREF="a_types/" ONCLICK="" TARGET="fmain"><?php  putGS("Article Types"); ?></A></TD></TR>
<?php  } ?><?php  if ($mcoa) { ?>    <TR><TD ALIGN="RIGHT"><A HREF="country/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Countries"); ?>"></A></TD><TD NOWRAP><A HREF="country/" ONCLICK="" TARGET="fmain"><?php  putGS("Countries"); ?></A></TD></TR>
<?php  } ?><?php  if ($mla) { ?>    <TR><TD ALIGN="RIGHT"><A HREF="languages/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Languages"); ?>"></A></TD><TD NOWRAP><A HREF="languages/" ONCLICK="" TARGET="fmain"><?php  putGS("Languages"); ?></A></TD></TR>
<?php  } ?><?php  if ($mda) { ?>    <TR><TD ALIGN="RIGHT"><A HREF="glossary/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Glossary"); ?>"></A></TD><TD NOWRAP><A HREF="glossary/" ONCLICK="" TARGET="fmain"><?php  putGS("Glossary"); ?></A></TD></TR>
<?php  } ?><?php  if ($mca) { ?>    <TR><TD ALIGN="RIGHT"><A HREF="infotype/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Infotype"); ?>"></A></TD><TD NOWRAP><A HREF="infotype/" ONCLICK="" TARGET="fmain"><?php  putGS("Infotype"); ?></A></TD></TR>
<?php  } ?><?php  if ($vla) { ?>    <TR><TD ALIGN="RIGHT"><A HREF="logs/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logs"); ?>"></A></TD><TD NOWRAP><A HREF="logs/" ONCLICK="" TARGET="fmain"><?php  putGS("Logs"); ?></A></TD></TR>
<?php  } ?><?php  if ($mlza) { ?>	<TR><TD COLSPAN="2"><HR NOSHADE SIZE="1" COLOR="BLACK"></TD></TR>
	<TR><TD ALIGN="RIGHT"><A HREF="localizer/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Localizer"); ?>"></A></TD><TD NOWRAP><A HREF="localizer/" ONCLICK="" TARGET="fmain"><?php  putGS("Localizer"); ?></A></TD></TR>
<?php  } ?><?php  if (1) { ?>  <TR><TD COLSPAN="2"><HR NOSHADE SIZE="1" COLOR="BLACK"></TD></TR>
  <TR><TD ALIGN="RIGHT"><A HREF="imagearchive/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Image archive"); ?>"></A></TD><TD NOWRAP><A HREF="imagearchive/" ONCLICK="" TARGET="fmain"><?php  putGS("Image archive"); ?></A></TD></TR>
<?php  } ?>    <TR><TD COLSPAN="2"><HR NOSHADE SIZE="1" COLOR="BLACK"></TD></TR>
<?php 
## added by sebastian
if (function_exists ("incModFile"))
	incModFile ($TOL_UserId);
?>
    <TR><TD ALIGN="RIGHT"><A HREF="logout.php" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD><TD NOWRAP><A HREF="logout.php" ONCLICK="" TARGET="fmain"><?php  putGS("Logout"); ?></A></TD></TR>
</TABLE>

</BODY>
<?php  } ?>

</HTML>
