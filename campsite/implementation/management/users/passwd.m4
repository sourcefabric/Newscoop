INCLUDE_PHP_LIB(<*$ADMIN_DIR/users*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_TITLE(<*Change user password*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change user passwords.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Change user password*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    todefnum('User');
    query ("SELECT UName FROM Users WHERE Id=$User", 'users');
    if ($NUM_ROWS) { 
	fetchRow($users);
    ?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<B><?php  pgetHVar($users,'UName'); ?></B>*>)
E_CURRENT

<P>
B_DIALOG(<*Change user password*>, <*POST*>, <*do_passwd.php*>)
	B_DIALOG_INPUT(<*Password*>)
		<INPUT TYPE="PASSWORD" class="input_text" NAME="cPass1" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Confirm password*>)
		<INPUT TYPE="PASSWORD" class="input_text" NAME="cPass2" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<?php  pencHTML($User); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/users/*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such user account.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
