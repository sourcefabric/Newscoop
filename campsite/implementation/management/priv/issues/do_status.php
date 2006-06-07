<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");

$Pub = Input::Get('Pub', 'int');
$Issue = Input::Get('Issue', 'int');
$Language = Input::Get('Language', 'int');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));
	exit;
}

if (!$g_user->hasPermission('ManageIssue') || !$g_user->hasPermission('Publish')) {
	$BackLink ="/$ADMIN/issues/?Pub=$Pub&Language=$Language";
	camp_html_display_error(getGS('You do not have the right to change issues.'));
	exit;
}

$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$issueObj->setWorkflowStatus();

header("Location: /$ADMIN/issues/?Pub=" . $publicationObj->getPublicationId());

?>