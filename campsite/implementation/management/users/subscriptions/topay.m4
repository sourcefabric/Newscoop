B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSubscriptions*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Change subscription status*>)
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
B_HEADER(<*Change subscription status*>)
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
	query ("SELECT Active, IdPublication,ToPay,Currency FROM Subscriptions WHERE Id=$Subs", 'q_subs');
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
B_DIALOG(<*Update payment*>, <*POST*>, <*do_topay.php*>)
        B_DIALOG_INPUT(<*Left to pay*>)
            <INPUT TYPE="TEXT" NAME="cToPay" VALUE="<? pgetHVar($q_subs,'ToPay'); ?>" SIZE=10> <? pgetHVar($q_subs,'Currency'); ?>
        E_DIALOG_INPUT
        B_DIALOG_BUTTONS
            <INPUT TYPE="HIDDEN" NAME="User" VALUE="<? p($User); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Subs" VALUE="<? p($Subs); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/users/subscriptions/?User=<? p($User); ?>*>)
        E_DIALOG_BUTTONS
E_DIALOG
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
