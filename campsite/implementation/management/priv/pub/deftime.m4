INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE(<*Countries Subscription Default Time*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  } 
    query ("SELECT * FROM SubsDefTime WHERE 1=0", 'q_deft');
    
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Pub');
    todefnum('Language', 1);
    
?>dnl
B_HEADER(<*Countries Subscription Default Time*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) { 
	fetchRow($q_pub);    
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
E_CURRENT

<TABLE>
<TR>
	<TD>X_NEW_BUTTON(<*Add new country*>, <*countryadd.php?Pub=<?php  pencURL($Pub); ?>&Language=<?php  pencURL($Language); ?>*>)</TD>
	<TD>X_BACK_BUTTON(<*Back to publication*>, <*edit.php?Pub=<?php  pencURL($Pub); ?>*>)</TD>
</TR>
</TABLE>

<P><?php 
    todefnum('ListOffs');
    if ($ListOffs < 0)
	$ListOffs= 0;
	
    query ("SELECT * FROM SubsDefTime WHERE IdPublication=$Pub ORDER BY CountryCode LIMIT $ListOffs, 11", 'q_deft');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i=10;
	$color=0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Country<BR><SMALL>(click to edit)</SMALL>*>)
		X_LIST_TH(<*Trial Period*>, <*1%*>, <*nowrap*>)
		X_LIST_TH(<*Paid Period*>, <*1%*>, <*nowrap*>)
		X_LIST_TH(<*Delete*>, <*1%*>)
	E_LIST_HEADER
<?php  
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_deft);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
<?php  query ("SELECT * FROM Countries WHERE Code = '".getVar($q_deft,'CountryCode')."' AND IdLanguage = $Language", 'q_ctr'); 
    fetchRow($q_ctr);
    ?>dnl
			<A HREF="X_ROOT/pub/editdeftime.php?Pub=<?php  pencURL($Pub); ?>&CountryCode=<?php  pgetHVar($q_deft,'CountryCode'); ?>&Language=<?php  pencURL($Language); ?>"><?php  pgetHVar($q_ctr,'Name'); ?> (<?php  pgetHVar($q_ctr,'Code'); ?>)</A>
		E_LIST_ITEM
		B_LIST_ITEM(<*RIGHT*>)
			<?php  pgetHVar($q_deft,'TrialTime'); ?>
		E_LIST_ITEM
		B_LIST_ITEM(<*RIGHT*>)
			<?php  pgetHVar($q_deft,'PaidTime'); ?>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<?php  putGS('Delete entry $1',getHVar($q_deft,'CountryCode')); ?>*>, <*/delete.png*>, <*pub/deldeftime.php?Pub=<?php  pencURL($Pub); ?>&CountryCode=<?php  pgetUVar($q_deft,'CountryCode'); ?>&Language=<?php  pencURL($Language); ?>*>)
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

