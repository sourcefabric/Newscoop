INCLUDE_PHP_LIB(<*$ADMIN_DIR/topics*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE(<*Topics*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  } 
    query ("SELECT * FROM Topics WHERE 1=0", 'categ');
?>dnl
E_HEAD

<?php  if ($access) { 
SET_ACCESS(<*mca*>, <*ManageTopics*>)
?>dnl

B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Topics*>)
B_HEADER_BUTTONS
E_HEADER_BUTTONS
E_HEADER

<?php 
	todefnum('IdCateg');
	todef('Path');
	todef('Top');
	if($IdCateg != 0) $Top="<A HREF=index.php> Top </A>";
	todef('cCateg');
?>dnl

<?php 
	if($cCateg != ""){
		query ("SELECT * FROM Topics WHERE Name = '$cCateg'", 'q_cat');
		if($NUM_ROWS) {
			fetchRow($q_cat);
			$IdCateg = getVar($q_cat, 'Id');
		}
	}
?>

B_CURRENT
	<?php 
		$crtCat = $IdCateg;
		while($crtCat != 0){
			query ("SELECT * FROM Topics WHERE Id = $crtCat", 'q_cat');
			fetchRow($q_cat);									//should I release the resource ?
			$Path= "<A HREF=index.php?IdCateg=".getVar($q_cat, 'Id')."> ".getVar($q_cat,'Name')."</A>/".$Path;
			$crtCat =getVar($q_cat, 'ParentId');
		}
		$Path=$Top."/".$Path;
		if($Path == '') $Path="/";
	?>
	X_CURRENT(<*Topic*>, <*<?php p($Path);?>*>)
E_CURRENT
<P>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
<?php  if ($mca) { ?>dnl
	<TD ALIGN="LEFT">X_NEW_BUTTON(<*Add new topic*>, <*add.php?IdCateg=<?php p($IdCateg);?>&Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>*>)</TD>
<?php  } ?>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*index.php*>)
		<TD><?php  putGS('Topic'); ?>:</TD>
		<TD><INPUT TYPE="TEXT" NAME="cCateg" SIZE="8" MAXLENGTH="20"></TD>
		<TD>SUBMIT(<*Search*>, <*Search*>)</TD>
		<INPUT TYPE="HIDDEN" NAME="IdCateg" VALUE="<?php  p($IdCateg); ?>">
	E_SEARCH_DIALOG
	</TD>
</TABLE>
<?php 
	todefnum('CatOffs');
	if ($CatOffs < 0) $CatOffs= 0;
	$lpp=10;
    
	query ("SELECT * FROM Topics WHERE ParentId = $IdCateg ORDER BY Name LIMIT $CatOffs, ".($lpp+1), 'categ');
	if ($NUM_ROWS) {
		$nr= $NUM_ROWS;
		$i= $lpp;
		$color= 0;
	?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Name*>)
<?php  if ($mca) { ?>dnl
		X_LIST_TH(<*Change*>, <*1%*>)
		X_LIST_TH(<*Delete*>, <*1%*>)
<?php  } ?>
	E_LIST_HEADER
<?php 
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($categ);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<A HREF="index.php?IdCateg=<?php pgetVar($categ,'Id');?>"><?php  pgetHVar($categ,'Name'); ?></A>
		E_LIST_ITEM
<?php  if ($mca) { ?>dnl
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="edit.php?IdCateg=<?php p($IdCateg);?>&EdCateg=<?php  pgetVar($categ,'Id'); ?>"><?php  putGS("Change"); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<?php  putGS('Delete topic $1',getHVar($categ,'Name')); ?>*>, <*icon/delete.png*>, <*topics/del.php?IdCateg=<?php p($IdCateg);?>&DelCateg=<?php  pgetVar($categ,'Id'); ?>*>)
		E_LIST_ITEM
<?php  } ?>
    E_LIST_TR
<?php 
    $i--;
    }
} ?>dnl
	B_LIST_FOOTER
<?php  if ($CatOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*index.php?IdCateg=<?php p($IdCateg);?>&CatOffs=<?php  print ($CatOffs - $lpp); ?>*>)
<?php  } ?>dnl
<?php  if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*index.php?IdCateg=<?php p($IdCateg);?>&CatOffs=<?php  print ($CatOffs + $lpp); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No topics'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
