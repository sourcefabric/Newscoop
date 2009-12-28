<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");

// Check permissions
if (!$g_user->hasPermission('ManageIssue')) {
	camp_html_display_error(getGS('You do not have the right to add issues.'));
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int');
$f_issue_name = trim(Input::Get('f_issue_name', 'string', ''));
$f_issue_number = trim(Input::Get('f_issue_number', 'int'));
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_url_name = Input::Get('f_url_name');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));
	exit;
}

$backLink = "/$ADMIN/issues/add_new.php?Pub=$f_publication_id";
$created = false;
if ($f_language_id == 0) {
	camp_html_add_msg(getGS('You must select a language.'));
}
if (empty($f_issue_name)) {
	camp_html_add_msg(getGS('You must fill in the $1 field.','<B>'.getGS('Name').'</B>'));
}
if ($f_url_name == "") {
	camp_html_add_msg(getGS('You must fill in the $1 field.','<B>'.getGS('URL Name').'</B>'));
}
if (!camp_is_valid_url_name($f_url_name)) {
	camp_html_add_msg(getGS('The $1 field may only contain letters, digits and underscore (_) character.', '</B>' . getGS('URL Name') . '</B>'));
}
if (empty($f_issue_number) || !is_numeric($f_issue_number) || ($f_issue_number <= 0)) {
	camp_html_add_msg(getGS('You must fill in the $1 field.','<B>'.getGS('Number').'</B>'));
}

if ($errorMsg = camp_is_issue_conflicting($f_publication_id, $f_issue_number, $f_language_id, $f_url_name, false)) {
	camp_html_add_msg($errorMsg);
}

if (camp_html_has_msgs()) {
	camp_html_goto_page($backLink);
}
$publicationObj = new Publication($f_publication_id);

$newIssueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
$created = $newIssueObj->create($f_url_name, array('Name' => $f_issue_name));
if ($created) {
	camp_html_add_msg(getGS("Issue created."), "ok");
	camp_html_goto_page("/$ADMIN/issues/edit.php?Pub=$f_publication_id&Issue=$f_issue_number&Language=$f_language_id");
} else {
	camp_html_add_msg(getGS('The issue could not be added.'));
	camp_html_goto_page($backLink);
}
?>