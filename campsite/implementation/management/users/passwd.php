<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");

list($access, $User) = check_basic_access($_REQUEST);

read_user_common_parameters(); // $uType, $userOffs, $lpp, search parameters
verify_user_type();
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
<FORM NAME="dialog" METHOD="POST" ACTION="do_passwd.php" >
<input type="hidden" name="uType" value="<?php echo $uType; ?>">
<input type="hidden" name="User" value="<?php echo $editUser->getId(); ?>">
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" ALIGN="CENTER">
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
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
