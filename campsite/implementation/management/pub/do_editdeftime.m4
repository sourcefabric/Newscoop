B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Changing default subscription time*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change publication information.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('cPub');
    todef('cCountryCode');
    todefnum('Language', 1);
    todefnum('cPayeTime');
    todefnum('cTrialTime');
?>dnl
B_HEADER(<*Changing default subscription time*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*pub/deftime.php?Pub=<? pencURL($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    $created= 0;
    
    query ("SELECT * FROM Publications WHERE Id=$cPub", 'q_pub');

    if ($NUM_ROWS) {
	query ("SELECT * FROM Countries WHERE Code='$cCountryCode'", 'q_ctr');
	
	if ($NUM_ROWS) {
	    fetchRow($q_pub);
	    fetchRow($q_ctr);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Country*>, <*<B><? pgetHVar($q_ctr,'Name'); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Changing default subscription time*>)
	X_MSGBOX_TEXT(<*
<?
    query ("UPDATE SubsDefTime SET TrialTime='$cTrialTime', PaidTime='$cPaidTime' WHERE CountryCode='$cCountryCode' AND IdPublication=$cPub");
    $created= ($AFFECTED_ROWS > 0);

    if ($created) { ?>dnl
		<LI><? putGS('The default subscription time for $1 has been successfuly updated.','<B>'.getHVar($q_pub,'Name').':'.getHVar($q_ctr,'Name').'</B>'); ?></LI>
X_AUDIT(<*6*>, <*getGS('Default subscription time for $1 changed',getVar($q_pub,'Name').':'.$cCountryCode)*>)
<? } else { ?>dnl
		<LI><? putGS('The default subscription time could not be updated.'); ?></LI>
<? } ?>dnl
		*>)
	B_MSGBOX_BUTTONS
<? if ($created) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/deftime.php?Pub=<? pencURL($Pub); ?>&Language=<? pencURL($Language); ?>*>)
<? } else { ?>
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/deftime.php?Pub=<? pencURL($Pub); ?>&Language=<? pencURL($Language); ?>*>)
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such country.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
