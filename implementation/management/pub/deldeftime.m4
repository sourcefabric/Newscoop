INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_TITLE(<*Delete subscription default time*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to manage publications.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Delete subscription default time*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*pub/deftime.php?Pub=<?php  pencURL($Pub); ?>&Language=<?php  pencURL($Language); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    todefnum('Pub');
    todefnum('Language');
    todef('CountryCode');

    query ("SELECT * FROM Publications WHERE Id=$Pub", 'p');
    if ($NUM_ROWS) { 
	fetchRow($p);
?>dnl
<P>
B_MSGBOX(<*Delete subscription default time*>)
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Are you sure you want to delete the subscription default time for $1?','<B>'.getHVar($p,'Name').':'.encHTML($CountryCode).'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_deldeftime.php">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  pencHTML($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  pencHTML($Language); ?>">
		<INPUT TYPE="HIDDEN" NAME="CountryCode" VALUE="<?php  pencHTML($CountryCode); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/pub/deftime.php?Pub=<?php  pencURL($Pub); ?>&Language=<?php  pencURL($Language); ?>*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
