INCLUDE_PHP_LIB(<*$ADMIN_DIR/glossary/keyword*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE(<*Keyword infotypes*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  }
    query ("SELECT IdClasses FROM KeywordClasses WHERE 1=0", 'q_kwdcls'); ?>dnl
E_HEAD

<?php  if ($access) {
SET_ACCESS(<*mda*>, <*ManageDictionary*>)
 ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Keyword');
    todefnum('Language');
?>dnl
B_HEADER(<*Keyword infotypes*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary*>, <*glossary/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT Keyword FROM Dictionary WHERE Id=$Keyword AND IdLanguage=$Language", 'q_dict');
    if ($NUM_ROWS) {
	query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
	fetchRow($q_lang);
	fetchRow($q_dict);
?>dnl
B_CURRENT
X_CURRENT(<*Keyword*>, <*<?php  pgetHVar($q_dict,'Keyword'); ?>*>)
X_CURRENT(<*Language*>, <*<?php  pgetHVar($q_lang,'Name'); ?>*>)
E_CURRENT

<?php  if ($mda != 0) { ?>
<P>X_NEW_BUTTON(<*Add new keyword infotype*>, <*add.php?Keyword=<?php  print encURL($Keyword); ?>&Language=<?php  print encURL($Language); ?>*>)
<?php  } ?>

<P><?php 
    todefnum('KwdOffs');
    if ($KwdOffs < 0) $KwdOffs= 0;
    $lpp=20;
    query ("SELECT IdClasses FROM KeywordClasses WHERE IdDictionary=$Keyword AND IdLanguage=$Language LIMIT $KwdOffs, ".($lpp+1), 'q_kwdcls');
    if ($NUM_ROWS) { 
	$nr= $NUM_ROWS;
	$i=$lpp;
	$color=0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Infotype*>)
	<?php  if ($mda != 0) { ?>
		X_LIST_TH(<*Edit*>, <*1%*>)
		X_LIST_TH(<*Delete*>, <*1%*>)
	<?php  } ?>
	E_LIST_HEADER
<?php 
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_kwdcls);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
<?php 
    $NUM_ROWS= 0;
    query ("SELECT Name FROM Classes WHERE Id=".getVar($q_kwdcls,'IdClasses')." AND IdLanguage=$Language", 'q_cls');
    if ($NUM_ROWS) { 
	fetchRow($q_cls);
	pgetVar($q_cls,'Name');
    } else {
	print '&nbsp;';
    }
    
?>dnl
		E_LIST_ITEM
	<?php  if ($mda != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/glossary/keyword/edit.php?Keyword=<?php  print encURL($Keyword); ?>&Class=<?php  pgetUVar($q_kwdcls,'IdClasses'); ?>&Language=<?php  print encURL($Language); ?>">Edit</A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*Unlink infotype*>, <*icon/x.gif*>, <*glossary/keyword/del.php?Keyword=<?php  print encURL($Keyword); ?>&Class=<?php  pgetUVar($q_kwdcls,'IdClasses'); ?>&Language=<?php  print encURL($Language); ?>*>)
		E_LIST_ITEM
	<?php  } ?>
	E_LIST_TR
<?php 
    $i--;
    }
}
?>dnl
	B_LIST_FOOTER
<?php  if ($KwdOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*index.php?Keyword=<?php  print encURL($Keyword); ?>&Language=<?php  print encURL($Language); ?>&KwdOffs=<?php  print ($KwdOffs - $lpp); ?>*>)
<?php  }
    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*index.php?Keyword=<?php  print encURL($Keyword); ?>&Language=<?php  print encURL($Language); ?>&KwdOffs=<?php  print ($KwdOffs + $lpp); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No infotypes for this keyword.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such keyword.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

