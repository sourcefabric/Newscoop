B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSubscriptions*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Changing subscription status*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change subscriptions status.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('User');
    todefnum('Subs');
?>dnl
B_HEADER(<*Changing subscription status*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*users/subscriptions/?User=<? p($User); ?>*>)
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
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
X_CURRENT(<*User account*>, <*<B><? pgetHVar($q_usr,'UName'); ?></B>*>)
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Changing subscription status*>)
<?
    query ("UPDATE Subscriptions SET Active=IF(Active = 'Y', 'N', 'Y') WHERE Id=$Subs");
    if ($AFFECTED_ROWS > 0) {
	if (getVar($q_subs,'Active') == "N") { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('The subscription has been activated.'); ?></LI>*>)
<? } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('The subscription has been deactivated.'); ?></LI>*>)
<? } ?>dnl
<? } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('Subscription status could not be changed.'); ?></LI>*>)
<? } ?>dnl
	B_MSGBOX_BUTTONS
<? if ($AFFECTED_ROWS > 0) { ?>dnl
		<A HREF="X_ROOT/users/subscriptions/?User=<? p($User); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/users/subscriptions/?User=<? p($User); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such subscription.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such user account.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
