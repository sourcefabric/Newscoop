<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/priv/pub/issues/sections/articles/article_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /priv/logout.php");
	exit;
}
$PublicationId = array_get_value($_REQUEST, "Pub", 0);
$IssueId  = array_get_value($_REQUEST, "Issue", 0);
$SectionId = array_get_value($_REQUEST, "Section", 0);
$InterfaceLanguageId = array_get_value($_REQUEST, "Language", 0);
$ArticleLanguageId = array_get_value($_REQUEST, "sLanguage", 0);
$ArticleId = array_get_value($_REQUEST, "Article", 0);

$articleObj =& new Article($PublicationId, $IssueId, $SectionId, $ArticleLanguageId, $ArticleId);

// If the user does not have permission to change the article
// or they didnt create the article, give them the boot.
if (!$User->hasPermission('ChangeArticle') 
	&& (($articleObj->getUserId() != $User->getId()) 
		|| ($articleObj->getPublished() != 'N'))) {
	header("Location: /priv/logout.php");
	exit;	
}

$articleObj->unlock();
header('Location: '.CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, "edit.php"));
exit;

?>