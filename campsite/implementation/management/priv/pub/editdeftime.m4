INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_TITLE(<*Change subscription default time*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to edit publication information.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Pub');
    todefnum('Language');
    todef('CountryCode');
?>
B_HEADER(<*Change subscription default time*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*pub/deftime.php?Pub=<?php  pencURL($Pub); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
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
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
X_CURRENT(<*Country*>, <*<?php  pgetHVar($q_ctr,'Name'); ?>*>)
E_CURRENT

<P>
B_DIALOG(<*Change subscription default time*>, <*POST*>, <*do_editdeftime.php*>)
	<INPUT TYPE=HIDDEN NAME=cPub VALUE="<?php  pencURL($Pub); ?>">
	<INPUT TYPE=HIDDEN NAME=cCountryCode VALUE="<?php  pencURL($CountryCode); ?>">
	<INPUT TYPE=HIDDEN NAME=Language VALUE="<?php  pencURL($Language); ?>">
	B_DIALOG_INPUT(<*Trial Period*>)
		<INPUT TYPE="TEXT" NAME="cTrialTime" VALUE="<?php  pgetHVar($q_deft,'TrialTime'); ?>" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Paid Period*>)
		<INPUT TYPE="TEXT" NAME="cPaidTime" VALUE="<?php  pgetHVar($q_deft,'PaidTime'); ?>" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  pencHTML($Pub); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/deftime.php?Pub=<?php  pencURL($Pub); ?>&Language=<?php  pencURL($Language); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No default time entry for that country.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such country.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

