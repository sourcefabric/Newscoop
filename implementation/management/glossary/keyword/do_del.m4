B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteDictionary*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Unlinking infotype from keyword*>)
<? if ($access == 0) { ?>dnl
        X_AD(<*You do not have the right to unlink infotypes from keywords.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('Keyword');
    todefnum('Class');
    todefnum('Language');
?>dnl
B_HEADER(<*Unlinking infotype from keyword*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Keyword Infotypes*>, <*glossary/keyword/?Keyword=<? pencURL($Keyword); ?>&Language=<? pencURL($Language); ?>*>)
X_HBUTTON(<*Glossary*>, <*glossary/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT Keyword FROM Dictionary WHERE Id=$Keyword AND IdLanguage=$Language", 'q_kwd');
    if ($NUM_ROWS) {
	$NUM_ROWS= 0;
	query ("SELECT Name FROM Classes WHERE Id=$Class AND IdLanguage=$Language", 'q_cls');
	if ($NUM_ROWS) {
	    $NUM_ROWS= 0;
	    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
	    if ($NUM_ROWS) { 
		fetchRow($q_kwd);
		fetchRow($q_lang);
	    ?>dnl
B_CURRENT
X_CURRENT(<*Keyword*>, <*<B><B><? pgetHVar($q_kwd,'Keyword'); ?></B>*>)
X_CURRENT(<*Language*>, <*<B><? pgetHVar($q_lang,'Name'); ?></B>*>)
E_CURRENT


<P>
B_MSGBOX(<*Deleting keyword infotype*>)
<?
    query ("DELETE FROM KeywordClasses WHERE IdDictionary=$Keyword AND IdClasses=$Class AND IdLanguage=$Language");
    if ($AFFECTED_ROWS > 0) { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('The infotype has been deleted.'); ?></LI>*>)
<? } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('The infotype could not be deleted.'); ?></LI>*>)
<? } ?>dnl
	B_MSGBOX_BUTTONS
<? if ($AFFECTED_ROWS > 0) { ?>dnl
		<A HREF="X_ROOT/glossary/keyword/?Keyword=<? pencURL($Keyword); ?>&Language=<? pencURL($Language); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/glossary/keyword/?Keyword=<? pencURL($Keyword); ?>&Language=<? pencURL($Language); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such language.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such infotype.'); ?></LI>
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
