B_HTML
INCLUDE_PHP_LIB(<*../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSubscriptions*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding subscription*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add subscriptions.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<? 
    todefnum('User');
    todefnum('Pub');
    todefnum('Subs');
    todefnum('cStartDate');
    todefnum('cSection');
    todefnum('cDays');
    todefnum('Success',1);
?>dnl
B_HEADER(<*Adding sections*>)
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
	query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Subscriptions WHERE Id = $Subs", 'q_sub');
	    if ($NUM_ROWS) {
		fetchRow($q_usr);
		fetchRow($q_pub);
		fetchRow($q_sub);
		$isPaid = 0;
		if (getHVar($q_sub, 'Type') == 'P')
		    $isPaid = 1;
?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<B><? pgetHVar($q_usr,'UName'); ?></B>*>)
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Adding sections to subscription*>)

<?
    $cPaidDays = 0;
    if (!$isPaid)
	$cPaidDays = $cDays;
    if ($cSection != 0) {
	query ("INSERT IGNORE INTO SubsSections SET IdSubscription=$Subs, SectionNumber='$cSection', StartDate='$cStartDate', Days='$cDays', PaidDays='$cPaidDays'");
	if ($AFFECTED_ROWS > 0) { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('The section was added successfully.'); ?></LI>*>)
    <? } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('The section could not be added.'); ?></LI><LI><? putGS("Please check if there isn't another subscription with the same section."); ?></LI>*>)
<? } ?>dnl
	B_MSGBOX_BUTTONS
<? if ($AFFECTED_ROWS > 0) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/users/subscriptions/sections/?Pub=<? p($Pub); ?>&User=<? p($User); ?>&Subs=<? p($Subs); ?>*>)
<? } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/users/subscriptions/sections/add.php?Pub=<? p($Pub); ?>&User=<? p($User); ?>&Subs=<? p($Subs); ?>*>)
<? } ?>dnl
	E_MSGBOX_BUTTONS

<? } else {

    query ("SELECT DISTINCT Number FROM Sections where IdPublication=$Pub", 'q_sect');
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRowNum($q_sect);
	$tval=encS(getNumVar($q_sect,0));

	query ("INSERT IGNORE INTO SubsSections SET IdSubscription=$Subs, SectionNumber='$tval', StartDate='$cStartDate', Days='$cDays', PaidDays='$cPaidDays'");
	if ($AFFECTED_ROWS == 0)
	    $Success= 0;
    }

    if ($Success) { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('The sections were added successfully.'); ?></LI>*>)
<? } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('The sections could not be added successfully. Some of them were already added !'); ?></LI>*>)
<? } ?>dnl
	B_MSGBOX_BUTTONS
<? if ($Success) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/users/subscriptions/sections/?Pub=<? p($Pub); ?>&User=<? p($User); ?>&Subs=<? p($Subs); ?>*>)
<? } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/users/subscriptions/sections/add.php?Pub=<? p($Pub); ?>&User=<? p($User); ?>&Subs=<? p($Subs); ?>*>)
<? } ?>dnl
	E_MSGBOX_BUTTONS
<? } ?>

E_MSGBOX
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such subscription.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
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
