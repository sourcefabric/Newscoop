<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Log.php");

list($access, $User) = check_basic_access($_REQUEST);

read_user_common_parameters(); // $uType, $userOffs, $lpp, search parameters
$uType = 'Staff';
compute_user_rights($User, &$canManage, &$canDelete);
if (!$canManage) {
	CampsiteInterface::DisplayError(getGS('You do not have the right to change user account information.'));
	exit;
}

$userId = Input::Get('User', 'int', 0);
$editUser = new User($userId);
if ($editUser->getUserName() == '') {
	CampsiteInterface::DisplayError(getGS('No such user account.'));
	exit;
}
$typeParam = 'uType=' . urlencode($uType);
$isReader = $uType == 'Subscribers' ? 'Y' : 'N';

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
	$queryStr .= ", `$field` = '" . $rightsFields[$field] . "'";
}

$queryStr = "UPDATE UserPerm SET " . substr($queryStr, 2) ." WHERE IdUser = $userId";
query($queryStr);

if ($AFFECTED_ROWS >= 0) {
	$logtext = getGS('Permissions for $1 changed',$editUser->getUserName());
	Log::Message($logtext, $editUser->getUserName(), 55);
}

header("Location: /$ADMIN/users/edit.php?uType=Staff&User=$userId");

?>
