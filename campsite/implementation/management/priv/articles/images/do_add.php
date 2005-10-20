<?php  

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_images");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Section.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('AddImage')) {
	camp_html_display_error(getGS('You do not have the right to add images' ));
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
$BackLink = Input::Get('BackLink', 'string', null, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;			
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);

// If the template ID is in use, dont add the image.
if (ArticleImage::TemplateIdInUse($Article, $ImageTemplateId)) {
	header('Location: '.camp_html_article_url($articleObj, $Language, 'images/index.php'));
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
	header('Location: '.camp_html_article_url($articleObj, $Language, 'images/index.php'));
	exit;
}

// Check if image was added successfully
if (!is_object($image)) {
	header('Location: '.camp_html_display_error($image, $BackLink));
	exit;	
}

ArticleImage::AddImageToArticle($image->getImageId(), $articleObj->getArticleId(), $ImageTemplateId);

$logtext = getGS('The image $1 has been added.', $attributes['Description']);
Log::Message($logtext, $User->getUserName(), 41);

// Go back to article image list.
$redirectLocation = camp_html_article_url($articleObj, $Language, 'images/edit.php')
	   ."&ImageId=".$image->getImageId()."&ImageTemplateId=$ImageTemplateId";
//echo $redirectLocation;
header("Location: $redirectLocation");
exit;
?>