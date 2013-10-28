<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('ManageIssue')) {
	camp_html_display_error($translator->trans('You do not have the right to add issues.', array(), 'issues'));
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int');
$f_issue_number = trim(Input::Get('f_issue_number', 'int'));

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid Input: $1', array('$1' => Input::GetErrorString()), 'issues'));
	exit;
}

$backLink = "/$ADMIN/issues/add_prev.php?Pub=$f_publication_id";
$publicationObj = new Publication($f_publication_id);
if (!$publicationObj->exists()) {
	camp_html_display_error($translator->trans("Publication does not exist."));
	exit;
}

$created = false;
$errorMsgs = array();
if ( empty($f_issue_number) || !is_numeric($f_issue_number) || ($f_issue_number <= 0) ) {
	camp_html_add_msg($translator->trans('You must fill in the $1 field.', array('$1' => '<B>'.$translator->trans('Number').'</B>')));
}

if (camp_html_has_msgs()) {
	camp_html_goto_page($backLink);
}
// check if the issue number already exists
$lastIssue = Issue::GetLastCreatedIssue($f_publication_id);
$existingIssues = Issue::GetIssues($f_publication_id, null, $f_issue_number, null, null, false, null, true);
if (count($existingIssues) > 0) {
	$conflictingIssue = array_pop($existingIssues);
	$conflictingIssueLink = "/$ADMIN/issues/edit.php?"
		."Pub=$f_publication_id"
		."&Issue=".$conflictingIssue->getIssueNumber()
		."&Language=".$conflictingIssue->getLanguageId();

	$errMsg = $translator->trans('The number must be unique for each issue in this publication of the same language.', array(), 'issues')."<br>".$translator->trans('The values you are trying to set conflict with issue $1$2. $3 ($4)$5.', array(
		'$1' => "<a href='$conflictingIssueLink' class='error_message' style='color:#E30000;'>",
		'$2' => $conflictingIssue->getIssueNumber(),
		'$3' => $conflictingIssue->getName(),
		'$4' => $conflictingIssue->getLanguageName(),
		'$5' => '</a>'), 'issues');
	camp_html_add_msg($errMsg);
	camp_html_goto_page($backLink);
}

$issueCopies = $lastIssue->copy(null, $f_issue_number);
if (!is_null($issueCopies)) {
	$issueCopy = $issueCopies[0];
	camp_html_add_msg($translator->trans("Issue created.", array(), 'issues'), "ok");
	camp_html_goto_page("/$ADMIN/issues/edit.php?Pub=$f_publication_id&Issue=".$issueCopy->getIssueNumber()
		   ."&Language=".$issueCopy->getLanguageId());
} else {
	camp_html_add_msg($translator->trans("The issue could not be added.", array(), 'issues'));
	camp_html_goto_page($backLink);
}
?>