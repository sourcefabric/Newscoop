INCLUDE_PHP_LIB(<*$ADMIN_DIR/users*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE(<*User management*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  }

    query ("SELECT (StartIP & 0xff000000) >> 24, (StartIP & 0x00ff0000) >> 16, (StartIP & 0x0000ff00) >> 8, StartIP & 0x000000ff, StartIP, Addresses FROM SubsByIP WHERE 1=0", 'IPs');
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*User IP access list management*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Users*>, <*users/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    todefnum('User');
    query ("SELECT Name FROM Users WHERE Id=$User", 'users');
    if ($NUM_ROWS) { 
	fetchRow($users);
    ?>dnl
B_CURRENT
X_CURRENT(<*User account*>, <*<?php  pgetHVar($users,'Name'); ?>*>)
E_CURRENT
<P>
<?php  } ?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<TD>X_NEW_BUTTON(<*Add new IP address group*>, <*ipadd.php?User=<?php  p($User); ?>*>)</TD>
	<TD ALIGN="RIGHT">
	</TD>
</TABLE>

<P><?php 
    todefnum('IPOffs');
    if ($IPOffs < 0)
	$IPOffs= 0;
    
    query ("SELECT (StartIP & 0xff000000) >> 24 as ip0, (StartIP & 0x00ff0000) >> 16 as ip1, (StartIP & 0x0000ff00) >> 8 as ip2, StartIP & 0x000000ff as ip3, StartIP, Addresses FROM SubsByIP WHERE IdUser = $User LIMIT $IPOffs, 11", 'IPs');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i= 10;
	$color= 0;
	?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Start IP*>)
		X_LIST_TH(<*Number of addresses*>)
		X_LIST_TH(<*Delete*>, <*1%*>)
	E_LIST_HEADER
<?php 
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($IPs);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<?php  p(getHVar($IPs,'ip0').'.'.getHVar($IPs,'ip1').'.'.getHVar($IPs,'ip2').'.'.getHVar($IPs,'ip3') ); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<?php  pgetHVar($IPs,'Addresses'); ?>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<?php  putGS('Delete IP Group $1',getHVar($IPs,'StartIP') ); ?>*>, <*icon/delete.png*>, <*users/ipdel.php?User=<?php  p($User); ?>&StartIP=<?php  pgetVar($IPs,'StartIP'); ?>*>)
		E_LIST_ITEM
	E_LIST_TR
<?php  
    $i--;
    }
}
?>dnl
	B_LIST_FOOTER
<?php  if ($IPOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*ipaccesslist.php?User=<?php  p($User); ?>&IPOffs=<?php  p($IPOffs - 10); ?>*>)
<?php  } ?>dnl
<?php  if ($nr < 11) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*ipaccesslist.php?User=<?php  p($User); ?>&IPOffs=<?php  p($IPOffs + 10); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No records.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

