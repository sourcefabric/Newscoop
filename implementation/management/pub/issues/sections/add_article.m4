INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues/sections*>)
B_DATABASE

<?php  query ("SELECT * FROM Sections WHERE 1=0", 'q_sect'); ?>dnl
CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE(<*Add new article*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add articles.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php  
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
?>dnl

B_HEADER(<*Add new article*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/add_article.php?Pub=<?php  p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/add_article.php*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS) {
        fetchRow($q_iss);
	query ("SELECT * FROM Publications WHERE Id=".getSVar($q_iss,'IdPublication'), 'q_pub');
	if ($NUM_ROWS) { 
	    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_language');
	    fetchRow($q_language);
	    fetchRow($q_pub);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_language,'Name'); ?>)</B>*>)
E_CURRENT

<P>
X_BULLET(<*Select the section*>)

<P><?php 
    todefnum('SectOffs');
    if ($SectOffs < 0)
	$SectOffs= 0;
        todefnum('lpp', 10);
    
    query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language ORDER BY Number LIMIT $SectOffs, ".($lpp+1), 'q_sect');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i= $lpp;
                        if($nr < $lpp)    $i = $nr;
	$color= 0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Nr*>, <*1%*>)
		X_LIST_TH(<*Name<BR><SMALL>(click to add article)</SMALL>*>)
	E_LIST_HEADER
<?php 
    for($loop=0;$loop<$i;$loop++) {
	fetchRow($q_sect); ?>dnl
	B_LIST_TR
		B_LIST_ITEM(<*RIGHT*>)
			<?php  pgetHVar($q_sect,'Number'); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/articles/add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  pgetUVar($q_sect,'NrIssue'); ?>&Section=<?php  pgetUVar($q_sect,'Number'); ?>&Language=<?php  pgetUVar($q_sect,'IdLanguage'); ?>&Wiz=1"><?php  pgetHVar($q_sect,'Name'); ?></A>
		E_LIST_ITEM
	E_LIST_TR
<?php 
}
?>dnl
	B_LIST_FOOTER
<?php  if ($SectOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
                                   X_PREV_A(<*add_article.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&SectOffs=<?php  p ($SectOffs - $lpp); ?>*>)
<?php  } ?>dnl
<?php  if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
                                   X_NEXT_A(<*add_article.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&SectOffs=<?php  p ($SectOffs + $lpp); ?>*>)
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
	<LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
