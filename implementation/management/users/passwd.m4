B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Change user password*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change user passwords.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
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

<?
    todefnum('User');
    query ("SELECT UName FROM Users WHERE Id=$User", 'users');
    if ($NUM_ROWS) { 
	fetchRow($users);
    ?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<B><? pgetHVar($users,'UName'); ?></B>*>)
E_CURRENT

<P>
B_DIALOG(<*Change user password*>, <*POST*>, <*do_passwd.php*>)
	B_DIALOG_INPUT(<*Password*>)
		<INPUT TYPE="PASSWORD" NAME="cPass1" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Confirm password*>)
		<INPUT TYPE="PASSWORD" NAME="cPass2" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<? pencHTML($User); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/users/*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such user account.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
