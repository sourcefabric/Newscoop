B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add new translation*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add issues.*>)
<? }
    query ("SELECT Name FROM Issues WHERE 1=0", 'q_iss');
    
?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
?>    
B_HEADER(<*Add new translation*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? pencURL($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) {
    	query ("SELECT Name FROM Issues WHERE IdPublication=$Pub AND Number=$Issue", 'q_iss');
	if ($NUM_ROWS) {
		$nriss=$NUM_ROWS;
        	fetchRow($q_pub);
//		fetchRow($q_iss);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
E_CURRENT

<?
    query ("SELECT Languages.Id, Languages.Name FROM Languages LEFT JOIN Issues ON Issues.IdPublication = $Pub AND Issues.Number=$Issue AND Issues.IdLanguage = Languages.Id WHERE Issues.IdPublication IS NULL ORDER BY Name", 'q_lang');
    if ($NUM_ROWS) { 
        $nrlang=$NUM_ROWS;
?>dnl
<P>
B_DIALOG(<*Add new translation*>, <*POST*>, <*do_translate.php*>)
	B_DIALOG_INPUT(<*Issue*>)
			<? $comma= 0;
    for($loop=0;$loop<$nriss;$loop++) {
	fetchRow($q_iss);
	if ($comma)
	    print ', ';
	pgetHVar($q_iss,'Name');
	$comma =1;
    }
?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
		<SELECT NAME="cLang"><?
	for($loop2=0;$loop2<$nrlang;$loop2++) { 
		fetchRow($q_lang);
		pcomboVar(getHVar($q_lang,'Id'),'',getHVar($q_lang,'Name'));
        }
	    ?>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="cPub" VALUE="<? pencHTML($Pub);?>">
		<INPUT TYPE="HIDDEN" NAME="cNumber" VALUE="<? pencHTML($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? pencHTML($Language); ?>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/pub/issues/?Pub=<? pencURL($Pub); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No more languages.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

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
