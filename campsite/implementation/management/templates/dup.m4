B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTempl*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<* Duplicate template*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to create templates.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<? todef('Path'); ?>dnl
<? todef('Name'); ?>dnl

B_HEADER(<*Duplicate template*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Templates*>, <*templates/?Path=<? pencURL(decS($Path)); ?>*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Path*>, <*<B><? pencHTML(decURL($Path)); ?></B>*>)
 X_CURRENT(<*Template*>, <*<B><? pencHTML(decURL($Name)); ?></B>*>)
E_CURRENT

B_DIALOG(<*Duplicate template*>, <*POST*>, <*do_dup.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="cPath" VALUE="<? pencHTML(decS($Path)); ?>">
		<INPUT TYPE="HIDDEN" NAME="Name" VALUE="<? pencHTML(decS($Name)); ?>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="<? pencHTML(decS($Path)); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
