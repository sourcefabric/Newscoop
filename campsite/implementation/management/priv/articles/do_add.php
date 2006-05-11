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
$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);

// For choosing the article location.
$f_destination_publication_id = Input::Get('f_destination_publication_id', 'int', 0, true);
$f_destination_issue_number = Input::Get('f_destination_issue_number', 'int', 0, true);
$f_destination_section_number = Input::Get('f_destination_section_number', 'int', 0, true);

$f_article_name = trim(Input::Get('f_article_name', 'string', ''));
$f_article_type = trim(Input::Get('f_article_type', 'string', ''));
$f_article_language = trim(Input::Get('f_article_language', 'int', 0));

$f_language_id = ($f_language_id > 0) ? $f_language_id : $f_article_language;

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

$publication_id = ($f_destination_publication_id > 0) ? $f_destination_publication_id : $f_publication_id;
$issue_number = ($f_destination_issue_number > 0) ? $f_destination_issue_number : $f_issue_number;
$section_number = ($f_destination_section_number > 0) ? $f_destination_section_number : $f_section_number;

if ($publication_id > 0) {
	$publicationObj =& new Publication($publication_id);
	if (!$publicationObj->exists()) {
		camp_html_display_error(getGS('Publication does not exist.'));
		exit;
	}

	if ($issue_number > 0) {
		$issueObj =& new Issue($publication_id, $f_article_language, $issue_number);
		if (!$issueObj->exists()) {
			camp_html_display_error(getGS('Issue does not exist.'));
			exit;
		}

		if ($section_number > 0) {
			$sectionObj =& new Section($publication_id, $issue_number, $f_article_language, $section_number);
			if (!$sectionObj->exists()) {
				camp_html_display_error(getGS('Section does not exist.'));
				exit;
			}
		}
	}
}

// Create article
$articleObj =& new Article($f_article_language);
if (($publication_id > 0) && ($issue_number > 0) && ($section_number > 0)) {
	$articleObj->create($f_article_type, $f_article_name, $publication_id, $issue_number, $section_number);
} else {
	$articleObj->create($f_article_type, $f_article_name);
}
if ($articleObj->exists()) {
	$articleObj->setCreatorId($User->getUserId());
	$articleObj->setIsPublic(true);
	if ($publication_id > 0) {
    	$commentDefault = $publicationObj->commentsArticleDefaultEnabled();
        $articleObj->setCommentsEnabled($commentDefault);
	}

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