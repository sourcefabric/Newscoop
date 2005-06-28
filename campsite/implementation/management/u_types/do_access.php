<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/u_types/utypes_common.php");

list($access, $User) = check_basic_access($_REQUEST);
$canManage = $User->hasPermission('ManageUserTypes');
$canDelete = $canManage;
if (!$canManage) {
	$error = getGS("You do not have the right to change user type permissions.");
	CampsiteInterface::DisplayError($error);
	exit;
}

$uType = Input::Get('UType', 'string', '');
if ($uType != '') {
	$userType = new UserType($uType);
	if ($userType->getName() == '') {
		CampsiteInterface::DisplayError(getGS('No such user type.'));
		exit;
	}
} else {
	CampsiteInterface::DisplayError(getGS('No such user type.'));
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
	if ($val == 'on')
		$rightsFields[$field] = 'Y';
	$userType->setProperty($field, $rightsFields[$field], false);
}
if ($userType->commit()) {
	$logtext = getGS('User type $1 changed permissions', $userType->getName());
	Log::Message($logtext, $userType->getName(), 123);
}

header("Location: /$ADMIN/u_types/access.php?UType=$uType");

?>
