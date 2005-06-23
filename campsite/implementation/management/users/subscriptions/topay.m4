INCLUDE_PHP_LIB(<*$ADMIN_DIR/users/subscriptions*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSubscriptions*>)

B_HEAD
	X_TITLE(<*Change subscription status*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change subscriptions status.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('User');
    todefnum('Subs');
    query ("SELECT UName FROM Users WHERE Id=$User", 'q_usr');
    if ($NUM_ROWS) {
    	fetchRow($q_usr);
    	$UName = getHVar($q_usr,'UName');
?>dnl
B_HEADER(<*Change subscription status*>)
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
	query ("SELECT Active, IdPublication,ToPay,Currency FROM Subscriptions WHERE Id=$Subs", 'q_subs');
	if ($NUM_ROWS) {
	    fetchRow($q_usr);
	    fetchRow($q_subs);
	    query ("SELECT Name FROM Publications WHERE Id=".getSVar($q_subs,'IdPublication'), 'q_pub');
	    if ($NUM_ROWS) {
		fetchRow($q_pub);
	?>dnl

B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
E_CURRENT

<P>
B_DIALOG(<*Update payment*>, <*POST*>, <*do_topay.php*>)
        B_DIALOG_INPUT(<*Left to pay*>)
            <INPUT TYPE="TEXT" class="input_text" NAME="cToPay" VALUE="<?php  pgetHVar($q_subs,'ToPay'); ?>" SIZE=10> <?php  pgetHVar($q_subs,'Currency'); ?>
        E_DIALOG_INPUT
        B_DIALOG_BUTTONS
            <INPUT TYPE="HIDDEN" NAME="User" VALUE="<?php  p($User); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Subs" VALUE="<?php  p($Subs); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/users/subscriptions/?User=<?php  p($User); ?>*>)
        E_DIALOG_BUTTONS
E_DIALOG
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such subscription.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

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
