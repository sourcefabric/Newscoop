<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/priv/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /priv/logout.php");
	exit;
}

if (!$User->hasPermission("AddArticle")) {
	header("Location: /priv/ad.php?ADReason=".urlencode(getGS("You do not have the right to add articles." ))); 	
	exit;
}

$Pub = Input::get('Pub', 'int', 0);
$Issue = Input::get('Issue', 'int', 0);
$Section = Input::get('Section', 'int', 0);
$Language = Input::get('Language', 'int', 0);
$Article = Input::get('Article', 'int', 0);
//$sLanguage = Input::get('sLanguage', 'int', 0);
$sLanguage = $Language;
$DestPublication = Input::get('destination_publication', 'int', 0);
$DestIssue = Input::get('destination_issue', 'int', 0);
$DestSection = Input::get('destination_section', 'int', 0);
$BackLink = Input::get('Back', 'string', '/priv/pub/issues/sections/articles/index.php', true);

if (!Input::isValid()) {
	header("Location: /priv/logout.php");
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
Log::Message($logtext, $User->getUserName(), 31);

header("Location: ".CampsiteInterface::ArticleUrl($articleCopy, $Language, "edit.php", $Back));
exit;
?>