INCLUDE_PHP_LIB(<*$ADMIN_DIR/users*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE(<*Change your password*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Change your password*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<P>
B_DIALOG(<*Change your password*>, <*POST*>, <*do_chpwd.php*>)
	B_DIALOG_INPUT(<*Old password*>)
		<INPUT TYPE="PASSWORD" class="input_text" NAME="cOldPass" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*New password*>)
		<INPUT TYPE="PASSWORD" class="input_text" NAME="cNewPass1" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Confirm new password*>)
		<INPUT TYPE="PASSWORD" class="input_text" NAME="cNewPass2" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/home.php*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
