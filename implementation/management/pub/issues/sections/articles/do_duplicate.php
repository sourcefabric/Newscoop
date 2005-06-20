<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}


$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
//$sLanguage = Input::Get('sLanguage', 'int', 0);
$sLanguage = $Language;
$DestPublication = Input::Get('destination_publication', 'int', 0);
$DestIssue = Input::Get('destination_issue', 'int', 0);
$DestSection = Input::Get('destination_section', 'int', 0);
$BackLink = Input::Get('Back', 'string', "/$ADMIN/pub/issues/sections/articles/index.php", true);

if (!$User->hasPermission("AddArticle")) {
	CampsiteInterface::DisplayError(getGS("You do not have the right to add articles."), $BackLink);
	exit;
}

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS("Invalid input: $1", Input::GetErrorString()), $BackLink);
	exit;	
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
$issueObj =& new Issue($Pub, $Language, $Issue);
$publicationObj =& new Publication($Pub);

$articleCopy = $articleObj->copy($DestPublication, $DestIssue, $DestSection, $User->getId());

$logtext = getGS('Article $1 added to $2. $3 from $4. $5 of $6',
	$articleCopy->getName(), $sectionObj->getSectionId(),
	$sectionObj->getName(), $issueObj->getIssueId(),
	$issueObj->getName(), $publicationObj->getName() );
Log::Message($logtext, $User->getUserName(), 155);

header("Location: ".CampsiteInterface::ArticleUrl($articleCopy, $Language, "edit.php", $BackLink));
exit;
?>