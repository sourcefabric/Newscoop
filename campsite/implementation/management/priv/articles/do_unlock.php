<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;	
}

$articleObj =& new Article($f_language_selected, $f_article_number);

// If the user does not have permission to change the article
// or they didnt create the article, give them the boot.
if (!$articleObj->userCanModify($User)) {
	camp_html_display_error(getGS("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only be changed by authorized users."));
	exit;	
}

$articleObj->unlock();
header('Location: '.camp_html_article_url($articleObj, $f_language_selected, "edit.php", "", "&Unlock=true"));
exit;

?>