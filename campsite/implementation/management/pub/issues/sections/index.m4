INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues/sections*>)
B_DATABASE

<?php  query ("SELECT * FROM Sections WHERE 1=0", 'q_sect'); ?>dnl
CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE(<*Sections*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE
<?php 
SET_ACCESS(<*aaa*>, <*AddArticle*>)
SET_ACCESS(<*msa*>, <*ManageSection*>)
SET_ACCESS(<*dsa*>, <*DeleteSection*>)
?>
B_BODY

<?php  
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
?>dnl

B_HEADER(<*Sections*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  pencURL($Pub); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS) {
	fetchRow($q_iss);
	query ("SELECT * FROM Publications WHERE Id=".getVar($q_iss,'IdPublication'), 'q_pub');
	if ($NUM_ROWS) {
	    fetchRow($q_pub);
	    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_language');
	    fetchRow($q_language);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
X_CURRENT(<*Issue*>, <*<?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_language,'Name'); ?>)*>)
E_CURRENT

<?php  if ($msa != 0) { ?>
<P>X_NEW_BUTTON(<*Add new section*>, <*add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
<?php  } ?>

<P><?php 
    todefnum('SectOffs');
    if ($SectOffs < 0)	$SectOffs= 0;
    todefnum('lpp', 20);

    query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language ORDER BY Number LIMIT $SectOffs, ".($lpp+1), 'q_sect');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i=$lpp;
	if($nr < $lpp) $i = $nr;
	$color=0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Nr*>, <*1%*>)
		X_LIST_TH(<*Name<BR><SMALL>(click to see articles)</SMALL>*>)
		X_LIST_TH(<*Short Name*>)
	<?php  if ($msa != 0) { ?>
		X_LIST_TH(<*Configure*>, <*1%*>)
	<?php 	} ?>
	<?php  if ($msa != 0 && $aaa != 0) { ?>
		X_LIST_TH(<*Duplicate*>, <*1%*>)
	<?php 	} ?>
	<?php 	if($dsa != 0) { ?>
		X_LIST_TH(<*Delete*>, <*1%*>)
	<?php  } ?>
	E_LIST_HEADER
<?php 
	for($loop=0;$loop<$i;$loop++) {
	fetchRow($q_sect); ?>dnl
	B_LIST_TR
		B_LIST_ITEM(<*RIGHT*>)
			<?php  pgetHVar($q_sect,'Number'); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  pgetUVar($q_sect,'NrIssue'); ?>&Section=<?php  pgetUVar($q_sect,'Number'); ?>&Language=<?php  pgetUVar($q_sect,'IdLanguage'); ?>"><?php  pgetHVar($q_sect,'Name'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM
			<?php  pgetHVar($q_sect,'ShortName'); ?>
		E_LIST_ITEM
	<?php  if ($msa != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/pub/issues/sections/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  pgetUVar($q_sect,'NrIssue'); ?>&Section=<?php  pgetUVar($q_sect,'Number'); ?>&Language=<?php  pgetUVar($q_sect,'IdLanguage'); ?>"><img src="/<?php echo $ADMIN; ?>/img/icon/configure.png" alt="<?php  putGS("Configure"); ?>" border="0"></A>
		E_LIST_ITEM
	<?php 	} ?>
	<?php  if ($msa != 0 && $aaa != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/pub/issues/sections/duplicate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  pgetUVar($q_sect,'Number'); ?>&Language=<?php  p($Language); ?>"><img src="/<?php echo $ADMIN; ?>/img/icon/duplicate.png" alt="Duplicate" border="0"></A>
		E_LIST_ITEM
	<?php 	} ?>
	<?php 	if ($dsa != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<?php  putGS('Delete section $1',getHVar($q_sect,'Name')); ?>*>, <*icon/x.gif*>, <*pub/issues/sections/del.php?Pub=<?php  p($Pub); ?>&Issue=<?php  pgetUVar($q_sect,'NrIssue'); ?>&Section=<?php  pgetUVar($q_sect,'Number'); ?>&Language=<?php  pgetUVar($q_sect,'IdLanguage'); ?>*>)
		E_LIST_ITEM
	<?php  } ?>
	E_LIST_TR
<?php 
}
?>dnl
	B_LIST_FOOTER
<?php  if ($SectOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*index.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&SectOffs=<?php  p ($SectOffs - $lpp); ?>*>)
<?php  }

    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*index.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&SectOffs=<?php  p ($SectOffs + $lpp); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No sections'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

