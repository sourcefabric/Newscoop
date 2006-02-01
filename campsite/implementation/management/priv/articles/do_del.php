<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('DeleteArticle')) {
	camp_html_display_error(getGS("You do not have the right to delete articles."));
	exit;
}

// Get input
$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
$ArticleOffset = Input::Get('ArtOffs', 'int', 0, true);

$BackLink = Input::Get('Back', 'string', "/$ADMIN/articles/index.php?Pub=$Pub&Issue=$Issue&Section=$Section&Language=$Language&ArtOffs=$ArticleOffset", true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;	
}

$articleObj =& new Article($sLanguage, $Article);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('Article does not exist.'), $BackLink);
	exit;		
}

$articleObj->delete();

## added by sebastian
if (function_exists ("incModFile")) {
	incModFile();
}

header('Location: '.$BackLink."?Pub=$Pub&Issue=$Issue&Section=$Section&Language=$Language&ArtOffs=$ArticleOffset");
exit;
?>