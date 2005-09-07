INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_TITLE(<*Deleting subscription default time*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to manage publications.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Deleting subscription default time*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
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
<?php 
    if ($del)
	query ("DELETE FROM SubsDefTime WHERE CountryCode='$CountryCode' AND IdPublication=$Pub");
    if ($AFFECTED_ROWS > 0) { ?>dnl
	<LI><?php  putGS('The subscription default time for $1 has been deleted.','<B>'.getHVar($q_pub,'Name').':'.encHTML($CountryCode).'</B>'); ?></LI>
X_AUDIT(<*5*>, <*getGS('Subscription default time for $1 deleted',getVar($q_pub,'Name').':'.$CountryCode)*>)
<?php  } else { ?>dnl
	<LI><?php  putGS('The default subscription time for $1 could not be deleted.','<B>'.getHVar($q_pub,'Name').':'.encHTML($CountryCode).'</B>'); ?></LI>
<?php  } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<?php  if ($AFFECTED_ROWS > 0) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/deftime.php?Pub=<?php  pencURL($Pub); ?>&Language=<?php  pencURL($Language); ?>*>)
<?php  } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/deftime.php?Pub=<?php  pencURL($Pub); ?>&Language=<?php  pencURL($Language); ?>*>)
<?php  } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
