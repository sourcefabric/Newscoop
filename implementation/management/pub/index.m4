B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Publications*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? } 
	query ("SELECT * FROM Publications WHERE 1=0", 'publ');
	query("SELECT  Id as IdLang FROM Languages WHERE code='$TOL_Language'", 'q_lang');
	if($NUM_ROWS == 0){
		query("SELECT IdDefaultLanguage as IdLang  FROM Publications WHERE Id=1", 'q_lang');
	}
	fetchRow($q_lang);
	$IdLang = getVar($q_lang,'IdLang');

?>dnl
E_HEAD

<? if ($access) { 
SET_ACCESS(<*mpa*>, <*ManagePub*>)
SET_ACCESS(<*dpa*>, <*DeletePub*>)
?>dnl

B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Publications*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<? if ($mpa != 0) { ?>dnl
	<P>X_NEW_BUTTON(<*Add new publication*>, <*add.php?Back=<? pencURL($REQUEST_URI); ?>*>)
<? } ?>dnl 

<P><?
    todefnum('PubOffs');
     if ($PubOffs < 0) $PubOffs= 0;
	$lpp=20;
    
    query ("SELECT * FROM Publications ORDER BY Name LIMIT $PubOffs, ".($lpp+1), 'publ');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i= $lpp;
	$color= 0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Name<BR><SMALL>(click to see issues)</SMALL>*>)
		X_LIST_TH(<*Site*>, <*20%*>)
		X_LIST_TH(<*Default Language*>, <*20%*>)
	<? if ($mpa != 0) { ?>dnl 
		X_LIST_TH(<*Subscription Default Time*>, <*10%*>)
		X_LIST_TH(<*Pay Period*>, <*10%*>)
		X_LIST_TH(<*Unit Cost*>, <*10%*>)
		X_LIST_TH(<*Paid Period*>, <*10%*>)
		X_LIST_TH(<*Trial Period*>, <*10%*>)
		X_LIST_TH(<*Info*>, <*1%*>)
	<? }
	
	if ($dpa != 0) { ?>dnl
		X_LIST_TH(<*Delete*>, <*1%*>)
	<? } ?>dnl 
	E_LIST_HEADER
<?
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($publ);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/?Pub=<? pgetUVar($publ,'Id'); ?>"><? pgetHVar($publ,'Name'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM
			<? pgetHVar($publ,'Site'); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
			<? query ("SELECT Name FROM Languages WHERE Id=".getVar($publ,'IdDefaultLanguage'), 'q_dlng');
			fetchRow($q_dlng);
			pgetHVar($q_dlng,'Name');
			?>&nbsp;
		E_LIST_ITEM
<? if ($mpa != 0) { ?>dnl
		B_LIST_ITEM
			<a href="deftime.php?Pub=<? pgetUVar($publ,'Id'); ?>">Change</A>
		E_LIST_ITEM
		B_LIST_ITEM(<*RIGHT*>)
			<? query ("SELECT Name FROM TimeUnits where Unit = '".getHVar($publ,'TimeUnit')."' and IdLanguage = ".($IdLang), 'tu');
			    fetchRow($tu);
			 pgetHVar($publ,'PayTime'); p("&nbsp;"); pgetHVar($tu,'Name'); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<? pgetHVar($publ,'UnitCost'); p("&nbsp;"); pgetHVar($publ,'Currency');
			    ?>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<? pgetHVar($publ,'PaidTime'); p("&nbsp;"); pgetHVar($tu,'Name'); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<? pgetHVar($publ,'TrialTime'); p("&nbsp;"); pgetHVar($tu,'Name'); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/pub/edit.php?Pub=<? pgetUVar($publ,'Id'); ?>">Change</A>
		E_LIST_ITEM
<? }
    if ($dpa != 0) { ?>dnl 
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<? putGS('Delete publication $1',getHVar($publ,'Name')); ?>*>, <*icon/x.gif*>, <*pub/del.php?Pub=<? pgetVar($publ,'Id'); ?>*>)
		E_LIST_ITEM
<? } ?>dnl
    E_LIST_TR
<?
    $i--;
    }
} ?>dnl
	B_LIST_FOOTER
<? if ($PubOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?PubOffs=<? print ($PubOffs - $lpp); ?>*>)
<? } ?>dnl
<? if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?PubOffs=<? print ($PubOffs + $lpp); ?>*>)
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
