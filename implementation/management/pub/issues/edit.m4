B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Change issue details*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change issue details.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
?>dnl

B_HEADER(<*Change issue details*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? pencURL($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'publ');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    query ("SELECT Id, Name FROM Languages WHERE Id=$Language", 'q_lang');
	    fetchRow($q_pub);
	    fetchRow($q_lang);
	    fetchRow($publ);
?>dnl

B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($publ,'Number'); ?>. <? pgetHVar($publ,'Name'); ?> (<? pgetHVar($q_lang,'Name'); ?>)</B>*>)
E_CURRENT

<P>
B_DIALOG(<*Change issue details*>, <*POST*>, <*do_edit.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64" value="<? pgetHVar($publ,'Name'); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
	    <SELECT NAME="cLang">
<?
    query ("SELECT Id, Name FROM Languages", 'q_lang');
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) { 
	fetchRow($q_lang);
	pcomboVar(getVar($q_lang,'Id'),getVar($publ,'IdLanguage'),getVar($q_lang,'Name'));
    }
?>dnl	
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<? pencHTML($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<? pencHTML($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? pencHTML($Language); ?>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/pub/issues/?Pub=<? pencURL($Pub); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
        <LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl    

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
