B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new country default subscription time*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to manage publications.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('cPub');
    todef('cCountryCode');
    todefnum('cTrialTime');
    todefnum('cPaidTime');
    todefnum('Language', 1);
    $correct= 1;
    $created= 0;
    
?>

B_HEADER(<*Adding new country default subscription time*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*pub/deftime.php?Pub=<? pencURL($cPub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT Name FROM Publications WHERE Id=$cPub", 'q_pub');
    if ($NUM_ROWS) { 
	fetchRow($q_pub);    
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Adding new country default subscription time*>)
	X_MSGBOX_TEXT(<*
<?
    $cCountryCode=trim($cCountryCode);
    if ($cCountryCode == "" || $cCountryCode == " ") {
	$correct= 0; ?>dnl
		<LI><? putGS('You must select a country.'); ?></LI>
<?
    }
    
    if ($correct) {
	query ("INSERT IGNORE INTO SubsDefTime SET CountryCode='$cCountryCode', IdPublication='$cPub', TrialTime='$cTrialTime', PaidTime='$cPaidTime'");
	$created= ($AFFECTED_ROWS > 0);
    }

    if ($created) { ?>dnl
		<LI><? putGS('The default subscription time for $1 has been added.','<B>'.getHVar($q_pub,'Name').':'.encHTML($cCountryCode).'</B>'); ?></LI>
X_AUDIT(<*4*>, <*getGS('The default subscription time for $1 has been added.',getVar($q_pub,'Name').':'.$cCountryCode)*>)
<? } else {
    if ($correct != 0) { ?>dnl
		<LI><? putGS('The default subscription time for country $1 could not be added.',getHVar($q_pub,'Name').':'.encHTML($cCountryCode)); ?></LI><LI><? putGS('Please check if another entry with the same country code does not already exist.'); ?></LI>
<? 
    }
    }
?>dnl
		*>)
<? if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/countryadd.php?Pub=<? pencURL($cPub); ?>&Language=<? pencURL($Language); ?>"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another country"></A>
		<A HREF="X_ROOT/pub/deftime.php?Pub=<? pencURL($cPub); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<? } else { ?>
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/countryadd.php?Pub=<? pencURL($cPub); ?>&Language=<? pencURL($Language); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
	E_MSGBOX_BUTTONS
<? } ?>dnl
E_MSGBOX
<P>
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

