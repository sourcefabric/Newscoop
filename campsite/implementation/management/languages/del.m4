B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteLanguages*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Delete language*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete languages.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Delete language*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Languages*>, <*languages/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<? 
    todefnum('Language');
    query ("SELECT * FROM Languages WHERE Id=$Language", 'q_lang');
    if ($NUM_ROWS) { 
	fetchRow($q_lang);
    ?>dnl
<P>
B_MSGBOX(<*Delete language*>)
	X_MSGBOX_TEXT(<*<LI><? putGS('Are you sure you want to delete the language $1?','<B>'.getHVar($q_lang,'Name').'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? print encHTML($Language); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		SUBMIT(<*No*>, <*No*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such language.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

