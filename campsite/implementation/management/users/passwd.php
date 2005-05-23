<FORM NAME="dialog" METHOD="POST" ACTION="do_passwd.php" >
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B><?php putGS("Change password"); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Old Password"); ?>:</TD>
		<TD>
		<INPUT TYPE="PASSWORD" class="input_text" NAME="cOldPass" SIZE="16" MAXLENGTH="32">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Password"); ?>:</TD>
		<TD>
		<INPUT TYPE="PASSWORD" class="input_text" NAME="cPass1" SIZE="16" MAXLENGTH="32">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Confirm password"); ?>:</TD>
		<TD>
		<INPUT TYPE="PASSWORD" class="input_text" NAME="cPass2" SIZE="16" MAXLENGTH="32">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<?php  pencHTML($User); ?>">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
		<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/admin/users/'">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
