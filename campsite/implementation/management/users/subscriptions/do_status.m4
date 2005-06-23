B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/users/subscriptions*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSubscriptions*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Changing subscription status*>)
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
B_HEADER(<*Changing subscription status*>)
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
	query ("SELECT Active, IdPublication FROM Subscriptions WHERE Id=$Subs", 'q_subs');
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
B_MSGBOX(<*Changing subscription status*>)
<?php 
    query ("UPDATE Subscriptions SET Active=IF(Active = 'Y', 'N', 'Y') WHERE Id=$Subs");
    if ($AFFECTED_ROWS > 0) {
	if (getVar($q_subs,'Active') == "N") { ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('The subscription has been activated.'); ?></LI>*>)
<?php  } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('The subscription has been deactivated.'); ?></LI>*>)
<?php  } ?>dnl
<?php  } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Subscription status could not be changed.'); ?></LI>*>)
<?php  } ?>dnl
	B_MSGBOX_BUTTONS
<?php  if ($AFFECTED_ROWS > 0) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/users/subscriptions/?User=<?php  p($User); ?>*>)
<?php  } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/users/subscriptions/?User=<?php  p($User); ?>*>)
<?php  } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
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
