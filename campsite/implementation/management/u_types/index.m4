INCLUDE_PHP_LIB(<*$ADMIN_DIR/u_types*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE(<*User types*>)
<?php  if ($access ==0) { ?>dnl
	X_LOGOUT
<?php  query ("SELECT * FROM UserTypes WHERE 1=0", 'UTypes'); 
 } ?>dnl
E_HEAD

<?php  if ($access) {
SET_ACCESS(<*muta*>, <*ManageUserTypes*>)
?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*User types*>)
B_HEADER_BUTTONS
E_HEADER_BUTTONS
E_HEADER

<?php  if ($muta != 0) { ?>
<P>X_NEW_BUTTON(<*Add new user type*>, <*add.php?Back=<?php  print encURL($REQUEST_URI); ?>*>)
<?php  } ?>

<P><?php  
    todefnum('UTOffs');
    if ($UTOffs < 0) $UTOffs= 0;
    todefnum('lpp', 20);
    query ("SELECT * FROM UserTypes ORDER BY Name LIMIT $UTOffs, ".($lpp+1), 'UTypes');
    if ($NUM_ROWS) {
	$nr=$NUM_ROWS;
	$i=$lpp;
	$color= 0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Type*>)
		X_LIST_TH(<*Reader*>, <*1%*>)
	<?php  if ($muta != 0) { ?> 
		X_LIST_TH(<*Access*>, <*1%*>)
		X_LIST_TH(<*Delete*>, <*1%*>)
	<?php  } ?>
	E_LIST_HEADER
	<?php 
	    for ($loop=0;$loop<$nr;$loop++) {
		fetchRow($UTypes);
		if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<?php  pgetHVar($UTypes, 'Name'); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<?php 
			    if (getVar($UTypes,'Reader') == "Y")
				putGS("Yes");
			    else
				putGS("No");
			?>
		E_LIST_ITEM
	<?php  if ($muta !=0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="access.php?UType=<?php  pgetUVar($UTypes,'Name'); ?>"><?php  putGS('Change'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*Delete user type <?php  pgetHVar($UTypes,'Name'); ?>*>, <*icon/x.gif*>, <*u_types/del.php?UType=<?php  pgetUVar($UTypes,'Name'); ?>*>)
		E_LIST_ITEM
	<?php  } ?>
	E_LIST_TR
<?php 
    $i--;
    }
} ?>dnl
	B_LIST_FOOTER
<?php  if ($UTOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*index.php?UTOffs=<?php  print ($UTOffs - $lpp); ?>*>)
<?php  } 
    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*index.php?UTOffs=<?php  print ($UTOffs + $lpp); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No user types.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

