<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files();
todefnum('TOL_UserId');
todefnum('TOL_UserKey');
list($access, $User) = check_basic_access($_REQUEST);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite["website_url"] ?>/stylesheet.css">	
	<TITLE><?php  putGS("Menu"); ?></TITLE>
</HEAD>

<?php  
if (!$access) { 
	?>
	</HTML>
	<?php
	return;
}
?>
<BODY BGCOLOR="#D0D0B0" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
    
<TR>
	<TD ALIGN="RIGHT"><A HREF="home.php" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD><TD NOWRAP><A HREF="home.php" ONCLICK="" TARGET="fmain"><?php  putGS("Home"); ?></A></TD>
</TR>
    
<TR>
	<TD ALIGN="RIGHT"><A HREF="" ONCLICK="window.open('/priv/popup/', 'fpopup', 'menu=no,width=500,height=410'); return false;" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Quick Menu"); ?>"></A></TD><TD NOWRAP><A HREF="" ONCLICK="window.open('/priv/popup/', 'fpopup', 'menu=no,width=500,height=410'); return false;" TARGET="fmain"><?php  putGS("Quick Menu"); ?></A></TD>
</TR>
    
<TR>
	<TD COLSPAN="2"><HR NOSHADE SIZE="1" COLOR="BLACK"></TD>
</TR>
    
<TR>
	<TD ALIGN="RIGHT"><A HREF="pub/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Publications"); ?>"></A></TD><TD NOWRAP><A HREF="pub/" ONCLICK="" TARGET="fmain"><?php  putGS("Publications"); ?></A></TD>
</TR>

<?php  if ($User->hasPermission("ManageTempl")) { ?>    
<TR>
	<TD ALIGN="RIGHT"><A HREF="/look/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Templates"); ?>"></A></TD><TD NOWRAP><A HREF="/look/" ONCLICK="" TARGET="fmain"><?php  putGS("Templates"); ?></A></TD>
</TR>
<?php  } ?>

<?php  if ($User->hasPermission("ManageTopics")) { ?>    
<TR>
	<TD ALIGN="RIGHT"><A HREF="topics/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Topics"); ?>"></A></TD><TD NOWRAP><A HREF="topics/" ONCLICK="" TARGET="fmain"><?php  putGS("Topics"); ?></A></TD>
</TR>
<?php  } ?>

<?php  if ($User->hasPermission("ManageUsers")) { ?>    
<TR>
	<TD ALIGN="RIGHT"><A HREF="users/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Users"); ?>"></A></TD><TD NOWRAP><A HREF="users/" ONCLICK="" TARGET="fmain"><?php  putGS("Users"); ?></A></TD>
</TR>
<?php  } ?>

<?php  if ($User->hasPermission("ManageUserTypes")) { ?>    
<TR>
	<TD ALIGN="RIGHT"><A HREF="u_types/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("User Types"); ?>"></A></TD><TD NOWRAP><A HREF="u_types/" ONCLICK="" TARGET="fmain"><?php  putGS("User Types"); ?></A></TD>
</TR>
<?php  } ?>

<?php  if ($User->hasPermission("ManageArticleTypes")) { ?>    
<TR>
	<TD ALIGN="RIGHT"><A HREF="a_types/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Article Types"); ?>"></A></TD><TD NOWRAP><A HREF="a_types/" ONCLICK="" TARGET="fmain"><?php  putGS("Article Types"); ?></A></TD>
</TR>
<?php  } ?>

<?php  if ($User->hasPermission("ManageCountries")) { ?>    
<TR>
	<TD ALIGN="RIGHT"><A HREF="country/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Countries"); ?>"></A></TD><TD NOWRAP><A HREF="country/" ONCLICK="" TARGET="fmain"><?php  putGS("Countries"); ?></A></TD>
</TR>
<?php  } ?>

<?php  if ($User->hasPermission("ManageLanguages")) { ?>    
<TR>
	<TD ALIGN="RIGHT"><A HREF="languages/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Languages"); ?>"></A></TD><TD NOWRAP><A HREF="languages/" ONCLICK="" TARGET="fmain"><?php  putGS("Languages"); ?></A></TD>
</TR>
<?php  } ?>

<?php  if ($User->hasPermission("ManageDictionary")) { ?>    
<TR>
	<TD ALIGN="RIGHT"><A HREF="glossary/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Glossary"); ?>"></A></TD><TD NOWRAP><A HREF="glossary/" ONCLICK="" TARGET="fmain"><?php  putGS("Glossary"); ?></A></TD>
</TR>
<?php  } ?>

<?php  if ($User->hasPermission("ManageClasses")) { ?>    
<TR>
	<TD ALIGN="RIGHT"><A HREF="infotype/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Infotype"); ?>"></A></TD><TD NOWRAP><A HREF="infotype/" ONCLICK="" TARGET="fmain"><?php  putGS("Infotype"); ?></A></TD>
</TR>
<?php  } ?>

<?php  if ($User->hasPermission("ViewLogs")) { ?>    
<TR>
	<TD ALIGN="RIGHT"><A HREF="logs/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logs"); ?>"></A></TD><TD NOWRAP><A HREF="logs/" ONCLICK="" TARGET="fmain"><?php  putGS("Logs"); ?></A></TD>
</TR>
<?php  } ?>

<?php  if ($User->hasPermission("ManageLocalizer")) { ?>	
<TR>
	<TD COLSPAN="2"><HR NOSHADE SIZE="1" COLOR="BLACK"></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT"><A HREF="localizer/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Localizer"); ?>"></A></TD><TD NOWRAP><A HREF="localizer/" ONCLICK="" TARGET="fmain"><?php  putGS("Localizer"); ?></A></TD>
</TR>
<?php  } ?>

<?php  if (1) { ?>  
<TR>
	<TD COLSPAN="2"><HR NOSHADE SIZE="1" COLOR="BLACK"></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT"><A HREF="imagearchive/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Image archive"); ?>"></A></TD><TD NOWRAP><A HREF="imagearchive/" ONCLICK="" TARGET="fmain"><?php  putGS("Image archive"); ?></A></TD>
</TR>
<?php  } ?>    

<TR>
	<TD COLSPAN="2"><HR NOSHADE SIZE="1" COLOR="BLACK"></TD>
</TR>

<?php 
## added by sebastian
if (function_exists ("incModFile"))
	incModFile ($TOL_UserId);
?>

<TR>
	<TD ALIGN="RIGHT"><A HREF="logout.php" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD><TD NOWRAP><A HREF="logout.php" ONCLICK="" TARGET="fmain"><?php  putGS("Logout"); ?></A></TD>
</TR>

</TABLE>

</BODY>
</HTML>
