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
$Pub = isset($_REQUEST['Pub'])?$_REQUEST['Pub']:0;
$Issue = isset($_REQUEST['Issue'])?$_REQUEST['Issue']:0;
$Section = isset($_REQUEST['Section'])?$_REQUEST['Section']:0;
$Language = isset($_REQUEST['Language'])?$_REQUEST['Language']:0;
$sLanguage = isset($_REQUEST['sLanguage'])?$_REQUEST['sLanguage']:0;
$Article = isset($_REQUEST['Article'])?$_REQUEST['Article']:0;
$Image = isset($_REQUEST['Image'])?$_REQUEST['Image']:0;

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

$logtext = getGS('Changed image properties of $1',$attributes['Description']); 
Log::Message($logtext, $User->getUserName(), 43);

// Go back to article image list.
header('Location: '.CampsiteInterface::ArticleUrl($articleObj, $sLanguage, 'images/'));

?>