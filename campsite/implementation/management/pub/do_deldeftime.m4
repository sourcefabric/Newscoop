B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting subscription default time*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to manage publications.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Deleting subscription default time*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todefnum('Pub');
    todefnum('Language');
    todef('CountryCode');
    todefnum('del', 1);

    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
    
    if ($NUM_ROWS) {
	fetchRow($q_pub);
?>dnl
<P>
B_MSGBOX(<*Deleting subscription default time*>)
	X_MSGBOX_TEXT(<*
<?
    if ($del)
	query ("DELETE FROM SubsDefTime WHERE CountryCode='$CountryCode' AND IdPublication=$Pub");
    if ($AFFECTED_ROWS > 0) { ?>dnl
	<LI><? putGS('The subscription default time for $1 has been deleted.','<B>'.getHVar($q_pub,'Name').':'.encHTML($CountryCode).'</B>'); ?></LI>
X_AUDIT(<*5*>, <*getGS('Subscription default time for $1 deleted',getVar($q_pub,'Name').':'.$CountryCode)*>)
<? } else { ?>dnl
	<LI><? putGS('The default subscription time for $1 could not be deleted.','<B>'.getHVar($q_pub,'Name').':'.encHTML($CountryCode).'</B>'); ?></LI>
<? } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<? if ($AFFECTED_ROWS > 0) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/deftime.php?Pub=<? pencURL($Pub); ?>&Language=<? pencURL($Language); ?>*>)
<? } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/deftime.php?Pub=<? pencURL($Pub); ?>&Language=<? pencURL($Language); ?>*>)
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
