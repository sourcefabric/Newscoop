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
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/CampsiteInterface.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header('Location: /priv/logout.php');
	exit;
}

//if (!IsValidInput(array(
//	'Pub' => 'int',
//	'Issue' => 'int',
//	'Section' => 'int',
//	'Language' => 'int',
//	'sLanguage' => 'int',
//	'Article' => 'int',
//	'Image' => 'int',
//	'cDescription' => 'string',
//	'cPhotographer' => 'string',
//	'cPlace' => 'string',
//	'cDate' => 'string'))) {
//	header('Location: /priv/logout.php');
//	exit;
//}
//$Pub = array_get_value($_REQUEST, 'Pub', 0);
//$Issue = array_get_value($_REQUEST, 'Issue', 0);
//$Section = array_get_value($_REQUEST, 'Section', 0);
//$Language = array_get_value($_REQUEST, 'Language', 0);
//$sLanguage = array_get_value($_REQUEST, 'sLanguage', 0);
//$Article = array_get_value($_REQUEST, 'Article', 0);
//$Image = array_get_value($_REQUEST, 'Image', 0);
//$Description = array_get_value($_REQUEST, 'cDescription', 'None');
//$Photographer = array_get_value($_REQUEST, 'cPhotographer', '');
//$Place = array_get_value($_REQUEST, 'cPlace', '');
//$Date = array_get_value($_REQUEST, 'cDate', '');
//

$Pub = Input::get('Pub', 'int', 0);
$Issue = Input::get('Issue', 'int', 0);
$Section = Input::get('Section', 'int', 0);
$Language = Input::get('Language', 'int', 0);
$sLanguage = Input::get('sLanguage', 'int', 0);
$Article = Input::get('Article', 'int', 0);
$Image = Input::get('Image', 'int', 0);
$ImageTemplateId = Input::get('cNumber', 'int', 0);
$Description = Input::get('cDescription', 'string', 'None', true);
$Photographer = Input::get('cPhotographer', 'string', '', true);
$Place = Input::get('cPlace', 'string', '', true);
$Date = Input::get('cDate', 'string', '', true);

if (!Input::isValid()) {
	header('Location: /priv/logout.php');
	exit;	
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$languageObj =& new Language($Language);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);

// This file can only be accessed if the user has the right to change articles
// or the user created this article and it hasnt been published yet.
if (!$User->hasPermission('ChangeArticle') 
	|| (($articleObj->getUserId() == $User->getId()) && ($articleObj->getPublished() == 'N'))) {
	header('Location: /priv/logout.php');
	exit;		
}

$imageObj =& new Image($Image);
$attributes = array();
$attributes['Description'] = $_REQUEST['cDescription'];
if (trim($attributes['Description']) == '') {
	$attributes['Description'] = 'None';
}
$attributes['Photographer'] = $_REQUEST['cPhotographer'];
$attributes['Place'] = $_REQUEST['cPlace'];
$attributes['Date'] = $_REQUEST['cDate'];
$imageObj->update($attributes);
ArticleImage::SetTemplateId($Article, $Image, $ImageTemplateId);

$logtext = getGS('Changed image properties of $1',$attributes['Description']); 
Log::Message($logtext, $User->getUserName(), 43);

// Go back to article image list.
header('Location: '.CampsiteInterface::ArticleUrl($articleObj, $sLanguage, 'images/'));

?>