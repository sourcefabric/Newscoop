<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('AddArticle')) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS("You do not have the right to add articles." )));
	exit;
}

// Get input
$Pub = Input::get('Pub', 'int', 0);
$Issue = Input::get('Issue', 'int', 0);
$Section = Input::get('Section', 'int', 0);
$Language = Input::get('Language', 'int', 0);
$cName = trim(Input::get('cName', 'string', ''));
$cType = trim(Input::get('cType', 'string', ''));
$cLanguage = trim(Input::get('cLanguage', 'int', 0));
$cFrontPage = Input::get('cFrontPage', 'string', 'N', true);
$cSectionPage = Input::get('cSectionPage', 'string', 'N', true);
$cKeywords = Input::get('cKeywords', 'string', '', true);

// Check input
if ($cName == "") {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>')));
	exit;
}
    
if ($cType == "") {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('You must select an article type.')));
	exit;
}
    
if ($cLanguage == "") {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('You must select a language.')));
	exit;
}

if (!Input::isValid()) {
	header("Location: /$ADMIN/logout.php");
	exit;	
}

$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Publication does not exist.')));
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Issue does not exist.')));
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Section does not exist.')));
	exit;	
}

$languageObj =& new Language($Language);

// Create article
$articleObj =& new Article($Pub, $Issue, $Section, $cLanguage);
$articleObj->create($cType, $cName);
$articleObj->setOnSection(($cSectionPage == "on"));
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