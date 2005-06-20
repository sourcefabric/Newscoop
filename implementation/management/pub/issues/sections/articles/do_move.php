<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('Publish')) {
	CampsiteInterface::DisplayError(getGS("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users." ));
	exit;
}

// Get input
$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
$ArticleLanguage = Input::Get('ArticleLanguage', 'int', 0);
$MoveType = Input::Get('move', 'string', 'up_rel');
$sLanguage = Input::Get('sLanguage', 'int', 0, true);
$ArticleOffset = Input::Get('ArtOffs', 'int', 0, true);
$MoveToPosition = Input::Get('pos', 'int', 1, true);
$BackLink = Input::Get('Back', 'string', "/$ADMIN/pub/issues/sections/articles/index.php?Pub=$Pub&Issue=$Issue&Section=$Section&Language=$Language&ArtOffs=$ArticleOffset&sLanguage=$sLanguage", true);

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;	
}


$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Publication does not exist.'), $BackLink);
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Issue does not exist.'), $BackLink);
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Section does not exist.'), $BackLink);
	exit;	
}

$languageObj =& new Language($Language);

$articleObj =& new Article($Pub, $Issue, $Section, $ArticleLanguage, $Article);
if (!$articleObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Article does not exist.'), $BackLink);
	exit;	
}

switch ($MoveType) {
case 'up_rel':
	$articleObj->moveRelative('up', 1);
	break;
case 'down_rel':
	$articleObj->moveRelative('down', 1);
	break;
case 'abs':
	$articleObj->moveAbsolute($MoveToPosition);
	break;
default: ;
}

## added by sebastian
if (function_exists ("incModFile")) {
	incModFile();
}

header("Location: $BackLink");
exit;
?>