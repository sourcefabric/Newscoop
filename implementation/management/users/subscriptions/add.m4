INCLUDE_PHP_LIB(<*$ADMIN_DIR/users/subscriptions*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSubscriptions*>)

B_HEAD
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.config.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.core.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.validators.js"></script>
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
	todefnum('User');
    query ("SELECT UName FROM Users WHERE Id=$User", 'q_usr');
    if ($NUM_ROWS) {
    	fetchRow($q_usr);
    	$UName = getHVar($q_usr,'UName');
?>dnl
B_HEADER(<*Add new subscription*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*users/subscriptions/?User=<?php  p($User); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*User account*>, <*users/edit.php?User=<?php echo $User; ?>&uType=Subscribers*>, <**>, <*'$UName'*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Subscribers*>, <*users/?uType=Subscribers*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT UName FROM Users WHERE Id=$User", 'q_usr');
    if ($NUM_ROWS) { 
	fetchRow($q_usr);
    ?>dnl

<P>
B_DIALOG(<*Add new subscription*>, <*POST*>, <*do_add.php*>, <**>, <*onsubmit="return validateForm(this, 0, 1, 0, 1, 8);"*>)
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
		<INPUT TYPE="TEXT" class="input_text" NAME="cStartDate" SIZE="10" VALUE="<?php  p(date("Y-m-d")); ?>" MAXLENGTH="10" alt="date|yyyy/mm/dd|-" emsg="<?php putGS("You must input a valid date."); ?>"><?php  putGS('(YYYY-MM-DD)'); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Subscription Type*>)
		<SELECT NAME="sType" class="input_select">
		<OPTION VALUE="PN"><?php  putGS("Paid (confirm payment now)"); ?></OPTION>
		<OPTION VALUE="PL"><?php  putGS("Paid (payment will be confirmed later)"); ?></OPTION>
		<OPTION VALUE="T"><?php  putGS("Trial"); ?></OPTION>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Days*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cDays" SIZE="5" MAXLENGTH="5" alt="number|0|1|1000000000" emsg="<?php putGS("You must input a number greater than 0 into the $1 field.", "Days"); ?>">
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

<?php  } ?>dnl
<?php  } ?>dnl
X_COPYRIGHT
E_BODY

E_DATABASE
E_HTML
