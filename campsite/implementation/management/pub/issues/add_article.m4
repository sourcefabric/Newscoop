INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*AddArticle*>)

B_HEAD
	X_TITLE(<*Add new article*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add articles.*>)
<?php  }
    
    query ("SELECT * FROM Issues WHERE 1=0", 'q_iss');
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php  todefnum('Pub'); ?>dnl
B_HEADER(<*Add new article*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/add_article.php*>)
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

<P>
X_BULLET(<*Select the issue*>)

<?php  $IssNr= "xxxxxxxxx"; ?>dnl
<P><?php 
    todefnum('IssOffs');
    if ($IssOffs < 0)
	$IssOffs= 0;
        $lpp=10;
    
    query ("SELECT Name, IdLanguage, Number, Name, if(Published='Y', PublicationDate, 'No') as Pub FROM Issues  WHERE IdPublication=$Pub ORDER BY Number DESC LIMIT $IssOffs, ".($lpp+1), 'q_iss');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i= $lpp;
                        if($nr < $lpp) $i = $nr;
	$color= 0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Nr*>, <*1%*>)
		X_LIST_TH(<*Name*>)
		X_LIST_TH(<*Language*>)
		X_LIST_TH(<*Published<BR><SMALL>(yyyy-mm-dd)</SMALL>*>, <*1%*>)
	E_LIST_HEADER

<?php 
    for($loop=0;$loop<$i;$loop++) {
	fetchRow($q_iss); ?>dnl
	B_LIST_TR
		B_LIST_ITEM(<*RIGHT*>)
 <?php  if ($IssNr != getVar($q_iss,'Number')) {
		pgetHVar($q_iss,'Number');
	    }
	    else {
		p('&nbsp;');
	    }
	?>dnl
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/add_article.php?Pub=<?php  p($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>"><?php  pgetHVar($q_iss,'Name'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM
 <?php 
	    query ("SELECT Name FROM Languages WHERE Id=".getSVar($q_iss,'IdLanguage'), 'language');
	    $nr2=$NUM_ROWS;
	    for($loop2=0;$loop2<$nr2;$loop2++) {
		fetchRow($language);
		pgetHVar($language,'Name');
	    }
	    ?>dnl
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<?php  pgetHVar($q_iss,'Pub'); ?>
		E_LIST_ITEM
	E_LIST_TR
<?php 
    $IssNr=getVar($q_iss,'Number');
}
?>dnl
	B_LIST_FOOTER
<?php  if ($IssOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*add_article.php?Pub=<?php  p($Pub); ?>&IssOffs=<?php  p($IssOffs - $lpp); ?>*>)
<?php  }

    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*add_article.php?Pub=<?php  p($Pub); ?>&IssOffs=<?php  p($IssOffs + $lpp); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No issues.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
