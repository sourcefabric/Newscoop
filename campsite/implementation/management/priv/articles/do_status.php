<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

//$Pub = Input::Get('Pub', 'int', 0);
//$Issue = Input::Get('Issue', 'int', 0);
//$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
//$sLanguage = Input::Get('sLanguage', 'int', 0);
$Status = strtoupper(Input::Get('Status', 'string', 'N'));
$BackLink = Input::Get('Back', 'string', "/$ADMIN/articles/index.php", true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;	
}

if ( ($Status != 'N') && ($Status != 'S') && ($Status != 'Y')) {
	camp_html_display_error(getGS('Invalid input: $1', "Invalid status code:".$Status), $BackLink);
	exit;		
}

$articleObj =& new Article($sLanguage, $Article);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('Article does not exist.'), $BackLink);
	exit;		
}
//$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
//if (!$sectionObj->exists()) {
//	camp_html_display_error(getGS('Section does not exist.'), $BackLink);	
//}
//$issueObj =& new Issue($Pub, $Language, $Issue);
//if (!$issueObj->exists()) {
//	camp_html_display_error(getGS('Issue does not exist.'), $BackLink);	
//}
//$publicationObj =& new Publication($Pub);
//if (!$publicationObj->exists()) {
//	camp_html_display_error(getGS('Publication does not exist.'), $BackLink);	
//}

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
	$errorStr = getGS("You do not have the right to change this article status. Once submitted an article can only be changed by authorized users.");
	camp_html_display_error($errorStr, $BackLink);
	exit;	
}

$articleObj->setPublished($Status);

header('Location: '.$BackLink);
exit;
?>