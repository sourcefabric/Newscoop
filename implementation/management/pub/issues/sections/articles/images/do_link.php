<?php  
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files();
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/CampsiteInterface.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header('Location: /priv/logout.php');
	exit;
}
$PublicationId = isset($_REQUEST['PublicationId'])?$_REQUEST['PublicationId']:0;
$IssueId = isset($_REQUEST['IssueId'])?$_REQUEST['IssueId']:0;
$SectionId = isset($_REQUEST['SectionId'])?$_REQUEST['SectionId']:0;
$InterfaceLanguageId = isset($_REQUEST['InterfaceLanguageId'])?$_REQUEST['InterfaceLanguageId']:0;
$ArticleLanguageId = isset($_REQUEST['ArticleLanguageId'])?$_REQUEST['ArticleLanguageId']:0;
$ArticleId = isset($_REQUEST['ArticleId'])?$_REQUEST['ArticleId']:0;
$ImageId = isset($_REQUEST['ImageId'])?$_REQUEST['ImageId']:0;
$ImageTemplateId = isset($_REQUEST['ImageTemplateId'])?$_REQUEST['ImageTemplateId']:0;

// Check input
if (!is_numeric($ArticleId) || ($ArticleId <= 0)
	|| !is_numeric($ImageId) || ($ImageId <= 0)
	|| !is_numeric($ImageTemplateId) || ($ImageTemplateId <= 0)) {
	header('Location: /priv/logout.php');
	exit;
}

$articleObj =& new Article($PublicationId, $IssueId, $SectionId, $ArticleLanguageId, $ArticleId);
if (!$articleObj->exists()) {
	header('Location: /priv/logout.php');
	exit;	
}

$imageObj =& new Image($ImageId);
if (!$imageObj->exists()) {
	header('Location: /priv/logout.php');
	exit;	
}

// This file can only be accessed if the user has the right to change articles
// or the user created this article and it hasnt been published yet.
if (!$User->hasPermission('ChangeArticle') 
	|| (($articleObj->getUserId() == $User->getId()) && ($articleObj->getPublished() == 'N'))) {
	header('Location: /priv/logout.php');
	exit;		
}

ArticleImage::AssociateImageWithArticle($ImageId, $ArticleId, $ImageTemplateId);

$logtext = getGS('Image $1 linked to $2', $ImageId, $ArticleId); 
Log::Message($logtext, $User->getUserName(), 42);

// Go back to article image list.
header('Location: '.CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, 'images/'));
exit;
?>