INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_TITLE(<*Delete alias*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to manage publications.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Delete alias*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<TABLE>
<TR>
	<TD>X_BACK_BUTTON(<*Back to publication*>, <*edit.php?Pub=<?php  pencURL($Pub); ?>*>)</TD>
	<TD>X_BACK_BUTTON(<*Back to aliases*>, <*aliases.php?Pub=<?php  pencURL($Pub); ?>*>)</TD>
</TR>
</TABLE>

<?php 
	todefnum('Pub');
	todefnum('Alias');

	query ("SELECT * FROM Publications WHERE Id=$Pub", 'p');
	if ($NUM_ROWS) { 
		fetchRow($p);
		query ("SELECT * FROM Aliases WHERE Id=$Alias", 'a');
		if ($NUM_ROWS) { 
			fetchRow($a);
?>dnl
<P>
B_MSGBOX(<*Delete alias*>)
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Are you sure you want to delete the alias $1?', '<B>'.getHVar($a,'Name').'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del_alias.php">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  pencHTML($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Alias" VALUE="<?php  pencHTML($Alias); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/pub/aliases.php?Pub=<?php  pencURL($Pub); ?>*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such alias.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

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
