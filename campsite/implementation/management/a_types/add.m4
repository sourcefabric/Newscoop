B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageArticleTypes*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add new article type*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add article types.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new article type*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Article Types*>, <*a_types/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<P>
B_DIALOG(<*Add new article type*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="15" MAXLENGTH="15">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
<? todef('Back'); ?>dnl
		<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<? print encHTML($Back); ?>">
<? if ($Back != "") { ?>dnl
		<A HREF="<? print ($Back); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/a_types/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<? } ?>dnl
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
