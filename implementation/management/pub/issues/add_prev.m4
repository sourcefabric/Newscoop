B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Copy previous issue*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add issues.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<? todefnum('Pub'); ?>dnl
B_HEADER(<*Copy previous issue*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? pencURL($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'publ');
    if ($NUM_ROWS) {
	fetchRow($publ);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($publ,'Name'); ?></B>*>)
E_CURRENT

<?
    query ("SELECT MAX(Number) FROM Issues WHERE IdPublication=$Pub", 'q_nr');
    fetchRowNum($q_nr);
    if (getNumVar($q_nr,0) == "") { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No previous issue.'); ?></LI>
</BLOCKQUOTE>
<? } else { ?>dnl
<P>
B_DIALOG(<*Copy previous issue*>, <*POST*>, <*do_add_prev.php*>)
	X_DIALOG_TEXT(<*<? putGS('Copy structure from issue nr $1','<B>'.getNumVar($q_nr,0).'</B>'); ?>*>)
	B_DIALOG_INPUT(<*Number*>)
		<INPUT TYPE="TEXT" NAME="cNumber" VALUE="<? print (getNumVar($q_nr,0) + 1); ?>" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="cOldNumber" VALUE="<? pgetNumVar($q_nr,0); ?>">
		<INPUT TYPE="HIDDEN" NAME="cPub" VALUE="<? pencHTML($Pub); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/issues/?Pub=<? pencURL($Pub); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>
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
