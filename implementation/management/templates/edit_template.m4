B_HTML
INCLUDE_PHP_LIB(<*..*>)dnl
B_DATABASE

<? todefnum('What'); ?>dnl

CHECK_BASIC_ACCESS
<? if ($What != 0) { ?>dnl
CHECK_ACCESS(<*ManageTempl*>)dnl
<? } ?>dnl

B_HEAD
	X_EXPIRES
	X_TITLE(<*Templates management*>)

<?
    if ($access == 0) {
	if ($What) { ?>dnl
	X_AD(<*You do not have the right to change default templates.*>)
<? } else { ?>dnl
	X_LOGOUT
<? }
    }
?>dnl
E_HEAD

<? if ($access) { 

SET_ACCESS(<*mta*>, <*ManageTempl*>)
SET_ACCESS(<*dta*>, <*DeleteTempl*>)
?>dnl
B_STYLE
E_STYLE

B_BODY

<? todef('Path'); ?>dnl
<? todef('Name'); ?>dnl

B_HEADER(<*Edit template*>)
B_HEADER_BUTTONS

X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Path*>, <*<B><A HREF="<? pencHTML(decURL($Path)); ?>"><? pencHTML(decURL($Path)); ?></A></B>*>)
X_CURRENT(<*Template*>, <*<B><? pencHTML(decURL($Name)); ?></B>*>)
E_CURRENT


B_DIALOG(<*Edit template*>, <*POST*>, <*do_edit.php*>)

	B_DIALOG_BUTTONS
<? if ($dta != 0) { ?>
	<INPUT TYPE="IMAGE" SRC="X_ROOT/img/button/save.gif" BORDER="0" ALT="OK"></A>
	<A HREF="<? pencHTML(decS($Path)); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<? } else { ?>
	<A HREF="<? pencHTML(decS($Path)); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } ?>
	E_DIALOG_BUTTONS

<?
	$filename = "$DOCUMENT_ROOT".decURL($Path)."$Name";
	$fd = fopen ($filename, "r");
	$contents = fread ($fd, filesize ($filename));
	fclose ($fd);
?>

	<TR><TD><TEXTAREA ROWS="28" COLS="90" NAME="cField" WRAP="NO"><? p(decS($contents)) ?></TEXTAREA></TD></TR>
	<INPUT TYPE="HIDDEN" NAME="Path" VALUE="<? p($Path); ?>">
	<INPUT TYPE="HIDDEN" NAME="Name" VALUE="<? p($Name); ?>">

	B_DIALOG_BUTTONS
<? if ($dta != 0) { ?>
	<INPUT TYPE="IMAGE" SRC="X_ROOT/img/button/save.gif" BORDER="0" ALT="OK"></A>
	<A HREF="<? pencHTML(decS($Path)); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<? } else { ?>
	<A HREF="<? pencHTML(decS($Path)); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } ?>
	E_DIALOG_BUTTONS
E_DIALOG



X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML


