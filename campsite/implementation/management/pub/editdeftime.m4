B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Change subscription default time*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to edit publication information.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('Pub');
    todefnum('Language');
    todef('CountryCode');
?>
B_HEADER(<*Change subscription default time*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*pub/deftime.php?Pub=<? pencURL($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) {
    
	query ("SELECT * FROM Countries WHERE Code='$CountryCode' AND IdLanguage=$Language", 'q_ctr');
	if ($NUM_ROWS) {
	
	    query ("SELECT * FROM SubsDefTime WHERE CountryCode='".encHTML($CountryCode)."' AND IdPublication=$Pub", 'q_deft');
	    if ($NUM_ROWS) { 
		fetchRow($q_pub);
		fetchRow($q_ctr);
		fetchRow($q_deft);

?>dnl

B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Country*>, <*<B><? pgetHVar($q_ctr,'Name'); ?></B>*>)
E_CURRENT

<P>
B_DIALOG(<*Change subscription default time*>, <*POST*>, <*do_editdeftime.php*>)
	<INPUT TYPE=HIDDEN NAME=cPub VALUE="<? pencURL($Pub); ?>">
	<INPUT TYPE=HIDDEN NAME=cCountryCode VALUE="<? pencURL($CountryCode); ?>">
	<INPUT TYPE=HIDDEN NAME=Language VALUE="<? pencURL($Language); ?>">
	B_DIALOG_INPUT(<*Trial Period*>)
		<INPUT TYPE="TEXT" NAME="cTrialTime" VALUE="<? pgetHVar($q_deft,'TrialTime'); ?>" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Paid Period*>)
		<INPUT TYPE="TEXT" NAME="cPaidTime" VALUE="<? pgetHVar($q_deft,'PaidTime'); ?>" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<? pencHTML($Pub); ?>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/pub/deftime.php?Pub=<? pencURL($Pub); ?>&Language=<? pencURL($Language); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No default time entry for that country.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such country.'); ?></LI>
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

