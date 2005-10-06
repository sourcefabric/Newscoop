<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Pub = Input::Get('Pub', 'int');
$Issue = Input::Get('Issue', 'int');
$Language = Input::Get('Language', 'int');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));
	exit;
}

if (!$User->hasPermission('ManageIssue') || !$User->hasPermission('Publish')) {
	$BackLink ="/$ADMIN/issues/?Pub=$Pub&Language=$Language";
	camp_html_display_error(getGS('You do not have the right to change issues.'));
	exit;
}

$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$issueObj->setPublished();

$logtext = getGS('Issue $1 Published: $2  changed status',$issueObj->getIssueId().'. '.$issueObj->getName().' ('.$issueObj->getLanguageName().')',$issueObj->getPublished()); 
Log::Message($logtext, $User->getUserName(), 14);

if ($issueObj->getPublished() == 'Y') {
	$t2=getGS('Not published');
	$t3=getGS('Published');
}
else {
	$t2=getGS('Published');
	$t3=getGS('Not published');
}

header("Location: /$ADMIN/issues/?Pub=" . $publicationObj->getPublicationId());

?>