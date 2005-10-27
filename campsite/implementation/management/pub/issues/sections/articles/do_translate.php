<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
$ArticleLanguage = Input::Get('ArticleLanguage', 'int', 0);
$cName = trim(Input::Get('cName'));
$cLanguage = Input::Get('cLanguage');
$cKeywords = Input::Get('cKeywords');
$BackLink = Input::Get('Back', 'string', "/$ADMIN/pub/issues/sections/articles/", true);

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;	
}
$languageObj =& new Language($cLanguage);
if (!$languageObj->exists()) {
	CampsiteInterface::DisplayError(getGS('You must select a language.'), $BackLink);
	exit;	
}

$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Publication does not exist.'), $BackLink);
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	CampsiteInterface::DisplayError(getGS('No such issue.'), $BackLink);
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	CampsiteInterface::DisplayError(getGS('No such section.'), $BackLink);
	exit;		
}

$articleObj =& new Article($Pub, $Issue, $Section, $ArticleLanguage, $Article);
if (!$articleObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Article does not exist.'), $BackLink);
	exit;
}

if (!$articleObj->userCanModify($User)) {
	$errorStr = getGS('You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users.');
	CampsiteInterface::DisplayError($errorStr, $BackLink);
	exit;	
}

$articleCopy = $articleObj->createTranslation($cLanguage, $User->getId(), $cName);

$logtext = getGS('Article $1 added to $2. $3 from $4. $5 of $6', 
	$cName, $sectionObj->getSectionId(), $sectionObj->getName(), 
	$issueObj->getIssueId(), $issueObj->getName(), $publicationObj->getName() ); 
Log::Message($logtext, $User->getUserName(), 31);
    
header('Location: '.CampsiteInterface::ArticleUrl($articleCopy, $Language, 'edit.php')); 
exit;
?>