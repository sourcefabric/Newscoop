<?php  
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/pub/issues/sections/articles/images");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$PublicationId = Input::get('PublicationId', 'int', 0);
$IssueId = Input::get('IssueId', 'int', 0);
$SectionId = Input::get('SectionId', 'int', 0);
$InterfaceLanguageId = Input::get('InterfaceLanguageId', 'int', 0);
$ArticleLanguageId = Input::get('ArticleLanguageId', 'int', 0);
$ArticleId = Input::get('ArticleId', 'int', 0);
$ImageId = Input::get('ImageId', 'int', 0);
$ImageTemplateId = Input::get('ImageTemplateId', 'int', 0);

// Check input
if (!Input::isValid()) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$articleObj =& new Article($PublicationId, $IssueId, $SectionId, $ArticleLanguageId, $ArticleId);

// This file can only be accessed if the user has the right to change articles
// or the user created this article and it hasnt been published yet.
$access = false;
if ($User->hasPermission('ChangeArticle') 
	|| (($articleObj->getUserId() == $User->getId()) && ($articleObj->getPublished() == 'N'))) {
	$access = true;
}
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;		
}

ArticleImage::RemoveImageFromArticle($ImageId, $ArticleId, $ImageTemplateId);

$logtext = getGS('Image $1 unlinked from $2', $ImageId, $ArticleId); 
Log::Message($logtext, $User->getUserName(), 42);


// Go back to article image list.
header('Location: '.CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, 'images/'));
exit;
?>