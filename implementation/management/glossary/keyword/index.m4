B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Keyword infotypes*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? }
    query ("SELECT IdClasses FROM KeywordClasses WHERE 1=0", 'q_kwdcls'); ?>dnl
E_HEAD

<? if ($access) {
SET_ACCESS(<*mda*>, <*ManageDictionary*>)
 ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('Keyword');
    todefnum('Language');
?>dnl
B_HEADER(<*Keyword infotypes*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary*>, <*glossary/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT Keyword FROM Dictionary WHERE Id=$Keyword AND IdLanguage=$Language", 'q_dict');
    if ($NUM_ROWS) {
	query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
	fetchRow($q_lang);
	fetchRow($q_dict);
?>dnl
B_CURRENT
X_CURRENT(<*Keyword*>, <*<B><? pgetHVar($q_dict,'Keyword'); ?></B>*>)
X_CURRENT(<*Language*>, <*<B><? pgetHVar($q_lang,'Name'); ?></B>*>)
E_CURRENT

<? if ($mda != 0) { ?>
<P>X_NEW_BUTTON(<*Add new keyword infotype*>, <*add.php?Keyword=<? print encURL($Keyword); ?>&Language=<? print encURL($Language); ?>*>)
<? } ?>

<P><?
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
	<? if ($mda != 0) { ?>
		X_LIST_TH(<*Edit*>, <*1%*>)
		X_LIST_TH(<*Delete*>, <*1%*>)
	<? } ?>
	E_LIST_HEADER
<?
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_kwdcls);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
<?
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
	<? if ($mda != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/glossary/keyword/edit.php?Keyword=<? print encURL($Keyword); ?>&Class=<? pgetUVar($q_kwdcls,'IdClasses'); ?>&Language=<? print encURL($Language); ?>">Edit</A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*Unlink infotype*>, <*icon/x.gif*>, <*glossary/keyword/del.php?Keyword=<? print encURL($Keyword); ?>&Class=<? pgetUVar($q_kwdcls,'IdClasses'); ?>&Language=<? print encURL($Language); ?>*>)
		E_LIST_ITEM
	<? } ?>
	E_LIST_TR
<?
    $i--;
    }
}
?>dnl
	B_LIST_FOOTER
<? if ($KwdOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?Keyword=<? print encURL($Keyword); ?>&Language=<? print encURL($Language); ?>&KwdOffs=<? print ($KwdOffs - $lpp); ?>*>)
<? }
    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?Keyword=<? print encURL($Keyword); ?>&Language=<? print encURL($Language); ?>&KwdOffs=<? print ($KwdOffs + $lpp); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No infotypes for this keyword.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such keyword.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

