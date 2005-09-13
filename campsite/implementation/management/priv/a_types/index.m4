INCLUDE_PHP_LIB(<*$ADMIN_DIR/a_types*>)
B_DATABASE

<?php  query ("SHOW TABLES LIKE 'XXYYZZ'", 'ATypes'); ?>dnl
CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE(<*Article Types*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  } ?>dnl
E_HEAD

<?php  if ($access) {

SET_ACCESS(<*mata*>, <*ManageArticleTypes*>)
SET_ACCESS(<*data*>, <*DeleteArticleTypes*>)

?>

B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Article Types*>)
B_HEADER_BUTTONS
E_HEADER_BUTTONS
E_HEADER

<?php  if ($mata != 0) { ?>dnl
<P>X_NEW_BUTTON(<*Add new article type*>, <*add.php?Back=<?php  print urlencode($_SERVER['REQUEST_URI']); ?>*>)
<?php  } ?>dnl

<P>
<?php 
    query ("SHOW TABLES LIKE 'X%'", 'ATypes');
    if ($NUM_ROWS) {
	todefnum('ATOffs');
	if ($ATOffs <= 0)  $ATOffs= 0;
	todefnum('lpp', 20);
	$be= $ATOffs;
	$en= 0;
	$color= 0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Type*>)
		X_LIST_TH(<*Fields*>, <*1%*>)
<?php  if ($data != 0) { ?>dnl
		X_LIST_TH(<*Delete*>, <*1%*>)
<?php  } ?>dnl
	E_LIST_HEADER
<?php 
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRowNum($ATypes);
	if (0 < $be)
	    $be--;
	else {
	    if ($en < $lpp) {
		$table=substr ( getNumVar($ATypes,0),1);
	    ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<?php  print encHTML($table); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/a_types/fields/?AType=<?php  print encURL($table); ?>"><?php  putGS('Fields'); ?></A>
		E_LIST_ITEM
<?php  if ($data != 0) { ?>dnl
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<?php  putGS('Delete article type $1', encHTML($table)); ?>*>, <*/x.gif*>, <*a_types/del.php?AType=<?php  print encURL($table); ?>*>)
		E_LIST_ITEM
<?php  } ?>dnl
	E_LIST_TR
<?php  }
    $en++;
    }
    } 
    
    ?>dnl
	B_LIST_FOOTER
<?php  if ($ATOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*X_ROOT/a_types/?ATOffs=<?php  print ($ATOffs - $lpp); ?>*>)
<?php  }
    if ($lpp < $en) { ?>dnl
		X_NEXT_A(<*X_ROOT/a_types/?ATOffs=<?php  print ($ATOffs + $lpp); ?>*>)
<?php  } else { ?>dnl
		X_NEXT_I
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No article types.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

