INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
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
E_HEADER_BUTTONS
E_HEADER

<?php 
	todefnum('Pub');
	todefnum('Alias');
	todefnum('del', 1);

	query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
		fetchRow($q_pub);
		$def_alias = getVar($q_pub, 'IdDefaultAlias');
		$pub_name = getHVar($q_pub, 'Name');
		query ("SELECT Name FROM Aliases WHERE Id=$Alias", 'q_alias');
		if ($NUM_ROWS) {
			fetchRow($q_alias);
			$alias_name = getHVar($q_alias,'Name');
?>dnl
<P>
B_MSGBOX(<*Deleting alias*>)
	X_MSGBOX_TEXT(<*
<?php 
	if ($del && $def_alias != $Alias)
		query ("DELETE FROM Aliases WHERE Id='$Alias'");
	if ($AFFECTED_ROWS > 0) {
		$params = array($operation_attr=>$operation_modify, "IdPublication"=>"$Pub" );
		$msg = build_reset_cache_msg($cache_type_publications, $params);
		send_message("127.0.0.1", server_port(), $msg, $err_msg);
?>dnl
		<LI><?php  putGS('The alias $1 has been deleted from publication $2.','<B>'.$alias_name.'</B>','<B>'.$pub_name.'</B>'); ?></LI>
		X_AUDIT(<*152*>, <*getGS('The alias $1 has been deleted from publication $2.',$alias_name,$pub_name)*>)
<?php
	} else {
		if ($def_alias == $Alias) {
			echo "<LI>";
			putGS('$1 is the default publication alias, it can not be deleted.', '<B>'.$alias_name.'</B>');
			echo "</LI>\n";
		}
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
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
