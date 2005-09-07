<?php  
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/pub/issues/sections/articles/images");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Section.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
$Image = Input::Get('Image', 'int', 0);
$ImageTemplateId = Input::Get('cNumber', 'int', 0, true);
$Description = Input::Get('cDescription', 'string', 'None', true);
$Photographer = Input::Get('cPhotographer', 'string', '', true);
$Place = Input::Get('cPlace', 'string', '', true);
$Date = Input::Get('cDate', 'string', '', true);

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;	
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);

// This file can only be accessed if the user has the right to change articles
// or the user created this article and it hasnt been published yet.
if (!$articleObj->userCanModify($User)) {	
	CampsiteInterface::DisplayError(getGS('You do not have the right to change the article.'));
	exit;		
}

$imageObj =& new Image($Image);
$attributes = array();
$attributes['Description'] = $Description;
if (trim($attributes['Description']) == '') {
	$attributes['Description'] = 'None';
}
$attributes['Photographer'] = $Photographer;
$attributes['Place'] = $Place;
$attributes['Date'] = $Date;
$view = Input::Get('view', 'string', 'thumbnail', true);
$imageObj->update($attributes);
if (is_numeric($ImageTemplateId) && ($ImageTemplateId > 0)) {
	ArticleImage::SetTemplateId($Article, $Image, $ImageTemplateId);
}

$logtext = getGS('Changed image properties of $1',$attributes['Description']); 
Log::Message($logtext, $User->getUserName(), 43);

$imageNav =& new ImageNav(CAMPSITE_IMAGEARCHIVE_IMAGES_PER_PAGE, $view);
$ref = CampsiteInterface::ArticleUrl($articleObj, $Language, 'images/search.php')
	. $imageNav->getKeywordSearchLink();

// Go back to article image list.
header("Location: $ref");

?>