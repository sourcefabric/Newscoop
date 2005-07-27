<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('DeleteArticle')) {
	CampsiteInterface::DisplayError(getGS("You do not have the right to delete articles."));
	exit;
}

// Get input
$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
$ArticleOffset = Input::Get('ArtOffs', 'int', 0, true);

$BackLink = Input::Get('Back', 'string', "/$ADMIN/pub/issues/sections/articles/index.php?Pub=$Pub&Issue=$Issue&Section=$Section&Language=$Language&ArtOffs=$ArticleOffset", true);

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;	
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Article does not exist.'), $BackLink);
	exit;		
}

$articleObj->delete();

$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
$languageObj =& new Language($Language);
$sLanguageObj =& new Language($sLanguage);

$logtext = getGS('Article $1 ($2) deleted from $3. $4 from $5. $6 ($7) of $8',
	$articleObj->getTitle(), $sLanguageObj->getName(), 
	$sectionObj->getSectionId(), $sectionObj->getName(), 
	$issueObj->getIssueId(), $issueObj->getName(), 
	$languageObj->getName(), $publicationObj->getName() ); 
Log::Message($logtext, $User->getUserName(), 32);

## added by sebastian
if (function_exists ("incModFile")) {
	incModFile();
}

header('Location: '.$BackLink."?Pub=$Pub&Issue=$Issue&Section=$Section&Language=$Language&ArtOffs=$ArticleOffset");
exit;
?>