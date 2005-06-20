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
$isReader = $uType == 'Readers' ? 'Y' : 'N';

$rightsFields = array('cManagePub'=>'N', 'cDeletePub'=>'N', 'cManageIssue'=>'N', 'cDeleteIssue'=>'N',
	'cManageSection'=>'N', 'cDeleteSection'=>'N', 'cAddArticle'=>'N', 'cChangeArticle'=>'N',
	'cDeleteArticle'=>'N', 'cAddImage'=>'N', 'cChangeImage'=>'N', 'cDeleteImage'=>'N',
	'cManageTempl'=>'N', 'cDeleteTempl'=>'N', 'cManageUsers'=>'N', 'cManageReaders'=>'N',
	'cManageSubscriptions'=>'N', 'cDeleteUsers'=>'N', 'cManageUserTypes'=>'N', 'cManageArticleTypes'=>'N',
	'cDeleteArticleTypes'=>'N', 'cManageLanguages'=>'N', 'cDeleteLanguages'=>'N', 'cMailNotify'=>'N',
	'cManageCountries'=>'N', 'cDeleteCountries'=>'N', 'cViewLogs'=>'N', 'cManageLocalizer'=>'N',
	'cManageIndexer'=>'N', 'cPublish'=>'N', 'cManageTopics'=>'N', 'cEditorBold'=>'N', 'cEditorItalic'=>'N',
	'cEditorUnderline'=>'N', 'cEditorUndoRedo'=>'N', 'cEditorCopyCutPaste'=>'N', 'cEditorImage'=>'N',
	'cEditorTextAlignment'=>'N', 'cEditorFontColor'=>'N', 'cEditorFontSize'=>'N', 'cEditorFontFace'=>'N',
	'cEditorTable'=>'N', 'cEditorSuperscript'=>'N', 'cEditorSubscript'=>'N', 'cEditorStrikethrough'=>'N',
	'cEditorIndent'=>'N', 'cEditorListBullet'=>'N', 'cEditorListNumber'=>'N', 'cEditorHorizontalRule'=>'N',
	'cEditorSourceView'=>'N', 'cEditorEnlarge'=>'N', 'cEditorTextDirection'=>'N', 'cEditorLink'=>'N',
	'cEditorSubhead'=>'N');
foreach ($rightsFields as $field=>$value) {
	$val = Input::Get($field, 'string', 'off');
	if ($val == 'on')
		$rightsFields[$field] = 'Y';
	$queryStr .= ", `" . substr($field, 1) . "` = '" . $rightsFields[$field] . "'";
}

$queryStr = "UPDATE UserPerm SET " . substr($queryStr, 2) ." WHERE IdUser = $userId";
echo "query: $queryStr";
query($queryStr);

if ($AFFECTED_ROWS >= 0) {
	$logtext = getGS('Permissions for $1 changed',$editUser->getUserName());
	Log::Message($logtext, $editUser->getUserName(), 55);
}

header("Location: /$ADMIN/users/edit.php?uType=Staff&User=$userId");

?>
