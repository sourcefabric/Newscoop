B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting alias*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to manage publications.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Deleting alias*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
	todefnum('Pub');
	todefnum('Alias');
	todefnum('del', 1);

	query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
		fetchRow($q_pub);
		query ("SELECT Name FROM Aliases WHERE Id=$Alias", 'q_alias');
		if ($NUM_ROWS) {
			fetchRow($q_alias);
?>dnl
<P>
B_MSGBOX(<*Deleting alias*>)
	X_MSGBOX_TEXT(<*
<?php 
	if ($del)
		query ("DELETE FROM Aliases WHERE Id='$Alias'");
	if ($AFFECTED_ROWS > 0) { ?>dnl
		<LI><?php  putGS('The alias $1 has been deleted from publication $2.','<B>'.getHVar($q_alias,'Name').'</B>','<B>'.getHVar($q_pub,'Name').'</B>'); ?></LI>
		X_AUDIT(<*152*>, <*getGS('The alias $1 has been deleted from publication $2.',getVar($q_alias,'Name'),getVar($q_pub, 'Name'))*>)
<?php
	} else {
?>dnl
		<LI><?php  putGS('The alias $1 could not be deleted.','<B>'.getHVar($q_alias,'Name').'</B>'); ?></LI>
<?php
	}
?>dnl
	*>)
	B_MSGBOX_BUTTONS
<?php  if ($AFFECTED_ROWS > 0) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/aliases.php?Pub=<?php  pencURL($Pub); ?>*>)
<?php  } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/aliases.php?Pub=<?php  pencURL($Pub); ?>*>)
<?php  } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such alias.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
