B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
<script>
<!--
/*
A slightly modified version of "Break-out-of-frames script"
By JavaScript Kit (http://javascriptkit.com)
*/

if (window != top.fmain && window != top) {
	if (top.fmenu)
		top.fmain.location.href=location.href
	else
		top.location.href=location.href
}
// -->
</script>
	X_EXPIRES
	X_TITLE(<*Publications*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  } 
	query ("SELECT * FROM Publications WHERE 1=0", 'publ');
	query("SELECT  Id as IdLang FROM Languages WHERE code='$TOL_Language'", 'q_lang');
	if($NUM_ROWS == 0){
		query("SELECT IdDefaultLanguage as IdLang  FROM Publications WHERE Id=1", 'q_lang');
	}
	fetchRow($q_lang);
	$IdLang = getVar($q_lang,'IdLang');

?>dnl
E_HEAD

<?php  if ($access) { 
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

<?php  if ($mpa != 0) { ?>dnl
	<P>X_NEW_BUTTON(<*Add new publication*>, <*add.php?Back=<?php  pencURL($REQUEST_URI); ?>*>)
<?php  } ?>dnl 

<P><?php 
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
	<?php  if ($mpa != 0) { ?>dnl 
		X_LIST_TH(<*Subscription Default Time*>, <*10%*>)
		X_LIST_TH(<*Pay Period*>, <*10%*>)
		X_LIST_TH(<*Unit Cost*>, <*10%*>)
		X_LIST_TH(<*Paid Period*>, <*10%*>)
		X_LIST_TH(<*Trial Period*>, <*10%*>)
		X_LIST_TH(<*Info*>, <*1%*>)
	<?php  }
	
	if ($dpa != 0) { ?>dnl
		X_LIST_TH(<*Delete*>, <*1%*>)
	<?php  } ?>dnl 
	E_LIST_HEADER
<?php 
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($publ);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/?Pub=<?php  pgetUVar($publ,'Id'); ?>"><?php  pgetHVar($publ,'Name'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM
			<?php  pgetHVar($publ,'Site'); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
			<?php  query ("SELECT Name FROM Languages WHERE Id=".getVar($publ,'IdDefaultLanguage'), 'q_dlng');
			fetchRow($q_dlng);
			pgetHVar($q_dlng,'Name');
			?>&nbsp;
		E_LIST_ITEM
<?php  if ($mpa != 0) { ?>dnl
		B_LIST_ITEM
			<a href="deftime.php?Pub=<?php  pgetUVar($publ,'Id'); ?>"><?php  putGS("Change"); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*RIGHT*>)
			<?php  query ("SELECT Name FROM TimeUnits where Unit = '".getHVar($publ,'TimeUnit')."' and IdLanguage = ".($IdLang), 'tu');
			    fetchRow($tu);
			 pgetHVar($publ,'PayTime'); p("&nbsp;"); pgetHVar($tu,'Name'); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<?php  pgetHVar($publ,'UnitCost'); p("&nbsp;"); pgetHVar($publ,'Currency');
			    ?>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<?php  pgetHVar($publ,'PaidTime'); p("&nbsp;"); pgetHVar($tu,'Name'); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<?php  pgetHVar($publ,'TrialTime'); p("&nbsp;"); pgetHVar($tu,'Name'); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/pub/edit.php?Pub=<?php  pgetUVar($publ,'Id'); ?>"><?php  putGS("Change"); ?></A>
		E_LIST_ITEM
<?php  }
    if ($dpa != 0) { ?>dnl 
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<?php  putGS('Delete publication $1',getHVar($publ,'Name')); ?>*>, <*icon/x.gif*>, <*pub/del.php?Pub=<?php  pgetVar($publ,'Id'); ?>*>)
		E_LIST_ITEM
<?php  } ?>dnl
    E_LIST_TR
<?php 
    $i--;
    }
} ?>dnl
	B_LIST_FOOTER
<?php  if ($PubOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*index.php?PubOffs=<?php  print ($PubOffs - $lpp); ?>*>)
<?php  } ?>dnl
<?php  if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*index.php?PubOffs=<?php  print ($PubOffs + $lpp); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No publications.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
