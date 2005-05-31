<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");

list($access, $User) = check_basic_access($_REQUEST);
compute_user_rights($User, &$canManage, &$canDelete);
if (!$canManage) {
	CampsiteInterface::DisplayError('You do not have the right to change user account information.');
	exit;
}

$userId = Input::Get('User', 'int', 0);
$editUser = new User($userId);
if ($editUser->getUserName() == '') {
	CampsiteInterface::DisplayError('No such user account.');
	exit;
}

?>
<form name="dialog" method="post" action="do_passwd.php" >
<input type="hidden" name="uType" value="<?php echo $uType; ?>">
<input type="hidden" name="User" value="<?php echo $editUser->getId(); ?>">
<table border="0" cellspacing="0" cellpadding="6" class="table_input" align="center" width="95%">
	<TR>
		<TD COLSPAN="2">
			<B><?php putGS("Change password"); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
<?php if ($userId == $User->getId()) { ?>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Old Password"); ?>:</TD>
		<TD>
		<INPUT TYPE="PASSWORD" class="input_text" NAME="oldPassword" SIZE="16" MAXLENGTH="32">
		</TD>
	</TR>
<?php } ?>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Password"); ?>:</TD>
		<TD>
		<INPUT TYPE="PASSWORD" class="input_text" NAME="password" SIZE="16" MAXLENGTH="32">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Confirm password"); ?>:</TD>
		<TD>
		<INPUT TYPE="PASSWORD" class="input_text" NAME="passwordConf" SIZE="16" MAXLENGTH="32">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<?php echo $editUser->getId(); ?>">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
		<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/users/?" . get_user_urlparams(); ?>'">
		</div>
		</td>
	</tr>
</table>
</form>
