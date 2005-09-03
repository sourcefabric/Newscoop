<?php  

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/articles/images");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Section.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('AddImage')) {
	CampsiteInterface::DisplayError(getGS('You do not have the right to add images' ));
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
$ImageTemplateId = Input::Get('cNumber', 'int', 0);
$cDescription = Input::Get('cDescription');
$cPhotographer = Input::Get('cPhotographer');
$cPlace = Input::Get('cPlace');
$cDate = Input::Get('cDate');
$cURL = Input::Get('cURL', 'string', '', true);

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;			
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);

// If the template ID is in use, dont add the image.
if (ArticleImage::TemplateIdInUse($Article, $ImageTemplateId)) {
	header('Location: '.CampsiteInterface::ArticleUrl($articleObj, $Language, 'images/index.php'));
	exit;
}

$attributes = array();
$attributes['Description'] = $cDescription;
$attributes['Photographer'] = $cPhotographer;
$attributes['Place'] = $cPlace;
$attributes['Date'] = $cDate;
if (!empty($cURL)) {
	$image =& Image::OnAddRemoteImage($cURL, $attributes, $User->getId());
}
elseif (!empty($_FILES['cImage'])) {
	$image =& Image::OnImageUpload($_FILES['cImage'], $attributes, $User->getId());
}
else {
	header('Location: '.CampsiteInterface::ArticleUrl($articleObj, $Language, 'images/index.php'));
	exit;
}

// Check if image was added successfully
if (!is_object($image)) {
	header('Location: '.CampsiteInterface::ArticleUrl($articleObj, $Language, 'images/index.php'));
	exit;	
}

ArticleImage::AddImageToArticle($image->getImageId(), $articleObj->getArticleId(), $ImageTemplateId);

$logtext = getGS('The image $1 has been added.', $attributes['Description']);
Log::Message($logtext, $User->getUserName(), 41);

// Go back to article image list.
header('Location: '.CampsiteInterface::ArticleUrl($articleObj, $Language, 'images/'));

?>