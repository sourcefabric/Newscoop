<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_article_code = Input::Get('f_article_code', 'string', 0);
$f_translation_title = trim(Input::Get('f_translation_title'));
$f_translation_language = Input::Get('f_translation_language');
list($articleNumber, $languageId) = split("_", $f_article_code);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;	
}

$articleObj =& new Article($languageId, $articleNumber);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('Article does not exist.'));
	exit;
}

if (!$articleObj->userCanModify($User)) {
	$errorStr = getGS('You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only be changed by authorized users.');
	camp_html_display_error($errorStr);
	exit;	
}

$articleCopy =& $articleObj->createTranslation($f_translation_language, $User->getId(), $f_translation_title);

header('Location: '.camp_html_article_url($articleCopy, $languageId, 'edit.php')); 
exit;
?>