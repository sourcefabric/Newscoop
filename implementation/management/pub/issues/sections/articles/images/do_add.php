<?php  

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files();
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Section.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/CampsiteInterface.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header('Location: /priv/logout.php');
	exit;
}
if (!$User->hasPermission('AddImage')) {
	header('Location: /priv/ad.php?ADReason='.encURL(getGS('You do not have the right to add images' ))); 
	exit;
}

if (!IsValidInput(array(
	"PublicationId" => "int",
	"IssueId" => "int",
	"SectionId" => "int",
	"InterfaceLanguageId" => "int",
	"ArticleLanguageId" => "int",
	"ArticleId" => "int",
	"cNumber" => "int"))) {
	header('Location: /priv/logout.php');
	exit;		
}
	
$PublicationId = array_get_value($_REQUEST, 'PublicationId', 0);
$IssueId = array_get_value($_REQUEST, 'IssueId', 0);
$SectionId = array_get_value($_REQUEST, 'SectionId', 0);
$InterfaceLanguageId = array_get_value($_REQUEST, 'InterfaceLanguageId', 0);
$ArticleLanguageId = array_get_value($_REQUEST, 'ArticleLanguageId', 0);
$ArticleId = array_get_value($_REQUEST, 'ArticleId', 0);
$ImageTemplateId = array_get_value($_REQUEST, 'cNumber', 0);

$articleObj =& new Article($PublicationId, $IssueId, $SectionId, $ArticleLanguageId, $ArticleId);

// If the template ID is in use, dont add the image.
if (ArticleImage::TemplateIdInUse($ArticleId, $ImageTemplateId)) {
	header('Location: '.CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, 'images/index.php'));
	exit;
}

$attributes = array();
$attributes['Description'] = $_REQUEST['cDescription'];
$attributes['Photographer'] = $_REQUEST['cPhotographer'];
$attributes['Place'] = $_REQUEST['cPlace'];
$attributes['Date'] = $_REQUEST['cDate'];
if (!empty($_REQUEST['cURL'])) {
	$image =& Image::OnAddRemoteImage($_REQUEST['cURL'], $attributes, $User->getId());
}
elseif (!empty($_REQUEST['cImage'])) {
	$image =& Image::OnImageUpload($_FILES['cImage'], $attributes, $User->getId());
}
else {
	header('Location: '.CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, 'images/index.php'));
	exit;
}

// Check if image was added successfully
if (!is_object($image)) {
	header('Location: '.CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, 'images/index.php'));
	exit;	
}

ArticleImage::AssociateImageWithArticle($image->getImageId(), $articleObj->getArticleId(), $ImageTemplateId);

$logtext = getGS('The image $1 has been added.', $attributes['Description']);
Log::Message($logtext, $User->getUserName(), 41);

// Go back to article image list.
header('Location: '.CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, 'images/'));

?>