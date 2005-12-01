<?php  
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);

$languageObj = & new Language($f_language_id);
$publicationObj = & new Publication($f_publication_id);
$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
$articleObj =& new Article($f_language_selected, $f_article_number);

$errorStr = "";
if (!$articleObj->exists()) {
	$errorStr = getGS('There was an error reading request parameters.');
} else {
	$templateId = $sectionObj->getArticleTemplateId();
	if ($templateId == 0) {
		$templateId = $issueObj->getArticleTemplateId();
	}
	if ($templateId == 0) {
		$errorStr = getGS('This article cannot be previewed. Please make sure it has the article template selected.');
	}
}

if ($errorStr != "") {
	camp_html_display_error($errorStr, null, true);
}

setcookie("TOL_UserId", $User->getUserId(), null, "/");
setcookie("TOL_UserKey", $User->getKeyId(), null, "/");
setcookie("TOL_Access", "all", null, "/");
if ($User->hasPermission("ManageTempl") || $User->hasPermission("DeleteTempl"))
	setcookie("TOL_Preview", "on", null, "/");

$templateObj =& new Template($templateId);

$urlType = $publicationObj->getProperty('IdURLType');
if ($urlType == 1) {
	$templateObj = & new Template($templateId);
	$url = "/look/" . $templateObj->getName() . "?IdLanguage=$f_language_id"
		. "&IdPublication=$f_publication_id&NrIssue=$f_issue_number&NrSection=$f_section_number"
		. "&NrArticle=$f_article_number";
} else {
	$url = "/" . $languageObj->getCode() . "/" . $issueObj->getUrlName()
		. "/" . $sectionObj->getUrlName() . "/" . $articleObj->getUrlName();
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
