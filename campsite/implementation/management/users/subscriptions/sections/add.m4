INCLUDE_PHP_LIB(<*$ADMIN_DIR/users/subscriptions/sections*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSubscriptions*>)

B_HEAD
	X_TITLE(<*Add new subscription*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add subscriptions.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Subs');
    todefnum('Pub');
    todefnum('User');
?>dnl
B_HEADER(<*Add new subscription*>)
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
	    fetchRow($q_usr);
	    fetchRow($q_pub);
?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<?php  pgetHVar($q_usr,'UName'); ?>*>)
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
E_CURRENT

<P>
B_DIALOG(<*Add new subscription*>, <*POST*>, <*do_add.php*>)

	B_DIALOG_INPUT(<*Section*>)
		<SELECT NAME="cSection" class="input_select">
		<OPTION VALUE=0>All sections</OPTION>
<?php  
    query ("SELECT DISTINCT Number, Name FROM Sections WHERE IdPublication=$Pub",'q_sect');
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_sect);
	pComboVar(getHVar($q_sect,'Number'),'',getVar($q_sect,'Name'));
    }
?>dnl
		</SELECT>
	E_DIALOG_INPUT

	B_DIALOG_INPUT(<*Start*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cStartDate" SIZE="10" VALUE="<?php  p(date("Y-m-d")); ?>" MAXLENGTH="10"><?php  putGS('(YYYY-MM-DD)'); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Days*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cDays" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<?php  p($User); ?>">
		<INPUT TYPE="HIDDEN" NAME="Subs" VALUE="<?php  p($Subs); ?>">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/users/subscriptions/sections/?Pub=<?php  p($Pub); ?>&User=<?php  p($User); ?>&Subs=<?php  p($Subs); ?>*>)
	E_DIALOG_BUTTONS
	<!--<tr><td colspan=2 width=250><?php  putGS('WARNING: If you subscribe to all sections, the periods for previously added sections will be overriden!'); ?></td></tr>-->
E_DIALOG
<P>

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
