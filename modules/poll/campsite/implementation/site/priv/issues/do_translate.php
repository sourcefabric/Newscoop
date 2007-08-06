<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");

// Check permissions
if (!$g_user->hasPermission('ManageIssue')) {
    camp_html_display_error(getGS('You do not have the right to add issues.'));
    exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int');
$f_issue_number = Input::Get('f_issue_number', 'int');
$f_language_id = Input::Get('f_language_id', 'int');

$f_name = trim(Input::Get('f_name'));
$f_url_name = trim(Input::Get('f_url_name'));
$f_new_language_id = Input::Get('f_new_language_id');

if (!Input::IsValid()) {
    camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));
    exit;
}
$publicationObj =& new Publication($f_publication_id);
$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);

$backLink = "/$ADMIN/issues/translate.php?Pub=$f_publication_id&Issue=$f_issue_number&Language=$f_language_id";
$created = false;

if ($f_new_language_id == 0) {
    camp_html_add_msg(getGS('You must select a language.'));
}

if ($f_name == "") {
    camp_html_add_msg(getGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'));
}

if ($f_url_name == "") {
    camp_html_add_msg(getGS('You must complete the $1 field.','<B>'.getGS('URL Name').'</B>'));
}

$errorMsg = camp_is_issue_conflicting($f_publication_id, $f_issue_number, $f_new_language_id, $f_url_name, false);
if ($errorMsg) {
    camp_html_add_msg($errorMsg);
}

if (camp_html_has_msgs()) {
    camp_html_goto_page($backLink);
}

$newIssue = $issueObj->copy(null, $issueObj->getIssueNumber(), $f_new_language_id);
if ($newIssue->exists()) {
    $newIssue->setName($f_name);
    $newIssue->setUrlName($f_url_name);
    camp_html_add_msg(getGS('The issue $1 has been successfuly added.','"<B>'.htmlspecialchars($f_name).'</B>"' ), "ok");
    camp_html_goto_page("/$ADMIN/issues/?Pub=$f_publication_id");
} else {
    camp_html_add_msg(getGS('The issue could not be added.'));
    camp_html_goto_page($backLink);
}
?>