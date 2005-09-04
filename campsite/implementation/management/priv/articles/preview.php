<?php  
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Article = Input::Get('Article', 'int', 0);

$languageObj = & new Language($Language);
$publicationObj = & new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);

$errorStr = "";
if (!$articleObj->exists()) {
	$errorStr = getGS('There was an error reading request parameters.');
} else {
	$templateId = $sectionObj->getArticleTemplateId();
	if ($templateId == 0)
		$templateId = $issueObj->getArticleTemplateId();
	if ($templateId == 0)
		$errorStr = getGS('This article cannot be previewed. Please make sure it has the article template selected.');
}

if ($errorStr != "")
	camp_html_display_error($errorStr, null, true);

setcookie("TOL_UserId", $User->getId(), null, "/");
setcookie("TOL_UserKey", $User->getKeyId(), null, "/");
setcookie("TOL_Access", "all", null, "/");
if ($User->hasPermission("ManageTempl") || $User->hasPermission("DeleteTempl"))
	setcookie("TOL_Preview", "on", null, "/");

$templateObj =& new Template($templateId);

$urlType = $publicationObj->getProperty('IdURLType');
if ($urlType == 1) {
	$templateObj = & new Template($templateId);
	$url = "/look/" . $templateObj->getName() . "?IdLanguage=$Language"
		. "&IdPublication=$Pub&NrIssue=$Issue&NrSection=$Section"
		. "&NrArticle=$Article";
} else {
	$url = "/" . $languageObj->getCode() . "/" . $issueObj->getShortName()
		. "/" . $sectionObj->getShortName() . "/" . $articleObj->getShortName();
}

if ($User->hasPermission("ManageTempl") || $User->hasPermission("DeleteTempl")) {
	// Show dual-pane view for those with template management priviledges
?>
	<FRAMESET ROWS="60%,*" BORDER="2">
		<FRAME SRC="<?php print $url; ?>" NAME="body" FRAMEBORDER="1">
		<FRAME NAME="e" SRC="empty.php" FRAMEBORDER="1">
	</FRAMESET>
<?php
} else {
	// Show single pane for everyone else.
?>
	<FRAMESET ROWS="100%">
		<FRAME SRC="<?php print $url; ?>" NAME="body" FRAMEBORDER="1">
	</FRAMESET>
<?php
}
?>
