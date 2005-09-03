<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('AddArticle')) {
	CampsiteInterface::DisplayError(getGS("You do not have the right to add articles."));
	exit;
}

// Get input
$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$cName = trim(Input::Get('cName', 'string', ''));
$cType = trim(Input::Get('cType', 'string', ''));
$cLanguage = trim(Input::Get('cLanguage', 'int', 0));
$cFrontPage = Input::Get('cFrontPage', 'string', 'N', true);
$cSectionPage = Input::Get('cSectionPage', 'string', 'N', true);
$cKeywords = Input::Get('cKeywords', 'string', '', true);

// Check input
if ($cName == "") {
	CampsiteInterface::DisplayError(getGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'));
	exit;
}
    
if ($cType == "") {
	CampsiteInterface::DisplayError(getGS('You must select an article type.'));
	exit;
}
    
if ($cLanguage == "") {
	CampsiteInterface::DisplayError(getGS('You must select a language.'));
	exit;
}

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;	
}

$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Publication does not exist.'));
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Issue does not exist.'));
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Section does not exist.'));
	exit;	
}

$languageObj =& new Language($Language);

// Create article
$articleObj =& new Article($Pub, $Issue, $Section, $cLanguage);
$articleObj->create($cType, $cName);
$articleObj->setOnSectionPage(($cSectionPage == "on"));
$articleObj->setOnFrontPage(($cFrontPage == "on"));
$articleObj->setUserId($User->getId());
$articleObj->setIsPublic(true);
$articleObj->setKeywords($cKeywords);

$logtext = getGS('Article $1 added to $2. $3 from $4. $5 of $6',
	$cName, $sectionObj->getSectionId(), 
	$sectionObj->getName(), $issueObj->getIssueId(),
	$issueObj->getName(), $publicationObj->getName()); 
Log::Message($logtext, $User->getUserName(), 31);

## added by sebastian
if (function_exists ("incModFile")) {
	incModFile();
}

header("Location: ".CampsiteInterface::ArticleUrl($articleObj, $Language, "edit.php"));
exit;
?>