<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('AddArticle')) {
	camp_html_display_error(getGS("You do not have the right to add articles."));
	exit;
}

// Get input
$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_article_name = trim(Input::Get('f_article_name', 'string', ''));
$f_article_type = trim(Input::Get('f_article_type', 'string', ''));
$f_article_language = trim(Input::Get('f_article_language', 'int', 0));

// Check input
if (empty($f_article_name)) {
	camp_html_display_error(getGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'));
	exit;
}
    
if (empty($f_article_type)) {
	camp_html_display_error(getGS('You must select an article type.'));
	exit;
}
    
if (empty($f_article_language)) {
	camp_html_display_error(getGS('You must select a language.'));
	exit;
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;	
}

$publicationObj =& new Publication($f_publication_id);
if (!$publicationObj->exists()) {
	camp_html_display_error(getGS('Publication does not exist.'));
	exit;	
}

$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
if (!$issueObj->exists()) {
	camp_html_display_error(getGS('Issue does not exist.'));
	exit;	
}

$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
if (!$sectionObj->exists()) {
	camp_html_display_error(getGS('Section does not exist.'));
	exit;	
}

// Create article
$articleObj =& new Article($f_language_id);
$articleObj->create($f_article_type, $f_article_name);
if ($articleObj->exists()) {
	$articleObj->setPublicationId($f_publication_id);
	$articleObj->setIssueNumber($f_issue_number);
	$articleObj->setSectionNumber($f_section_number);
	$articleObj->setUserId($User->getUserId());
	$articleObj->setIsPublic(true);
	
	## added by sebastian
	if (function_exists ("incModFile")) {
		incModFile();
	}
	
	header("Location: ".camp_html_article_url($articleObj, $f_language_id, "edit.php"));
}
else {
	camp_html_display_error("Could not create article.");
}
exit;
?>