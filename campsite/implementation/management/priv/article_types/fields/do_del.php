<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_type_fields");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleType.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('DeleteArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to delete article type fields."));
	exit;
}

$articleTypeName = Input::Get('AType');
$fieldName = Input::Get('Field');


$field =& new ArticleTypeField($articleTypeName, $fieldName);
if ($field->exists()) {
	$field->delete();
	$logtext = getGS('Article type field $1 deleted', $Field); 
	Log::Message($logtext, $User->getUserName(), 72);
}
header("Location: /$ADMIN/article_types/fields/?AType=".urlencode($articleTypeName));
exit;
