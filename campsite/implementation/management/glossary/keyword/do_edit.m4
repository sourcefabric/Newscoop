B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageDictionary*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Changing keyword/infotype definition*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change definitions.*>)
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
    todef('cDefinition');
?>dnl
B_HEADER(<*Changing keyword/infotype definition*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Keyword infotypes*>, <*glossary/keyword/?Keyword=<? pencURL($Keyword); ?>&Language=<? pencURL($Language); ?>*>)
X_HBUTTON(<*Glossary*>, <*glossary/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT Keyword FROM Dictionary WHERE Id=$Keyword AND IdLanguage=$Language", 'q_kwd');
    if ($NUM_ROWS) {
	query ("SELECT Name FROM Classes WHERE Id=$Class AND IdLanguage=$Language", 'q_cls');
	if ($NUM_ROWS) {
	    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
	    if ($NUM_ROWS) { 
		fetchRow($q_kwd);
		fetchRow($q_cls);
		fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Keyword*>, <*<B><B><? pgetHVar($q_kwd,'Keyword'); ?></B>*>)
X_CURRENT(<*Infotype*>, <*<B><B><? pgetHVar($q_cls,'Name'); ?></B>*>)
X_CURRENT(<*Language*>, <*<B><? pgetHVar($q_lang,'Name'); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Changing keyword*>)
	X_MSGBOX_TEXT(<*
<?
    query ("UPDATE KeywordClasses SET Definition='".encHTML($cDefinition)."' WHERE IdDictionary=$Keyword AND IdClasses=$Class AND IdLanguage=$Language");
    if ($AFFECTED_ROWS > 0) { ?>dnl
		<LI><? putGS('The keyword has been changed.'); ?></LI>
X_AUDIT(<*93*>, <*getGS('Keyword $1 changed',getHVar($q_kwd,'Keyword'))*>)
<? } else { ?>dnl
		<LI><? putGS('The keyword could not be changed.'); ?><LI>
<? } ?>dnl
		*>)
<? if ($AFFECTED_ROWS > 0) { ?>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/glossary/keyword/?Keyword=<? pencURL($Keyword); ?>&Language=<? pencURL($Language); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<? } else { ?>
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/glossary/keyword/?Keyword=<? pencURL($Keyword); ?>&Language=<? pencURL($Language); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
	E_MSGBOX_BUTTONS
<? } ?>dnl
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

