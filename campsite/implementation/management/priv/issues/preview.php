<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Template.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Language = Input::Get('Language', 'int', 0);
$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);

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
if ($errorStr == "" && ($templateId = $issueObj->getIssueTemplateId()) == 0)
	$errorStr = 'This issue cannot be previewed. Please make sure it has the front page template selected.';

if ($errorStr != "") {
	header("Location: /$ADMIN/ad_popup.php?ADReason=".urlencode($errorStr));
	exit(0);
}

$accessParams = "LoginUserId=" . $User->getUserId() . "&LoginUserKey=" . $User->getKeyId()
				. "&AdminAccess=all";
$urlType = $publicationObj->getProperty('IdURLType');
if ($urlType == 1) {
	$templateObj = & new Template($templateId);
	$uri = "/look/" . $templateObj->getName()
		. "?IdLanguage=$Language&IdPublication=$Pub&NrIssue=$Issue&$accessParams";
} else {
	$uri = "/" . $languageObj->getCode() . "/" . $issueObj->getUrlName() . "?$accessParams";
}

if ($User->hasPermission("ManageTempl") || $User->hasPermission("DeleteTempl")) {
	// Show dual-pane view for those with template management priviledges
?>
<FRAMESET ROWS="60%,*" BORDER="1">
	<FRAME SRC="<?php echo "$uri&preview=on"; ?>" NAME="body" FRAMEBORDER="1">
	<FRAME NAME="e" SRC="empty.php" FRAMEBORDER="1">
</FRAMESET>
<?php
} else {
	// Show single pane for everyone else.
?>
	<FRAMESET ROWS="100%">
		<FRAME SRC="<?php print $uri; ?>" NAME="body" FRAMEBORDER="1">
	</FRAMESET>
<?php
}
?>
