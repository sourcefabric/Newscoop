INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_TITLE(<*Adding new country default subscription time*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to manage publications.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
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
X_HBUTTON(<*Subscriptions*>, <*pub/deftime.php?Pub=<?php  pencURL($cPub); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT Name FROM Publications WHERE Id=$cPub", 'q_pub');
    if ($NUM_ROWS) { 
	fetchRow($q_pub);    
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
E_CURRENT

<P>
B_MSGBOX(<*Adding new country default subscription time*>)
	X_MSGBOX_TEXT(<*
<?php 
    $cCountryCode=trim($cCountryCode);
    if ($cCountryCode == "" || $cCountryCode == " ") {
	$correct= 0; ?>dnl
		<LI><?php  putGS('You must select a country.'); ?></LI>
<?php 
    }
    
    if ($correct) {
	query ("INSERT IGNORE INTO SubsDefTime SET CountryCode='$cCountryCode', IdPublication='$cPub', TrialTime='$cTrialTime', PaidTime='$cPaidTime'");
	$created= ($AFFECTED_ROWS > 0);
    }

    if ($created) { ?>dnl
		<LI><?php  putGS('The default subscription time for $1 has been added.','<B>'.getHVar($q_pub,'Name').':'.encHTML($cCountryCode).'</B>'); ?></LI>
X_AUDIT(<*4*>, <*getGS('The default subscription time for $1 has been added.',getVar($q_pub,'Name').':'.$cCountryCode)*>)
<?php  } else {
    if ($correct != 0) { ?>dnl
		<LI><?php  putGS('The default subscription time for country $1 could not be added.',getHVar($q_pub,'Name').':'.encHTML($cCountryCode)); ?></LI><LI><?php  putGS('Please check if another entry with the same country code exists already.'); ?></LI>
<?php  
    }
    }
?>dnl
		*>)
<?php  if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*new*>, <*Add another*>, <*X_ROOT/pub/countryadd.php?Pub=<?php  pencURL($cPub); ?>&Language=<?php  pencURL($Language); ?>*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/deftime.php?Pub=<?php  pencURL($cPub); ?>*>)
	E_MSGBOX_BUTTONS
<?php  } else { ?>
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/countryadd.php?Pub=<?php  pencURL($cPub); ?>&Language=<?php  pencURL($Language); ?>*>)
	E_MSGBOX_BUTTONS
<?php  } ?>dnl
E_MSGBOX
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
        <LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

