B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageClasses*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Delete infotype*>)
<? if ($access == 0) { ?>dnl
		X_AD(<*You do not have the right to delete glossary infotypes.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Delete infotype*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary infotypes*>, <*infotype/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todefnum('Class');
    todefnum('Lang');
    query ("SELECT Name FROM Classes WHERE Id=$Class AND IdLanguage=$Lang", 'c');
?>dnl
<P>
<? if ($NUM_ROWS) { 
    fetchRow($c);
?>dnl
B_MSGBOX(<*Delete infotype*>)
	X_MSGBOX_TEXT(<*<LI><? putGS('Are you sure you want to delete the infotype $1?','<B>'.getHVar($c,'Name').'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="Class" VALUE="<? print encHTML(decS($Class)); ?>">
		<INPUT TYPE="HIDDEN" NAME="Lang" VALUE="<? print encHTML(decS($Lang)); ?>">
		<INPUT TYPE="HIDDEN" NAME="cName" VALUE="<? pgetHVar($c,'Name'); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/infotype/*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<? } else { ?>dnl
	<LI><? putGS('No such infotype.'); ?></LI>
<? } ?>dnl
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

