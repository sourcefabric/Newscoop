<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
$PublicationId = Input::Get('Pub', 'int', 0);
$IssueId  = Input::Get('Issue', 'int', 0);
$SectionId = Input::Get('Section', 'int', 0);
$InterfaceLanguageId = Input::Get('Language', 'int', 0);
$ArticleLanguageId = Input::Get('sLanguage', 'int', 0);
$ArticleId = Input::Get('Article', 'int', 0);

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;	
}

$articleObj =& new Article($PublicationId, $IssueId, $SectionId, $ArticleLanguageId, $ArticleId);

// If the user does not have permission to change the article
// or they didnt create the article, give them the boot.
if (!$articleObj->userCanModify($User)) {
	CampsiteInterface::DisplayError(getGS("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users."));
	exit;	
}

$articleObj->unlock();
header('Location: '.CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, "edit.php", "", "&Unlock=true"));
exit;

?>