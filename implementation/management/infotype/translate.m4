B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageClasses*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Translate infotype*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add glossary infotypes.*>)
<? }
    query ("SELECT Name FROM Classes WHERE 1=0", 'c');
?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Translate infotype*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary infotypes*>, <*infotype/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todefnum('Infotype');
    query ("SELECT Name FROM Classes WHERE Id=$Class", 'c');
    $nr=$NUM_ROWS;
    if ($NUM_ROWS) {
	$NUM_ROWS= 0;
	query ("SELECT Languages.Id, Languages.Name FROM Languages LEFT JOIN Classes ON Classes.Id = $Class AND Classes.IdLanguage = Languages.Id WHERE Classes.Id IS NULL ORDER BY Name", 'languages');
	$nr_lang=$NUM_ROWS;
	if ($NUM_ROWS) { ?>dnl
<P>
B_DIALOG(<*Translate keyword*>, <*POST*>, <*do_translate.php*>)
	B_DIALOG_INPUT(<*Keyword infotype*>)
<?
    $comma= 0;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($c);
	if ($comma)
	    print ',';
	pgetHVar($c,'Name');
	$comma= 1;
    }
?>dnl	
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Translation*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
		<SELECT NAME="cLang"><?
		
		    for($loop=0;$loop<$nr_lang;$loop++) {
			fetchRow($languages);
			pcomboVar(getVar($languages,'Id'),'',getVar($languages,'Name'));
		    }
		?></SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="cId" VALUE="<? print encHTML($Class); ?>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/infotype/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No more languages.'); ?></LI>
</BLOCKQUOTE>
<? }
} else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such keyword infotype.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

