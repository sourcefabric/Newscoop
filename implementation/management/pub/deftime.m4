B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Subscription Default Time*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? } 
    query ("SELECT * FROM SubsDefTime WHERE 1=0", 'q_deft');
    
?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('Pub');
    todefnum('Language', 1);
    
?>dnl
B_HEADER(<*Subscription Default Time*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) { 
	fetchRow($q_pub);    
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
E_CURRENT

<P>X_NEW_BUTTON(<*Add new country*>, <*countryadd.php?Pub=<? pencURL($Pub); ?>&Language=<? pencURL($Language); ?>*>)

<P><?
    todefnum('ListOffs');
    if ($ListOffs < 0)
	$ListOffs= 0;
	
    query ("SELECT * FROM SubsDefTime WHERE IdPublication=$Pub ORDER BY CountryCode LIMIT $ListOffs, 11", 'q_deft');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i=10;
	$color=0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Country<BR><SMALL>(click to edit)</SMALL>*>)
		X_LIST_TH(<*Trial Period*>, <*1%*>, <*nowrap*>)
		X_LIST_TH(<*Paid Period*>, <*1%*>, <*nowrap*>)
		X_LIST_TH(<*Delete*>, <*1%*>)
	E_LIST_HEADER
<? 
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_deft);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
<? query ("SELECT * FROM Countries WHERE Code = '".getVar($q_deft,'CountryCode')."' AND IdLanguage = $Language", 'q_ctr'); 
    fetchRow($q_ctr);
    ?>dnl
			<A HREF="X_ROOT/pub/editdeftime.php?Pub=<? pencURL($Pub); ?>&CountryCode=<? pgetHVar($q_deft,'CountryCode'); ?>&Language=<? pencURL($Language); ?>"><? pgetHVar($q_ctr,'Name'); ?> (<? pgetHVar($q_ctr,'Code'); ?>)</A>
		E_LIST_ITEM
		B_LIST_ITEM(<*RIGHT*>)
			<? pgetHVar($q_deft,'TrialTime'); ?>
		E_LIST_ITEM
		B_LIST_ITEM(<*RIGHT*>)
			<? pgetHVar($q_deft,'PaidTime'); ?>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<? putGS('Delete entry $1',getHVar($q_deft,'CountryCode')); ?>*>, <*icon/x.gif*>, <*pub/deldeftime.php?Pub=<? pencURL($Pub); ?>&CountryCode=<? pgetUVar($q_deft,'CountryCode'); ?>&Language=<? pencURL($Language); ?>*>)
		E_LIST_ITEM
	E_LIST_TR
<?
    $i--;
    }
} 
?>dnl
	B_LIST_FOOTER(9)
<? if ($ListOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?Pub=<? pencURL($Pub); ?>&ListOffs=<? print ($ListOffs - 10); ?>*>)
<? }
    if ($nr < 11) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?Pub=<? pencURL($Pub); ?>&ListOffs=<? print ($ListOffs + 10); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No entries defined.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? }  else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

