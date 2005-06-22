INCLUDE_PHP_LIB(<*$ADMIN_DIR/users/subscriptions*>)
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

<?php  todefnum('User'); ?>dnl
B_HEADER(<*Add new subscription*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*users/subscriptions/?User=<?php  p($User); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Subscribers*>, <*users/?uType=Subscribers*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT UName FROM Users WHERE Id=$User", 'q_usr');
    if ($NUM_ROWS) { 
	fetchRow($q_usr);
    ?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<?php  pgetHVar($q_usr,'UName'); ?>*>)
E_CURRENT

<P>
B_DIALOG(<*Add new subscription*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Publication*>)
<?php 
    query ("SELECT Id, Name FROM Publications ORDER BY Name", 'q_pub');
    $nr=$NUM_ROWS;
?>dnl
		<SELECT NAME="cPub" class="input_select">
		<?php 
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($q_pub);
			pComboVar(getHVar($q_pub,'Id'),'',getHVar($q_pub,'Name'));
		    }
		?>dnl
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Sections*>)
		<SELECT NAME="bAddSect" class="input_select"><OPTION VALUE="Y"><?php  putGS('Add sections now'); ?><OPTION VALUE="N"><?php  putGS('Add sections later'); ?>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Start*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cStartDate" SIZE="10" VALUE="<?php  p(date("Y-m-d")); ?>" MAXLENGTH="10"><?php  putGS('(YYYY-MM-DD)'); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Subscription Type*>)
		<SELECT NAME="sType" class="input_select">
		<OPTION VALUE="PN"><?php  putGS("Paid (confirm payment now)"); ?></OPTION>
		<OPTION VALUE="PL"><?php  putGS("Paid (payment will be confirmed later)"); ?></OPTION>
		<OPTION VALUE="T"><?php  putGS("Trial"); ?></OPTION>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Days*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cDays" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cActive" CHECKED class="input_checkbox">*>)
		Active
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<?php  p($User); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/users/subscriptions/?User=<?php  p($User); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

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
