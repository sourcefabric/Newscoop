<?php  
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files();
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/CampsiteInterface.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header('Location: /priv/logout.php');
	exit;
}
//$PublicationId = array_get_value($_REQUEST, 'PublicationId', 0);
//$IssueId = array_get_value($_REQUEST, 'IssueId', 0);
//$SectionId = array_get_value($_REQUEST, 'SectionId', 0);
//$InterfaceLanguageId = array_get_value($_REQUEST, 'InterfaceLanguageId', 0);
//$ArticleLanguageId = array_get_value($_REQUEST, 'ArticleLanguageId', 0);
//$ArticleId = array_get_value($_REQUEST, 'ArticleId', 0);
//$ImageId = array_get_value($_REQUEST, 'ImageId', 0);
//$ImageTemplateId = array_get_value($_REQUEST, 'ImageTemplateId', 0);

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
	header('Location: /priv/logout.php');
	exit;
}

$articleObj =& new Article($PublicationId, $IssueId, $SectionId, $ArticleLanguageId, $ArticleId);

// This file can only be accessed if the user has the right to change articles
// or the user created this article and it hasnt been published yet.
if (!$User->hasPermission('ChangeArticle') 
	|| (($articleObj->getUserId() == $User->getId()) && ($articleObj->getPublished() == 'N'))) {
	header('Location: /priv/logout.php');
	exit;		
}

ArticleImage::DisassociateImageFromArticle($ImageId, $ArticleId, $ImageTemplateId);

$logtext = getGS('Image $1 unlinked from $2', $ImageId, $ArticleId); 
Log::Message($logtext, $User->getUserName(), 42);


// Go back to article image list.
header('Location: '.CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, 'images/'));
exit;
?>