B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

<? query ("SHOW TABLES LIKE 'XXYYZZ'", 'ATypes'); ?>dnl
CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Article Types*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? } ?>dnl
E_HEAD

<? if ($access) {

SET_ACCESS(<*mata*>, <*ManageArticleTypes*>)
SET_ACCESS(<*data*>, <*DeleteArticleTypes*>)

?>

B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Article Types*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<? if ($mata != 0) { ?>dnl
<P>X_NEW_BUTTON(<*Add new article type*>, <*add.php?Back=<? print encURL($REQUEST_URI); ?>*>)
<? } ?>dnl

<P>
<?
    query ("SHOW TABLES LIKE 'X%'", 'ATypes');
    if ($NUM_ROWS) {
	todefnum('ATOffs');
	if ($ATOffs <= 0)  $ATOffs= 0;
	todefnum('lpp', 20);
	$be= $ATOffs;
	$en= 0;
	$color= 0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Type*>)
		X_LIST_TH(<*Fields*>, <*1%*>)
<? if ($data != 0) { ?>dnl
		X_LIST_TH(<*Delete*>, <*1%*>)
<? } ?>dnl
	E_LIST_HEADER
<?
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRowNum($ATypes);
	if (0 < $be)
	    $be--;
	else {
	    if ($en < $lpp) {
		$table=substr ( getNumVar($ATypes,0),1);
	    ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<? print encHTML($table); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/a_types/fields/?AType=<? print encURL($table); ?>"><? putGS('Fields'); ?></A>
		E_LIST_ITEM
<? if ($data != 0) { ?>dnl
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<? putGS('Delete article type $1', encHTML($table)); ?>*>, <*icon/x.gif*>, <*a_types/del.php?AType=<? print encURL($table); ?>*>)
		E_LIST_ITEM
<? } ?>dnl
	E_LIST_TR
<? }
    $en++;
    }
    } 
    
    ?>dnl
	B_LIST_FOOTER
<? if ($ATOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*X_ROOT/a_types/?ATOffs=<? print ($ATOffs - $lpp); ?>*>)
<? }
    if ($lpp < $en) { ?>dnl
		X_NEXT_A(<*X_ROOT/a_types/?ATOffs=<? print ($ATOffs + $lpp); ?>*>)
<? } else { ?>dnl
		X_NEXT_I
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No article types.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

