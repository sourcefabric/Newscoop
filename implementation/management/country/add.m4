B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageCountries*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add New Country*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add countries.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new country*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Countries*>, <*country/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<P>
B_DIALOG(<*Add new country*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Code*>)
		<INPUT TYPE="TEXT" NAME="cCode" SIZE="2" MAXLENGTH="2">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
			<SELECT NAME="cLanguage">
<? query ("SELECT Id, Name FROM Languages ORDER BY Id", 'q_lng');
    for($loop=0;$loop<$NUM_ROWS;$loop++) {
	fetchRow($q_lng);
	print '<OPTION VALUE="'.getHVar($q_lng,'Id').'">'.getHVar($q_lng,'Name');
    } ?>dnl
			</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? todef('Language'); print encHTML($Language); ?>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
<? todef('Back'); ?>dnl
		<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<? print encHTML($Back); ?>">
<? if ($Back != "") { ?>dnl
		<A HREF="<? print $Back; ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/country/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
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

