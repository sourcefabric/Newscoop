B_HTML
INCLUDE_PHP_LIB(<*../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSection*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add new section*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add sections.*>)
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
B_HEADER(<*Add new section*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_language');

	    fetchRow($q_iss);
	    fetchRow($q_pub);
	    fetchRow($q_language);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($q_iss,'Number'); ?>. <? pgetHVar($q_iss,'Name'); ?> (<? pgetHVar($q_language,'Name'); ?>)</B>*>)
E_CURRENT

<?
    query ("SELECT MAX(Number) + 1 FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language", 'q_nr');
    fetchRowNum($q_nr);
    
    if (getNumVar($q_nr,0) == "")
	$nr= 1;
    else
	$nr=getNumVar($q_nr,0);
?>dnl
<P>
B_DIALOG(<*Add new section*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Number*>)
		<INPUT TYPE="TEXT" NAME="cNumber" VALUE="<? p($nr); ?>" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<? p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<? p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? p($Language); ?>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
