B_HTML
INCLUDE_PHP_LIB(<*../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSubscriptions*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting subscription*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete subscriptions.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('Subs');
    todefnum('Sect');
    todefnum('Pub');
    todefnum('User');
?>dnl
B_HEADER(<*Deleting subscription*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Sections*>, <*users/subscriptions/sections/?User=<? p($User); ?>&Pub=<? p($Pub); ?>&Subs=<? p($Subs); ?>*>)
X_HBUTTON(<*Subscriptions*>, <*users/subscriptions/?User=<? p($User); ?>*>)
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT UName FROM Users WHERE Id=$User", 'q_usr');
    if ($NUM_ROWS) {
	query ("SELECT SectionNumber FROM SubsSections WHERE IdSubscription=$Subs", 'q_ssubs');
	if ($NUM_ROWS) {
	    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	    if ($NUM_ROWS) {
		fetchRow($q_usr);
		fetchRow($q_ssubs);
		fetchRow($q_pub);
?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<B><? pgetHVar($q_usr,'UName'); ?></B>*>)
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Deleting subscription*>)
<?
    query ("DELETE FROM SubsSections WHERE IdSubscription=$Subs AND SectionNumber=$Sect");
    if ($AFFECTED_ROWS > 0) { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('The subscription to the section $1 has been deleted.','<B>'.getHVar($q_ssubs,'SectionNumber').'</B>'); ?></LI>*>)
<? } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('The subscription to the section $1 could not be deleted.','<B>'.getHVar($q_ssubs,'SectionNumber').'</B>'); ?></LI>*>)
<? } ?>dnl
	B_MSGBOX_BUTTONS
<? if ($AFFECTED_ROWS > 0) { ?>dnl
		<A HREF="X_ROOT/users/subscriptions/sections/?Pub=<? p($Pub); ?>&User=<? p($User); ?>&Subs=<? p($Subs); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/users/subscriptions/sections/del.php?Pub=<? p($Pub); ?>&User=<? p($User); ?>&Subs=<? p($Subs); ?>&Sec=<? p($Sect); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
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
