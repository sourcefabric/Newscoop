INCLUDE_PHP_LIB(<*article_type_fields*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE(<*Article type fields*>)
	<?php  if ($access == 0) { ?>
		X_LOGOUT
	<?php  }
    query ("SHOW COLUMNS FROM Articles LIKE 'XXYYZZ'", 'q_col'); ?>dnl
E_HEAD

<?php

todef('AType');
if ($access) {

SET_ACCESS(<*mata*>, <*ManageArticleTypes*>)

?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Article type fields*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Article Types*>, <*a_types/*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Article type*>, <*<?php  print encHTML($AType); ?>*>)
E_CURRENT

<?php  if ($mata != 0) { ?>
<P>X_NEW_BUTTON(<*Add new field*>, <*add.php?AType=<?php  print encHTML($AType); ?>*>)
<?php  } ?>


<P>
<?php  
    query ("SHOW COLUMNS FROM X$AType LIKE 'F%'", 'q_col'); 
    if ($NUM_ROWS) {
	todefnum('AFOffs');
	if ($AFOffs <= 0)   $AFOffs= 0;
	$lpp=20;
	$be= $AFOffs;
	$en= 0;
	$color= 0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Name*>)
		X_LIST_TH(<*Type*>)
	<?php  if ($mata != 0) { ?>
		X_LIST_TH(<*Delete*>, <*1%*>)
	<?php  } ?>
	E_LIST_HEADER
<?php 
    $nr=$NUM_ROWS;
    for ($loop=0;$loop<$nr;$loop++) {
	fetchRowNum($q_col);
	if (0 < $be)
	    $be--;
	else {
	    if ($en < $lpp) {
	    $table=substr ( getNumVar ( $q_col,0),1);
	    ?>
	B_LIST_TR
		B_LIST_ITEM
			<?php  print encHTML($table); ?>&nbsp;
    		E_LIST_ITEM
		B_LIST_ITEM
<?php 
    $desc=getNumVar($q_col,1);
    $desc=str_replace('mediumblob',getGS('Article body'),$desc);
    $desc=str_replace('varchar(255)',getGS('Text'),$desc);
    $desc=str_replace('varbinary(255)',getGS('Text'),$desc);
    $desc=str_replace('date',getGS('Date'),$desc);
    print encHTML($desc);
?>&nbsp;
		E_LIST_ITEM
	<?php  if ($mata != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<?php  putGS('Delete field $1',encHTML($table)); ?>*>, <*/x.gif*>, <*a_types/fields/del.php?AType=<?php  print encURL($AType); ?>&Field=<?php  print encURL($table); ?>*>)
		E_LIST_ITEM
	<?php  } ?>
	E_LIST_TR
<?php  }
    $en++;
    
    }	    
}
    ?>dnl
	B_LIST_FOOTER
<?php 
    if ($AFOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*X_ROOT/a_types/fields/?AType=<?php  print encURL($AType); ?>&AFOffs=<?php  print ($AFOffs - $lpp); ?>*>)
<?php  }
    if ($lpp < $en) { ?>dnl
		X_NEXT_A(<*X_ROOT/a_types/fields/?AType=<?php  print encURL($AType); ?>&AFOffs=<?php  print ($AFOffs + $lpp); ?>*>)
<?php  } else { ?>dnl
		X_NEXT_I
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No fields.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

