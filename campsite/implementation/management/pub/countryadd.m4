B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add new country default subscription time*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to manage publications.*>)
<? }
    query ("SELECT Code, Name FROM Countries WHERE 1=0", 'q_ctr');
?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('Language', 1);
    todefnum('Pub');
?>
B_HEADER(<*Add new country default subscription time*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*pub/deftime.php?Pub=<? pencHTML($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) {
	fetchRow($q_pub);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
E_CURRENT
 
<P>
B_DIALOG(<*Add new country default subscription time*>, <*POST*>, <*do_countryadd.php*>)
	<INPUT TYPE=HIDDEN NAME=cPub VALUE="<? pencHTML($Pub); ?>">
	B_DIALOG_INPUT(<*Country*>)
	    <SELECT NAME="cCountryCode">
<?
    query ("SELECT Code, Name FROM Countries WHERE IdLanguage = $Language", 'q_ctr');
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) { 
	fetchRow($q_ctr);
	query ("SELECT * FROM SubsDefTime WHERE CountryCode = '".getSVar($q_ctr,'Code')."' AND IdPublication=$Pub", 'q_subs');
	if ($NUM_ROWS == 0) { ?>
	    <OPTION VALUE="<? pgetHVar($q_ctr,'Code'); ?>"><? pgetHVar($q_ctr,'Name'); ?>dnl
	<? }
    }
?>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Trial Period*>)
		<INPUT TYPE="TEXT" NAME="cTrialTime" VALUE="1" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Paid Period*>)
		<INPUT TYPE="TEXT" NAME="cPaidTime" VALUE="1" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/deftime.php?Pub=<? pencURL($Pub); ?>&Language=<? pencURL($Language); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>
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
