<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files();
todefnum('TOL_UserId');
todefnum('TOL_UserKey');
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	exit;
}
$menu_index = 1;
$max_menu_items = 5;
$counter_reset = false;
$counter_resets = 0;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite["website_url"] ?>/css/admin_stylesheet.css">	
	<TITLE><?php  putGS("Menu"); ?></TITLE>
</HEAD>

<BODY BGCOLOR="white" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<TABLE CELLSPACING="1" CELLPADDING="0" WIDTH="100%" bgcolor="black">

<TR>
<TD VALIGN="TOP">
	<TABLE CELLSPACING="1" CELLPADDING="1" BGCOLOR="#D0D0B0" width="100%" height="100%"><TR>
	<TD ALIGN="RIGHT"><A HREF="pub/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Publications"); ?>"></A></TD><TD NOWRAP><A HREF="pub/" ONCLICK="" TARGET="fmain"><?php  putGS("Publications"); ?></A></TD>
<?php  increment_item_counter($menu_index, $max_menu_items, $counter_reset); ?>

<?php  if ($User->hasPermission("ManageTempl")) {
		   check_counter_reset($counter_reset, $counter_resets);
?>
	<TD ALIGN="RIGHT"><A HREF="/look/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Templates"); ?>"></A></TD><TD NOWRAP><A HREF="/look/" ONCLICK="" TARGET="fmain"><?php  putGS("Templates"); ?></A></TD>
<?php
           increment_item_counter($menu_index, $max_menu_items, $counter_reset);
       }
?>

<?php  if ($User->hasPermission("ManageTopics")) {
		   check_counter_reset($counter_reset, $counter_resets);
?>
	<TD ALIGN="RIGHT"><A HREF="topics/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Topics"); ?>"></A></TD><TD NOWRAP><A HREF="topics/" ONCLICK="" TARGET="fmain"><?php  putGS("Topics"); ?></A></TD>
<?php
           increment_item_counter($menu_index, $max_menu_items, $counter_reset);
       }
?>

<?php  if ($User->hasPermission("ManageUsers")) {
		   check_counter_reset($counter_reset, $counter_resets);
?>
	<TD ALIGN="RIGHT"><A HREF="users/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Users"); ?>"></A></TD><TD NOWRAP><A HREF="users/" ONCLICK="" TARGET="fmain"><?php  putGS("Users"); ?></A></TD>
<?php
           increment_item_counter($menu_index, $max_menu_items, $counter_reset);
       }
?>

<?php  if ($User->hasPermission("ManageUserTypes")) {
		   check_counter_reset($counter_reset, $counter_resets);
?>
	<TD ALIGN="RIGHT"><A HREF="u_types/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("User Types"); ?>"></A></TD><TD NOWRAP><A HREF="u_types/" ONCLICK="" TARGET="fmain"><?php  putGS("User Types"); ?></A></TD>
<?php
           increment_item_counter($menu_index, $max_menu_items, $counter_reset);
       }
?>

<?php  if ($User->hasPermission("ManageArticleTypes")) {
		   check_counter_reset($counter_reset, $counter_resets);
?>
	<TD ALIGN="RIGHT"><A HREF="a_types/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Article Types"); ?>"></A></TD><TD NOWRAP><A HREF="a_types/" ONCLICK="" TARGET="fmain"><?php  putGS("Article Types"); ?></A></TD>
<?php
           increment_item_counter($menu_index, $max_menu_items, $counter_reset);
       }
?>

<?php  if ($User->hasPermission("ManageCountries")) {
		   check_counter_reset($counter_reset, $counter_resets);
?>
	<TD ALIGN="RIGHT"><A HREF="country/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Countries"); ?>"></A></TD><TD NOWRAP><A HREF="country/" ONCLICK="" TARGET="fmain"><?php  putGS("Countries"); ?></A></TD>
<?php
           increment_item_counter($menu_index, $max_menu_items, $counter_reset);
       }
?>

<?php  if ($User->hasPermission("ManageLanguages")) {
		   check_counter_reset($counter_reset, $counter_resets);
?>
	<TD ALIGN="RIGHT"><A HREF="languages/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Languages"); ?>"></A></TD><TD NOWRAP><A HREF="languages/" ONCLICK="" TARGET="fmain"><?php  putGS("Languages"); ?></A></TD>
<?php
           increment_item_counter($menu_index, $max_menu_items, $counter_reset);
       }
?>

<?php  if ($User->hasPermission("ManageDictionary")) {
		   check_counter_reset($counter_reset, $counter_resets);
?>
	<TD ALIGN="RIGHT"><A HREF="glossary/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Glossary"); ?>"></A></TD><TD NOWRAP><A HREF="glossary/" ONCLICK="" TARGET="fmain"><?php  putGS("Glossary"); ?></A></TD>
<?php
           increment_item_counter($menu_index, $max_menu_items, $counter_reset);
       }
?>

<?php  if ($User->hasPermission("ManageClasses")) {
		   check_counter_reset($counter_reset, $counter_resets);
?>
	<TD ALIGN="RIGHT"><A HREF="infotype/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Infotype"); ?>"></A></TD><TD NOWRAP><A HREF="infotype/" ONCLICK="" TARGET="fmain"><?php  putGS("Infotype"); ?></A></TD>
<?php
           increment_item_counter($menu_index, $max_menu_items, $counter_reset);
       }
?>

<?php  if ($User->hasPermission("ViewLogs")) {
		   check_counter_reset($counter_reset, $counter_resets);
?>
	<TD ALIGN="RIGHT"><A HREF="logs/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logs"); ?>"></A></TD><TD NOWRAP><A HREF="logs/" ONCLICK="" TARGET="fmain"><?php  putGS("Logs"); ?></A></TD>
<?php
           increment_item_counter($menu_index, $max_menu_items, $counter_reset);
       }
?>

<?php  if ($User->hasPermission("ManageLocalizer")) {
		   check_counter_reset($counter_reset, $counter_resets);
?>
	<TD ALIGN="RIGHT"><A HREF="localizer/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Localizer"); ?>"></A></TD><TD NOWRAP><A HREF="localizer/" ONCLICK="" TARGET="fmain"><?php  putGS("Localizer"); ?></A></TD>
<?php
           increment_item_counter($menu_index, $max_menu_items, $counter_reset);
       }
?>

<?php  if (true) {
		   check_counter_reset($counter_reset, $counter_resets);
?>
	<TD ALIGN="RIGHT"><A HREF="imagearchive/" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Image archive"); ?>"></A></TD><TD NOWRAP><A HREF="imagearchive/" ONCLICK="" TARGET="fmain"><?php  putGS("Image Archive"); ?></A></TD>
<?php
           increment_item_counter($menu_index, $max_menu_items, $counter_reset);
       }
       if (!$counter_reset)
		   echo "\t</TR>\n";
	   if ($counter_reset)
		   $menu_index = $max_menu_items;
	   else
		   $menu_index--;
	   if ($counter_resets < 1)
		   echo "\t<TR><TD COLSPAN=\"$menu_index\">&nbsp;</TD></TR>\n";
?>
	</TABLE>
</TD>
<?php 
## added by sebastian
if (function_exists ("incModFile")) {
?>
<TD ROWSPAN="2">|</TD>
<TD VALIGN="TOP">
<?php
	incModFile ($TOL_UserId);
	echo "</TD>\n";
}
?>

<TD WIDTH="1%" VALIGN="TOP">
	<TABLE CELLSPACING="1" CELLPADDING="1" BGCOLOR="#D0D0B0" width="100%" height="100%"><TR>
	<TD ALIGN="RIGHT"><A HREF="home.php" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD><TD NOWRAP><A HREF="home.php" ONCLICK="" TARGET="fmain"><?php  putGS("Home"); ?></A></TD>
	<TD ALIGN="RIGHT"><A HREF="" ONCLICK="window.open('/priv/popup/', 'fpopup', 'menu=no,width=500,height=410'); return false;" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Quick Menu"); ?>"></A></TD><TD NOWRAP><A HREF="" ONCLICK="window.open('/priv/popup/', 'fpopup', 'menu=no,width=500,height=410'); return false;" TARGET="fmain"><?php  putGS("Quick Menu"); ?></A></TD>
	</TR><TR>
	<TD ALIGN="RIGHT"><A HREF="logout.php" ONCLICK="" TARGET="fmain"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD><TD NOWRAP COLSPAN="3" ALIGN="LEFT"><A HREF="logout.php" ONCLICK="" TARGET="fmain"><?php  putGS("Logout"); ?></A></TD>
	</TR>
<?php
	while ($counter_resets > 1) {
		echo "\t<TR><TD COLSPAN=\"4\">&nbsp;</TD></TR>\n";
		$counter_resets--;
	}
?>
	</TABLE>
</TD>

</TR>
</TABLE>

</BODY>
</HTML>

<?php

function check_counter_reset($counter_reset, &$counter_resets)
{
	if ($counter_reset) {
		echo "\t<TR>\n";
		$counter_reset = false;
		$counter_resets++;
	}
}

function increment_item_counter(&$menu_index, $max_menu_items, &$counter_reset)
{
	$menu_index++;
	if ($menu_index > $max_menu_items) {
		echo "\t</TR>\n";
		$menu_index = 1;
		$counter_reset = true;
	} else {
		$counter_reset = false;
	}
}

?>
