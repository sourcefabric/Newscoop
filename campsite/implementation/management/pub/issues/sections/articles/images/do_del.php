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
$PublicationId = isset($_REQUEST['PublicationId'])?$_REQUEST['PublicationId']:0;
$IssueId = isset($_REQUEST['IssueId'])?$_REQUEST['IssueId']:0;
$SectionId = isset($_REQUEST['SectionId'])?$_REQUEST['SectionId']:0;
$ArticleLanguageId = isset($_REQUEST['ArticleLanguageId'])?$_REQUEST['ArticleLanguageId']:0;
$ArticleId = isset($_REQUEST['ArticleId'])?$_REQUEST['ArticleId']:0;
$ImageId = isset($_REQUEST['ImageId'])?$_REQUEST['ImageId']:0;
$InterfaceLanguageId = isset($_REQUEST['InterfaceLanguageId'])?$_REQUEST['InterfaceLanguageId']:0;

$articleObj =& new Article($PublicationId, $IssueId, $SectionId, $ArticleLanguageId, $ArticleId);

// This file can only be accessed if the user has the right to change articles
// or the user created this article and it hasnt been published yet.
if (!$User->hasPermission('ChangeArticle') || !$User->hasPermission('DeleteImage')
	|| (($articleObj->getUserId() == $User->getId()) && ($articleObj->getPublished() == 'N'))) {
	header('Location: /priv/logout.php');
	exit;		
}

$imageObj =& new Image($ImageId);
$imageObj->delete();
$logtext = getGS('Image $1 deleted', $imageObj->getImageId()); 
Log::Message($logtext, $User->getUserName(), 42);

// Go back to article image list.
header('Location: '.CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, 'images/'));

?>