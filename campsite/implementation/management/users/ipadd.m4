B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add new IP address group*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add IP address groups.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new IP address group*>)
B_HEADER_BUTTONS
X_HBUTTON(<*IP Access List*>, <*users/ipaccesslist.php?User=<? p($User); ?>*>)
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<P>
B_DIALOG(<*Add new IP address group*>, <*POST*>, <*do_ipadd.php*>)
        <INPUT TYPE="HIDDEN" NAME="User" VALUE="<? p($User); ?>" SIZE="3" MAXLENGTH="3">.
	B_DIALOG_INPUT(<*Start IP*>)
		<INPUT TYPE="TEXT" NAME="cStartIP1" SIZE="3" MAXLENGTH="3">.
		<INPUT TYPE="TEXT" NAME="cStartIP2" SIZE="3" MAXLENGTH="3">.
		<INPUT TYPE="TEXT" NAME="cStartIP3" SIZE="3" MAXLENGTH="3">.
		<INPUT TYPE="TEXT" NAME="cStartIP4" SIZE="3" MAXLENGTH="3">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Number of addresses*>)
		<INPUT TYPE="TEXT" NAME="cAddresses" SIZE="10" MAXLENGTH="10">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/users/ipaccesslist.php?User=<? p($User); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
