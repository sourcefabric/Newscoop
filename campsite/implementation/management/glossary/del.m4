B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteDictionary*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Delete keyword*>)
<? if ($access == 0) { ?>dnl
		X_AD(<*You do not have the right to delete keywords.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Delete keyword*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary*>, <*glossary/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todefnum('Keyword');
    todefnum('Language');
    query ("SELECT Keyword FROM Dictionary WHERE Id=$Keyword AND IdLanguage=$Language", 'q_dict');
    if ($NUM_ROWS) {
	query ("SELECT COUNT(*) FROM KeywordClasses WHERE IdDictionary=$Keyword AND IdLanguage=$Language", 'q_kwdcls');
	fetchRowNum($q_kwdcls);
	if (getNumVar($q_kwdcls,0) == 0) { 
	    fetchRow($q_dict);
	?>dnl
<P>
B_MSGBOX(<*Delete keyword*>)
	X_MSGBOX_TEXT(<*<LI><? putGS('Are you sure you want to delete the keyword $1?','<B>'.getHVar($q_dict,'Keyword').'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="Keyword" VALUE="<? print encHTML($Keyword); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? print encHTML($Language); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/glossary/*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('You must delete keyword infotypes first.'); ?></LI>
</BLOCKQUOTE>
<? }
    } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such keyword.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
