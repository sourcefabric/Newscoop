B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageDictionary*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Edit keyword/infotype definition*>)
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
?>dnl
B_HEADER(<*Edit keyword/infotype definition*>)
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
	    query ("SELECT Definition FROM KeywordClasses WHERE IdDictionary=$Keyword AND IdClasses=$Class AND IdLanguage=$Language", 'q_kwdcls');
	    if ($NUM_ROWS){
		query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
		if ($NUM_ROWS) {
		    fetchRow($q_kwd);
		    fetchRow($q_cls);
		    fetchRow($q_lang);
		    fetchRow($q_kwdcls);
		
?>dnl
B_CURRENT
X_CURRENT(<*Keyword*>, <*<B><B><? pgetHVar($q_kwd,'Keyword'); ?></B>*>)
X_CURRENT(<*Infotype*>, <*<B><B><? pgetHVar($q_cls,'Name'); ?></B>*>)
X_CURRENT(<*Language*>, <*<B><? pgetHVar($q_lang,'Name'); ?></B>*>)
E_CURRENT


<P>
B_DIALOG(<*Edit keyword*>, <*POST*>, <*do_edit.php*>)
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Definition*>)
		<TEXTAREA NAME="cDefinition" ROWS="8" COLS="60"><? pgetHVar($q_kwdcls,'Definition'); ?></TEXTAREA>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Keyword" VALUE="<? pencHTML($Keyword); ?>">
		<INPUT TYPE="HIDDEN" NAME="Class" VALUE="<? pencHTML($Class); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? pencHTML($Language); ?>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/glossary/keyword/?Keyword=<? pencURL($Keyword); ?>&Language=<? pencHTML($Language); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
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
	<LI><? putGS('No such keyword/infotype definition.'); ?></LI>
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

