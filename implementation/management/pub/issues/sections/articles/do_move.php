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
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users." )));
	exit;
}

// Get input
$Pub = Input::get('Pub', 'int', 0);
$Issue = Input::get('Issue', 'int', 0);
$Section = Input::get('Section', 'int', 0);
$Language = Input::get('Language', 'int', 0);
$Article = Input::get('Article', 'int', 0);
$ArticleLanguage = Input::get('ArticleLanguage', 'int', 0);
$MoveType = Input::get('move', 'string', 'up_rel');
$sLanguage = Input::get('sLanguage', 'int', 0, true);
$ArticleOffset = Input::get('ArtOffs', 'int', 0, true);
$MoveToPosition = Input::get('pos', 'int', 1, true);
$BackLink = Input::get('Back', 'string', "/$ADMIN/pub/issues/sections/articles/index.php?Pub=$Pub&Issue=$Issue&Section=$Section&Language=$Language&ArtOffs=$ArticleOffset&sLanguage=$sLanguage", true);

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

$articleObj =& new Article($Pub, $Issue, $Section, $ArticleLanguage, $Article);
if (!$articleObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Article does not exist.')));
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