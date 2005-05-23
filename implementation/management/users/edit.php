<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");

list($access, $User) = check_basic_access($_REQUEST);

read_user_common_parameters(); // $uType, $userOffs, $lpp, search parameters
verify_user_type();
compute_user_rights($User, &$canManage, &$canDelete);

$typeParam = 'uType=' . urlencode($uType);
$isReader = $uType == 'Readers' ? 'Y' : 'N';

$userId = Input::Get('User', 'int', 0);
$editUser = new User($userId);
if ($editUser->getUserName() == '') {
	CampsiteInterface::DisplayError('No such user account.',$_SERVER['REQUEST_URI']);
	exit;
}

?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
<TR>
	<TD class="page_title"><?php  putGS("Change user account information"); ?></TD>
	<TD align="right"><A HREF="/<?php echo $ADMIN; ?>/users/?<?php echo get_user_urlparams(); ?>" class="breadcrumb" ><?php putGS($uType);  ?></A></TD>
</TR>
</TABLE>
<table border="0">
<tr>
	<td rowspan="2" valign="top">
		<?php require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/info.php"); ?>
	</td>
	<td valign="top" height="1%">
<?php
if ($uType == 'Staff')
	require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/rights.php");
if ($uType == 'Readers')
	require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/subscriptions.php");
?>
	</td>
</tr>
<tr>
	<td valign="top">
<?php
if ($uType == 'Readers')
	require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/ipaccesslist.php");
?>
	</td>
</tr>

</table>
<?php CampsiteInterface::CopyrightNotice(); ?>
