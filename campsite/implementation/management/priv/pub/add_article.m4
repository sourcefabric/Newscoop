INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*AddArticle*>)

B_HEAD
	X_TITLE(<*Add new article*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add articles.*>)
<?php  }
    query ("SELECT * FROM Publications WHERE 1=0", 'publ');
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new article*>)
B_HEADER_BUTTONS
E_HEADER_BUTTONS
E_HEADER

<P>
X_BULLET(<*Select the publication*>)

<P><?php 
    todefnum('PubOffs');
    if ($PubOffs < 0)
	$PubOffs= 0;

    query ("SELECT * FROM Publications ORDER BY Name LIMIT $PubOffs, 11", 'publ');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i=10;
	$color=0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Name<BR><SMALL>(click to select the publication)</SMALL>*>)
		X_LIST_TH(<*Site*>, <*20%*>)
	E_LIST_HEADER
<?php 
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($publ);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<A HREF="X_ROOT/issues/add_article.php?Pub=<?php  pgetUVar($publ,'Id'); ?>"><?php  pgetHVar($publ,'Name'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM
			<?php  pgetHVar($publ,'Site'); ?>&nbsp;
		E_LIST_ITEM
    E_LIST_TR
<?php 
    $i--;
    }
}
?>dnl
	B_LIST_FOOTER
<?php 
    if ($PubOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*add_article.php?PubOffs=<?php  print  ($PubOffs - 10); ?>*>)
<?php  }
    if ($nr < 11) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*add_article.php?PubOffs=<?php  print  ($PubOffs + 10); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No publications.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
