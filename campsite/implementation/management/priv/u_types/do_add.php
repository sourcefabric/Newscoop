<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/user_types/utypes_common.php");

list($access, $User) = check_basic_access($_REQUEST);
$canManage = $User->hasPermission('ManageUserTypes');
if (!$canManage) {
	$error = getGS("You do not have the right to change user type permissions.");
	camp_html_display_error($error);
	exit;
}

$uType = Input::Get('Name', 'string', '');
if ($uType != '') {
	$userType = new UserType($uType);
	if ($userType->exists()) {
		$errMsg = getGS("An user type with the name '$1' already exists.", $uType);
		camp_html_display_error($errMsg);
		exit;
	}
} else {
	camp_html_display_error(getGS('You must complete the $1 field.', getGS('Name')));
	exit;
}

$rightsFields = array('ManagePub'=>'N', 'DeletePub'=>'N', 'ManageIssue'=>'N', 'DeleteIssue'=>'N',
	'ManageSection'=>'N', 'DeleteSection'=>'N', 'AddArticle'=>'N', 'ChangeArticle'=>'N',
	'DeleteArticle'=>'N', 'AddImage'=>'N', 'ChangeImage'=>'N', 'DeleteImage'=>'N',
	'ManageTempl'=>'N', 'DeleteTempl'=>'N', 'ManageUsers'=>'N', 'ManageReaders'=>'N',
	'ManageSubscriptions'=>'N', 'DeleteUsers'=>'N', 'ManageUserTypes'=>'N', 'ManageArticleTypes'=>'N',
	'DeleteArticleTypes'=>'N', 'ManageLanguages'=>'N', 'DeleteLanguages'=>'N', 'MailNotify'=>'N',
	'ManageCountries'=>'N', 'DeleteCountries'=>'N', 'ViewLogs'=>'N', 'ManageLocalizer'=>'N',
	'ManageIndexer'=>'N', 'Publish'=>'N', 'ManageTopics'=>'N', 'EditorBold'=>'N', 'EditorItalic'=>'N',
	'EditorUnderline'=>'N', 'EditorUndoRedo'=>'N', 'EditorCopyCutPaste'=>'N', 'EditorImage'=>'N',
	'EditorTextAlignment'=>'N', 'EditorFontColor'=>'N', 'EditorFontSize'=>'N', 'EditorFontFace'=>'N',
	'EditorTable'=>'N', 'EditorSuperscript'=>'N', 'EditorSubscript'=>'N', 'EditorStrikethrough'=>'N',
	'EditorIndent'=>'N', 'EditorListBullet'=>'N', 'EditorListNumber'=>'N', 'EditorHorizontalRule'=>'N',
	'EditorSourceView'=>'N', 'EditorEnlarge'=>'N', 'EditorTextDirection'=>'N', 'EditorLink'=>'N',
	'EditorSubhead'=>'N');
foreach ($rightsFields as $field=>$value) {
	$val = Input::Get($field, 'string', 'off');
	if ($val == 'on') {
		$rightsFields[$field] = 'Y';
	}
}
if ($userType->create($rightsFields)) {
	$logtext = getGS('User type $1 added', $uType);
	Log::Message($logtext, $uType, 121);
}

header("Location: /$ADMIN/user_types/access.php?UType=$uType");

?>
