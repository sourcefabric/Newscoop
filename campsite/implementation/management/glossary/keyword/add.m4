B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageDictionary*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add keyword infotype*>)
<? if ($access == 0) { ?>dnl
        X_AD(<*You do not have the right to add keyword infotypes.*>)
<? }
    query ("SELECT Id, Name FROM Classes WHERE 1=0", 'q_cls');
?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('Keyword');
    todefnum('Language');
?>dnl
B_HEADER(<*Add keyword infotype*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Keyword infotype*>, <*glossary/keyword/?Keyword=<? print encURL($Keyword); ?>&Language=<? print encURL($Language); ?>*>)
X_HBUTTON(<*Glossary*>, <*glossary/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT Keyword FROM Dictionary WHERE Id=$Keyword AND IdLanguage=$Language", 'q_dict');
    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
    fetchRow($q_dict);
    fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Keyword*>, <*<B><? pgetHVar($q_dict,'Keyword'); ?></B>*>)
X_CURRENT(<*Language*>, <*<B><? pgetHVar($q_lang,'Name'); ?></B>*>)
E_CURRENT

<?
    query ("SELECT Id, Name FROM Classes WHERE IdLanguage=$Language", 'q_cls');
    if ($NUM_ROWS) { ?>dnl
<P>
B_DIALOG(<*Add keyword infotype*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Infotype*>)
	    <SELECT NAME="cClass" SIZE="5">
<?
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) { 
	fetchRow($q_cls);
	query ("SELECT COUNT(*) FROM KeywordClasses WHERE IdDictionary=$Keyword AND IdClasses=".getVar($q_cls,'Id')." AND IdLanguage=$Language", 'q_kwdcls');
	fetchRowNum($q_kwdcls);
	if (getNumVar($q_kwdcls,0) == 0)
		pcomboVar(getVar($q_cls,'Id'),'',getVar($q_cls,'Name'));
    }
?>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Keyword" VALUE="<? pencHTML($Keyword); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? pencHTML($Language); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/glossary/keyword/?Keyword=<? pencURL($Keyword); ?>&Language=<? pencURL($Language); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No infotypes available.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

