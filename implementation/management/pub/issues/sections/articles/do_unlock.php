<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/priv/pub/issues/sections/articles/article_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /priv/logout.php");
	exit;
}
$PublicationId = Input::get('Pub', 'int', 0);
$IssueId  = Input::get('Issue', 'int', 0);
$SectionId = Input::get('Section', 'int', 0);
$InterfaceLanguageId = Input::get('Language', 'int', 0);
$ArticleLanguageId = Input::get('sLanguage', 'int', 0);
$ArticleId = Input::get('Article', 'int', 0);

if (!Input::isValid()) {
	header("Location: /priv/logout.php");
	exit;	
}

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