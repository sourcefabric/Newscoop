B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*AddArticle*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add new article*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add articles.*>)
<? }
    query ("SELECT * FROM Publications WHERE 1=0", 'publ');
?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new article*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<P>
X_BULLET(<*Select the publication*>)

<P><?
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
<?
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($publ);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/add_article.php?Pub=<? pgetUVar($publ,'Id'); ?>"><? pgetHVar($publ,'Name'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM
			<? pgetHVar($publ,'Site'); ?>&nbsp;
		E_LIST_ITEM
    E_LIST_TR
<?
    $i--;
    }
}
?>dnl
	B_LIST_FOOTER
<?
    if ($PubOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*add_article.php?PubOffs=<? print  ($PubOffs - 10); ?>*>)
<? }
    if ($nr < 11) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*add_article.php?PubOffs=<? print  ($PubOffs + 10); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No publications.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
