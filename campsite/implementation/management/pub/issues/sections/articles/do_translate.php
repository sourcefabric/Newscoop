<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Pub = Input::get('Pub', 'int', 0);
$Issue = Input::get('Issue', 'int', 0);
$Section = Input::get('Section', 'int', 0);
$Language = Input::get('Language', 'int', 0);
$sLanguage = Input::get('sLanguage', 'int', 0);
$cName = trim(Input::get('cName'));
$cLanguage = Input::get('cLanguage');
$cKeywords = Input::get('cKeywords');
$BackLink = Input::get('Back', 'string', "/$ADMIN/pub/issues/sections/articles/", true);

if (!Input::isValid()) {
	header("Location: /$ADMIN/logout.php");
	exit;	
}
$languageObj =& new Language($cLanguage);
if (!$languageObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('You must select a language.')));
	exit;	
}

$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	header("Location: /$ADMIN/logout.php");
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	header("Location: /$ADMIN/logout.php");
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	header("Location: /$ADMIN/logout.php");
	exit;		
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Article does not exist.')));
	exit;
}

$access = false;
if ($User->hasPermission('ChangeArticle') || (($articleObj->getUserId() == $User->getId()) && ($articleObj->getPublished() == 'N'))) {
	$access= true;
}
if (!$access) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users.")));
	exit;	
}

$articleCopy =& $articleObj->createTranslation($cLanguage, $User->getId(), $cName);

$logtext = getGS('Article $1 added to $2. $3 from $4. $5 of $6', 
	$cName, $sectionObj->getSectionId(), $sectionObj->getName(), 
	$issueObj->getIssueId(), $issueObj->getName(), $publicationObj->getName() ); 
Log::Message($logtext, $User->getUserName(), 31);
    
header('Location: '.CampsiteInterface::ArticleUrl($articleCopy, $Language, 'edit.php')); 
exit;
?>