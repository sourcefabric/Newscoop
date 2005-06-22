B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/users/subscriptions*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSubscriptions*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting subscription*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete subscriptions.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php  
    todefnum('User');
    todefnum('Subs');
?>dnl
B_HEADER(<*Deleting subscription*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*users/subscriptions/?User=<?php  p($User); ?>*>)
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
X_CURRENT(<*User account*>, <*<?php  pgetHVar($q_usr,'UName'); ?>*>)
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
E_CURRENT

<P>
B_MSGBOX(<*Deleting subscription*>)
<?php 
    query ("DELETE FROM SubsSections WHERE IdSubscription=$Subs");
    query ("DELETE FROM Subscriptions WHERE Id=$Subs");
    if ($AFFECTED_ROWS > 0) { ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('The subscription has been deleted.'); ?></LI>*>)
<?php  } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('The subscription could not be deleted.'); ?></LI>*>)
<?php  } ?>dnl
	B_MSGBOX_BUTTONS
<?php  if ($AFFECTED_ROWS > 0) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/users/subscriptions/?User=<?php  p($User); ?>*>)
<?php  } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/users/subscriptions/del.php?User=<?php  p($User); ?>&Subs=<?php  p($Subs); ?>*>)
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

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
