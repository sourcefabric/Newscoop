INCLUDE_PHP_LIB(<*$ADMIN_DIR/users/subscriptions/sections*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSubscriptions*>)

B_HEAD
	X_TITLE(<*Change subscription*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change subscriptions.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Subs');
    todefnum('Sect');
    todefnum('Pub');
    todefnum('User');
?>dnl
B_HEADER(<*Change subscription*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Sections*>, <*users/subscriptions/sections/?User=<?php  p($User); ?>&Pub=<?php  p($Pub); ?>&Subs=<?php  p($Subs); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Subscriptions*>, <*users/subscriptions/?User=<?php  p($User); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Subscribers*>, <*users/?uType=Subscribers*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT UName FROM Users WHERE Id=$User", 'q_usr');
    if ($NUM_ROWS) {
	query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Subscriptions WHERE Id = $Subs", 'q_sub');
	    if ($NUM_ROWS) {
		$sectCond = "";
		if ($Sect > 0)
		    $sectCond = "SectionNumber = ".$Sect." AND";
		query ("SELECT DISTINCT Sub.*, Sec.Name FROM SubsSections as Sub, Sections as Sec WHERE $sectCond IdSubscription=$Subs AND Sub.SectionNumber = Sec.Number", 'q_ssub');
		if ($NUM_ROWS) {
		    fetchRow($q_usr);
		    fetchRow($q_pub);
		    fetchRow($q_sub);
		    fetchRow($q_ssub);
		    $isPaid = 0;
		    if (getHVar($q_sub, 'Type') == 'P')
			$isPaid = 1;
?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<?php  pgetHVar($q_usr,'UName'); ?>*>)
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
E_CURRENT

<P>
B_DIALOG(<*Change subscription*>, <*POST*>, <*do_change.php*>)

	B_DIALOG_INPUT(<*Section*>)
		<?php  if ($Sect > 0) pgetHVar($q_ssub,'Name'); else putGS("-- ALL SECTIONS --"); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Start*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cStartDate" SIZE="10" VALUE="<?php  pgetHVar($q_ssub,'StartDate'); ?>" MAXLENGTH="10"> <?php  putGS('(YYYY-MM-DD)'); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Days*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cDays" SIZE="5" VALUE="<?php  pgetHVar($q_ssub,'Days'); ?>"  MAXLENGTH="5">
	E_DIALOG_INPUT
<?php  if ($isPaid) { ?>
	B_DIALOG_INPUT(<*Paid Days*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cPaidDays" SIZE="5" VALUE="<?php  pgetHVar($q_ssub,'PaidDays'); ?>"  MAXLENGTH="5">
	E_DIALOG_INPUT
<?php  } ?>
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<?php  p($User); ?>">
		<INPUT TYPE="HIDDEN" NAME="Subs" VALUE="<?php  p($Subs); ?>">
		<INPUT TYPE="HIDDEN" NAME="Sect" VALUE="<?php  p($Sect); ?>">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/users/subscriptions/sections/?Pub=<?php  p($Pub); ?>&User=<?php  p($User); ?>&Subs=<?php  p($Subs); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No sections in the current subscription.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such subscription.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such user account.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
