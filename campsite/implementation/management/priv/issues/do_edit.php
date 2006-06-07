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
	camp_html_display_error(getGS('You must select a language.'), $backLink);
	exit;
}
if (empty($f_issue_name)) {
	camp_html_display_error(getGS('You must complete the $1 field.', "'".getGS('Name')."'"),
		$backLink);
	exit;
}
if (empty($f_url_name)) {
	camp_html_display_error(getGS('You must complete the $1 field.', "'".getGS('URL Name')."'"),
		$backLink);
	exit;
}
if (!camp_is_valid_url_name($f_url_name)) {
	camp_html_display_error(getGS('The $1 field may only contain letters, digits and underscore (_) character.', "'" . getGS('URL Name') . "'"), $backLink);
	exit;
}
$issueObj->setProperty('Name', $f_issue_name, false);
if ($issueObj->getWorkflowStatus() == 'Y') {
	$issueObj->setProperty('PublicationDate', $f_publication_date, false);
}
$issueObj->setProperty('IssueTplId', $f_issue_template_id, false);
$issueObj->setProperty('SectionTplId', $f_section_template_id, false);
$issueObj->setProperty('ArticleTplId', $f_article_template_id, false);
$issueObj->setProperty('ShortName', $f_url_name, false);
if ($issueObj->commit()) {
	$issueObj->setLanguageId($f_new_language_id);
	$logtext = getGS('Issue $1 updated in publication $2', $f_issue_name, $publicationObj->getName());
	Log::Message($logtext, $g_user->getUserName(), 11);
} else {
	$errMsg = getGS("Could not save the changes to the issue $1. Please make sure the issue URL name '$2' was not used before in the publication $3.",
					$issueObj->getName(), $issueObj->getUrlName(), $publicationObj->getName());
	camp_html_display_error($errMsg, $backLink);
	exit;
}

header("Location: /$ADMIN/issues/edit.php?Pub=$f_publication_id&Issue=$f_issue_number&Language=".$issueObj->getLanguageId());
exit;

?>