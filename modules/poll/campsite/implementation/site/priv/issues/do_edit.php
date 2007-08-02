<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Template.php');

// Check permissions
if (!$g_user->hasPermission('ManageIssue')) {
	camp_html_display_error(getGS('You do not have the right to change issue details.'));
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int');
$f_issue_number = Input::Get('f_issue_number', 'int');
$f_current_language_id = Input::Get('f_current_language_id', 'int');
$f_issue_name = trim(Input::Get('f_issue_name'));
$f_new_language_id = Input::Get('f_new_language_id', 'int');
$f_publication_date = Input::Get('f_publication_date', 'string', '', true);
$f_issue_template_id = Input::Get('f_issue_template_id', 'int');
$f_section_template_id = Input::Get('f_section_template_id', 'int');
$f_article_template_id = Input::Get('f_article_template_id', 'int');
$f_url_name = trim(Input::Get('f_url_name'));

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;
}
$publicationObj =& new Publication($f_publication_id);
$issueObj =& new Issue($f_publication_id, $f_current_language_id, $f_issue_number);

$backLink = "/$ADMIN/issues/edit.php?Pub=$f_publication_id&Issue=$f_issue_number&Language=$f_current_language_id";
if ($f_new_language_id == 0) {
	camp_html_add_msg(getGS('You must select a language.'));
}
if (empty($f_issue_name)) {
	camp_html_add_msg(getGS('You must complete the $1 field.', "'".getGS('Name')."'"));
}
if (empty($f_url_name)) {
	camp_html_add_msg(getGS('You must complete the $1 field.', "'".getGS('URL Name')."'"));
}
if (!camp_is_valid_url_name($f_url_name)) {
	camp_html_add_msg(getGS('The $1 field may only contain letters, digits and underscore (_) character.', "'" . getGS('URL Name') . "'"));
}
if (camp_html_has_msgs()) {
	camp_html_goto_page($backLink);
}

$changed = true;
$changed &= $issueObj->setName($f_issue_name);
if ($issueObj->getWorkflowStatus() == 'Y') {
	$changed &= $issueObj->setPublicationDate($f_publication_date);
}
$changed &= $issueObj->setIssueTemplateId($f_issue_template_id);
$changed &= $issueObj->setSectionTemplateId($f_section_template_id);
$changed &= $issueObj->setArticleTemplateId($f_article_template_id);

if ($changed) {
	$logtext = getGS('Issue $1 updated in publication $2', $f_issue_name, $publicationObj->getName());
	Log::Message($logtext, $g_user->getUserName(), 11);
} else {
	$errMsg = getGS("Could not save the changes to the issue.");
	camp_html_add_msg($errMsg);
	exit;
}

// The tricky part - language ID and URL name must be unique.
$conflictingIssues = Issue::GetIssues($f_publication_id, $f_new_language_id, null, $f_url_name);
$conflictingIssue = array_pop($conflictingIssues);
// If it conflicts with another issue
if ($errorMsg = camp_is_issue_conflicting($f_publication_id, $f_issue_number, $f_new_language_id, $f_url_name, true)) {
	camp_html_add_msg($errorMsg);
	camp_html_goto_page($backLink);
} else {
	$issueObj->setProperty('ShortName', $f_url_name, false);
	$issueObj->setProperty('IdLanguage', $f_new_language_id, false);
	$issueObj->commit();
	$link = "/$ADMIN/issues/edit.php?Pub=$f_publication_id&Issue=$f_issue_number&Language=".$issueObj->getLanguageId();
	camp_html_add_msg(getGS('Issue updated'), "ok");
	camp_html_goto_page($link);
}

?>