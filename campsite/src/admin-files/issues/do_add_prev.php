<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");

// Check permissions
if (!$g_user->hasPermission('ManageIssue')) {
	camp_html_display_error(getGS('You do not have the right to add issues.'));
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int');
$f_issue_number = trim(Input::Get('f_issue_number', 'int'));

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));
	exit;
}

$backLink = "/$ADMIN/issues/add_prev.php?Pub=$f_publication_id";
$publicationObj = new Publication($f_publication_id);
if (!$publicationObj->exists()) {
	camp_html_display_error(getGS("Publication does not exist."));
	exit;
}

$created = false;
$errorMsgs = array();
if ( empty($f_issue_number) || !is_numeric($f_issue_number) || ($f_issue_number <= 0) ) {
	camp_html_add_msg(getGS('You must fill in the $1 field.','<B>'.getGS('Number').'</B>'));
}

if (camp_html_has_msgs()) {
	camp_html_goto_page($backLink);
}
// check if the issue number already exists
$lastIssue = Issue::GetLastCreatedIssue($f_publication_id);
$existingIssues = Issue::GetIssues($f_publication_id, null, $f_issue_number, null, null, null, true);
if (count($existingIssues) > 0) {
	$conflictingIssue = array_pop($existingIssues);
	$conflictingIssueLink = "/$ADMIN/issues/edit.php?"
		."Pub=$f_publication_id"
		."&Issue=".$conflictingIssue->getIssueNumber()
		."&Language=".$conflictingIssue->getLanguageId();

	$errMsg = getGS('The number must be unique for each issue in this publication of the same language.')."<br>".getGS('The values you are trying to set conflict with issue "$1$2. $3 ($4)$5".',
		"<a href='$conflictingIssueLink' class='error_message' style='color:#E30000;'>",
		$conflictingIssue->getIssueNumber(),
		$conflictingIssue->getName(),
		$conflictingIssue->getLanguageName(),
		'</a>');
	camp_html_add_msg($errMsg);
	camp_html_goto_page($backLink);
}

$issueCopies = $lastIssue->copy(null, $f_issue_number);
if (!is_null($issueCopies)) {
	$issueCopy = array_pop($issueCopies);
	camp_html_add_msg(getGS("Issue created."), "ok");
	$logtext = getGS('New issue $1 from $2 in publication $3', $f_issue_number,
					 $lastIssue->getIssueNumber(), $publicationObj->getName());
	Log::Message($logtext, $g_user->getUserId(), 11);
	camp_html_goto_page("/$ADMIN/issues/edit.php?Pub=$f_publication_id&Issue=".$issueCopy->getIssueNumber()
		   ."&Language=".$issueCopy->getLanguageId());
} else {
	camp_html_add_msg(getGS("The issue could not be added."));
	camp_html_goto_page($backLink);
}
?>