B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSubscriptions*>)

B_HEAD
	X_EXPIRES
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
?>dnl
B_HEADER(<*Change subscription status*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*users/subscriptions/?User=<?php  p($User); ?>*>)
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
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
X_CURRENT(<*User account*>, <*<B><?php  pgetHVar($q_usr,'UName'); ?></B>*>)
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Change subscription status*>)
<?php  if (getVar($q_subs,'Active') == "Y") { ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Are you sure you want to deactivate the subscription?'); ?></LI>*>)
<?php  } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Are you sure you want to activate the subscription?'); ?></LI>*>)
<?php  } ?>dnl
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_status.php">
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<?php  p($User); ?>">
		<INPUT TYPE="HIDDEN" NAME="Subs" VALUE="<?php  p($Subs); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/users/subscriptions/?User=<?php  p($User); ?>*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such publication.'); ?></LI>
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

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
