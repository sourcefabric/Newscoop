B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Languages*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? } 
    query ("SELECT Id, Name, OrigName, CodePage, Code FROM Languages WHERE 1=0", 'Languages'); ?>dnl
E_HEAD

<? if ($access) {

SET_ACCESS(<*mla*>, <*ManageLanguages*>)
SET_ACCESS(<*dla*>, <*DeleteLanguages*>)

?>
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Languages*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<? if ($mla != 0) { ?>
<P>X_NEW_BUTTON(<*Add new language*>, <*add.php?Back=<? print encURL($REQUEST_URI); ?>*>)
<? } ?>

<P><?
    todefnum('LangOffs');
    if ($LangOffs < 0) $LangOffs= 0;
    $lpp = 20;
    query ("SELECT Id, Name, OrigName, CodePage, Code FROM Languages ORDER BY Name LIMIT $LangOffs, ".($lpp+1), 'Languages');
    if ($NUM_ROWS) { 
	$nr= $NUM_ROWS;
	$i= $lpp;
	$color= 0; ?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Language*>)
		X_LIST_TH(<*Native name*>)
		X_LIST_TH(<*Code*>)
		X_LIST_TH(<*Code page*>)
	<? if ($mla != 0) { ?>
		X_LIST_TH(<*Edit*>, <*1%*>)
	<? }
	if ($dla != 0) { ?>
		X_LIST_TH(<*Delete*>, <*1%*>)
	<? } ?>
	E_LIST_HEADER
<?
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($Languages);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<? pgetHVar($Languages,'Name'); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<? pgetHVar($Languages,'OrigName'); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<? pgetHVar($Languages,'Code'); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
			<? pgetHVar($Languages,'CodePage'); ?>&nbsp;
		E_LIST_ITEM
	<? if ($mla != 0) { ?> 
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="modify.php?Lang=<? pgetVar($Languages,'Id'); ?>">Edit</A>
		E_LIST_ITEM
	<? }
	if ($dla != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<? putGS('Delete language $1',getHVar($Languages,'Name')); ?>*>, <*icon/x.gif*>, <*languages/del.php?Language=<? pgetVar($Languages,'Id'); ?>*>)
		E_LIST_ITEM
	<? } ?>
	E_LIST_TR
<?
    $i--;
        }
    } ?>
	B_LIST_FOOTER
<? if ($LangOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?LangOffs=<? print ($LangOffs - $lpp); ?>*>)
<? }
    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?LangOffs=<? print ($LangOffs + $lpp); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No language.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

