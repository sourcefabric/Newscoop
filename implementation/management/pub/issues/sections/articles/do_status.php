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
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Status = strtoupper(Input::Get('Status', 'string', 'N'));
$BackLink = Input::Get('Back', 'string', "/$ADMIN/pub/issues/sections/articles/index.php", true);

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(array('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;	
}

if ( ($Status != 'N') && ($Status != 'S') && ($Status != 'Y')) {
	CampsiteInterface::DisplayError(array('Invalid input: $1', "Invalid status code:".$Status), $BackLink);
	exit;		
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	CampsiteInterface::DisplayError('Article does not exist.', $BackLink);
	exit;		
}
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	CampsiteInterface::DisplayError('Section does not exist.', $BackLink);	
}
$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	CampsiteInterface::DisplayError('Issue does not exist.', $BackLink);	
}
$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	CampsiteInterface::DisplayError('Publication does not exist.', $BackLink);	
}

$languageObj =& new Language($Language);

$access = false;
// A publisher can change the status in any way he sees fit.
// Someone who can change an article can submit/unsubmit articles.
// A user who owns the article may submit it.
if ($User->hasPermission('Publish') 
	|| ($User->hasPermission('ChangeArticle') && ($Status != 'Y'))
	|| ($articleObj->userCanModify($User) && ($Status == 'S') )) {
	$access = true;
}
if (!$access) {
	$errorStr = "You do not have the right to change this article status. Once submitted an article can only changed by authorized users.";
	CampsiteInterface::DisplayError($errorStr, $BackLink);
	exit;	
}

$articleObj->setPublished($Status);

$logtext = getGS('Article $1 status from $2. $3 from $4. $5 ($6) of $7 changed', $articleObj->getTitle(), $sectionObj->getSectionId(), $sectionObj->getName(), $issueObj->getIssueId(), $issueObj->getName(), $languageObj->getName(), $publicationObj->getName() ); 
Log::Message($logtext, $User->getUserName(), 35); 

header('Location: '.$BackLink);
exit;
?>