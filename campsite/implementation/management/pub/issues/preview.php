<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/pub/issues");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

setcookie("TOL_Access", "all", time());
setcookie("TOL_Preview", "on", time());

$Language = Input::get('Language', 'int', 0);
$Pub = Input::get('Pub', 'int', 0);
$Issue = Input::get('Issue', 'int', 0);

$errorStr = "";
$languageObj = & new Language($Language);
if (!$languageObj->exists()) {
	$errorStr = getGS('There was an error reading the language parameter.');
}
if ($errorStr == "") {
	$publicationObj = & new Publication($Pub);
	if (!$publicationObj->exists())
		$errorStr = getGS('There was an error reading the publication parameter.');
}
if ($errorStr == "") {
	$issueObj = & new Issue($Pub, $Language, $Issue);
	if (!$issueObj->exists())
		$errorStr = getGS('There was an error reading the issue parameter.');
}

if ($errorStr != "") {
	header("Location: /$ADMIN/ad_popup.php?ADReason=".urlencode($errorStr));
	exit(0);
}

if (($templateId = $issueObj->getIssueTemplateId()) == 0)
	$errorStr = getGS('This issue cannot be previewed. Please make sure it has a $1 template selected.','<B><I>'.getGS('front page').'</I></B>');

?>

<HTML>
<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<TITLE><?php  putGS("Preview issue"); ?></TITLE>
</HEAD>

<BODY>

<?php if ($errorStr == "") { ?>

<FRAMESET ROWS="60%,*" BORDER="1">
<FRAME SRC="<?php  pgetVar($q_iss,'IssueTplId'); ?>?IdPublication=<?php  pencURL($Pub); ?>&NrIssue=<?php  pencURL($Issue); ?>&IdLanguage=<?php  pencURL($Language); ?>" NAME="body" FRAMEBORDER="1" MARGINWIDTH="0" MARGINHEIGHT="0">
<FRAME NAME="e" SRC="empty.php" FRAMEBORDER="1" MARGINWIDTH="0" MARGINHEIGHT="0">
</FRAMESET>

<?php } else { ?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Preview issue"); ?>
		</TD>

	<TR><TD>&nbsp;</TD></TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  echo $publicationObj->getName(); ?></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Issue"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php echo $publicationObj->getName(); ?>. <?php echo $publicationObj->getName(); ?> (<?php echo $languageObj->getName(); ?>)</TD>

</TR></TABLE>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER" style="margin-top: 50px; margin-bottom: 50px;">
<TR>
	<TD>
		<B><font color="red"><?php  putGS("Error"); ?> </font></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD>
		<font color="red"><li><?php echo $errorStr; ?></li></font>
	</TD>
</TR>
<TR>
	<TD align="center">
		<a href="javascript:self.close()"><b><?php  putGS('Close'); ?></b></a>
	</TD>
</TR>
</TABLE>

<?php CampsiteInterface::CopyrightNotice(); ?>
</BODY>

<?php } ?>

</HTML>
