INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE(<*Publication Aliases*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
E_HEAD

<?php  } else { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
	todefnum('Pub');
?>dnl
B_HEADER(<*Publication Aliases*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
	$sql = "SELECT Name FROM Publications WHERE Id=$Pub";
	query($sql, 'q_pub');
	if ($NUM_ROWS) { 
		fetchRow($q_pub);    
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
E_CURRENT

<TABLE>
<TR>
	<TD>X_NEW_BUTTON(<*Add new alias*>, <*add_alias.php?Pub=<?php  pencURL($Pub); ?>*>)</TD>
	<TD>X_BACK_BUTTON(<*Back to publication*>, <*edit.php?Pub=<?php  pencURL($Pub); ?>*>)</TD>
</TR>
</TABLE>

<P><?php 
	todefnum('ListOffs');
	if ($ListOffs < 0)
		$ListOffs= 0;

	$sql = "SELECT * FROM Aliases WHERE IdPublication=$Pub ORDER BY Name ASC LIMIT $ListOffs, 11";
	query($sql, 'q_aliases');
	if ($NUM_ROWS) {
		$nr = $NUM_ROWS;
		$i = 10;
		$color = 0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Alias (click to edit)*>)
		X_LIST_TH(<*Delete*>, <*1%*>)
	E_LIST_HEADER
<?php  
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_aliases);
		if ($i) {
?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/edit_alias.php?Pub=<?php  pencURL($Pub); ?>&Alias=<?php  pgetHVar($q_aliases, 'Id'); ?>"><?php  pgetHVar($q_aliases,'Name'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<?php  putGS('Delete entry $1',getHVar($q_aliases, 'Name')); ?>*>, <*/delete.png*>, <*pub/del_alias.php?Pub=<?php  pencURL($Pub); ?>&Alias=<?php  pgetUVar($q_aliases, 'Id'); ?>*>)
		E_LIST_ITEM
	E_LIST_TR
<?php 
			$i--;
		}
	}
?>dnl
	B_LIST_FOOTER(9)
<?php  if ($ListOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*index.php?Pub=<?php  pencURL($Pub); ?>&ListOffs=<?php  print ($ListOffs - 10); ?>*>)
<?php  }
    if ($nr < 11) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*index.php?Pub=<?php  pencURL($Pub); ?>&ListOffs=<?php  print ($ListOffs + 10); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No entries defined.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  }  else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

