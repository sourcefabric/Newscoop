B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteDictionary*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting keyword*>)
<? if ($access == 0) { ?>dnl
		X_AD(<*You do not have the right to delete keywords.*>)
<? query ("SELECT 1", 's');
} ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Deleting keyword*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary*>, <*glossary/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todefnum('Keyword');
    todefnum('Language');

    query ("SELECT Keyword FROM Dictionary WHERE Id=$Keyword AND IdLanguage=$Language", 'q_dic');
    if ($NUM_ROWS) {
	query ("SELECT COUNT(*) FROM KeywordClasses WHERE IdDictionary=$Keyword AND IdLanguage=$Language", 'q_kwdcls');
	fetchRowNum($q_kwdcls);
	if (getNumVar($q_kwdcls,0) == 0) {
	    $AFFECTED_ROWS= 0;
	    fetchRow($q_dic);
	    query ("DELETE FROM Dictionary WHERE Id=$Keyword AND IdLanguage=$Language");
	    ?>dnl
<P>
B_MSGBOX(<*Deleting keyword*>)
<? if ($AFFECTED_ROWS > 0) { ?>
	X_MSGBOX_TEXT(<*<LI><? putGS('The keyword has been deleted.'); ?></LI>*>)
X_AUDIT(<*82*>, <*getGS('Keyword $1 deleted',getHVar($q_dic,'Keyword'))*>)
<? } else { ?>
	X_MSGBOX_TEXT(<*<LI><? putGS('The keyword could not be deleted.'); ?></LI>*>)
<? } ?>
	B_MSGBOX_BUTTONS
<? if ($AFFECTED_ROWS > 0) { ?>
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/glossary/*>)
<? } else { ?>
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/glossary/*>)
<? } ?>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('You must delete keyword infotypes first.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
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
