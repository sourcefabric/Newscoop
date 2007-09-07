<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");

$Pub = Input::Get('Pub', 'int');
$Issue = Input::Get('Issue', 'int');
$Language = Input::Get('Language', 'int');
$f_target = Input::Get('f_target', 'string', 'index.php', true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));
	exit;
}

if (!$g_user->hasPermission('ManageIssue') || !$g_user->hasPermission('Publish')) {
	$BackLink ="/$ADMIN/issues/?Pub=$Pub&Language=$Language";
	camp_html_display_error(getGS('You do not have the right to change issues.'));
	exit;
}

$issueObj =& new Issue($Pub, $Language, $Issue);
$issueObj->setWorkflowStatus();

camp_html_goto_page("/$ADMIN/issues/$f_target?Pub=$Pub&Issue=$Issue&Language=$Language");

?>