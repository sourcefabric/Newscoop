B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new publication*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add publications.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Adding new publication*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todef('cName');
    todef('cSite');
    todefnum('cLanguage');
    todefnum('cPayTime');
    todefnum('cTimeUnit');
    todefnum('cUnitCost');
    todefnum('cCurrency');
    todefnum('cPaid');
    todefnum('cTrial');

    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new publication*>)
	X_MSGBOX_TEXT(<*
<?
    $cName=trim($cName);
    $cSite=trim($cSite);
//!sql query "SELECT TRIM('?cName'), TRIM('?cSite')" q_tr>dnl

    if ($cName == "" || $cName == " ") {
	$correct= 0; ?>dnl
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
    <? }
    
    if ($cSite == "" || $cSite == " ") {
	$correct=0; ?>dnl
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Site').'</B>'); ?></LI>
    <? }

    if ($correct) {
	$AFFECTED_ROWS=0;
	query ("INSERT IGNORE INTO Publications SET Name='$cName', Site='$cSite', IdDefaultLanguage=$cLanguage, PayTime='$cPayTime', TimeUnit='$cTimeUnit', UnitCost='$cUnitCost', Currency='$cCurrency', PaidTime='$cPaid', TrialTime='$cTrial'");
	$created= ($AFFECTED_ROWS > 0);
    }

    if ($created) { ?>dnl
		<LI><? putGS('The publication $1 has been successfuly added.',"<B>".encHTML(decS($cName))."</B>"); ?></LI>
X_AUDIT(<*1*>, <*getGS('Publication $1 added',$cName)*>)
<?
    } else {
	if ($correct != 0) { ?>dnl
		<LI><? putGS('The publication could not be added.'); ?></LI><LI><? putGS('Please check if another publication with the same or the same site name does not already exist.'); ?></LI>
<? }
}
?>dnl
		*>)
<? if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/add.php"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another publication"></A>
		<A HREF="X_ROOT/pub/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<? } else { ?>
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/add.php"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
	E_MSGBOX_BUTTONS
<? } ?>dnl
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
