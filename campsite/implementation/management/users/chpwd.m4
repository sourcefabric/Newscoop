B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Change your password*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
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
		<INPUT TYPE="PASSWORD" NAME="cOldPass" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*New password*>)
		<INPUT TYPE="PASSWORD" NAME="cNewPass1" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Confirm new password*>)
		<INPUT TYPE="PASSWORD" NAME="cNewPass2" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/home.php"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
