<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/priv/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /priv/logout.php");
	exit;
}

$Pub = Input::get('Pub', 'int', 0);
$Issue = Input::get('Issue', 'int', 0);
$Section = Input::get('Section', 'int', 0);
$Language = Input::get('Language', 'int', 0);
$Article = Input::get('Article', 'int', 0);
$sLanguage = Input::get('sLanguage', 'int', 0);
$Status = Input::get('Status', 'string', 'N');
$Back = Input::get('Back', 'string', '/priv/pub/issues/sections/articles/index.php', true);

if (!Input::isValid()) {
	header("Location: /priv/logout.php");
	exit;	
}

if ( ($Status != 'N') && ($Status != 'S') && ($Status != 'Y')) {
	header("Location: /priv/logout.php");
	exit;		
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
$issueObj =& new Issue($Pub, $Language, $Issue);
$languageObj =& new Language($Language);
$publicationObj =& new Publication($Pub);

$userIsArticleOwner = ($User->getId() == $articleObj->getUserId());
$articleIsNew = ($articleObj->getPublished() == 'N');

$access = false;
// A publisher can change the status in any way he sees fit.
// A user who owns the article may submit it.
if ($User->hasPermission('Publish') || ($userIsArticleOwner && $articleIsNew && ($Status == 'S') )) {
	$access = true;
}
if (!$access) {
	header("Location: /priv/ad.php?ADReason=".urlencode(getGS("You do not have the right to change this article status. Once submitted an article can only changed by authorized users." )));
	exit;	
}

$articleObj->setPublished($Status);

$logtext = getGS('Article $1 status from $2. $3 from $4. $5 ($6) of $7 changed', $articleObj->getTitle(), $sectionObj->getSectionId(), $sectionObj->getName(), $issueObj->getIssueId(), $issueObj->getName(), $languageObj->getName(), $publicationObj->getName() ); 
Log::Message($logtext, $User->getUserName(), 35); 

header('Location: '.$Back);
exit;
?>