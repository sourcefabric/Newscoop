B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Article type fields*>)
	<? if ($access == 0) { ?>
		X_LOGOUT
	<? }
    query ("SHOW COLUMNS FROM Articles LIKE 'XXYYZZ'", 'q_col'); ?>dnl
E_HEAD

<? if ($access) { 

SET_ACCESS(<*mata*>, <*ManageArticleTypes*>)

?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Article type fields*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Article Types*>, <*a_types/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Article type*>, <*<B><? print encHTML($AType); ?></B>*>)
E_CURRENT

<? if ($mata != 0) { ?>
<P>X_NEW_BUTTON(<*Add new field*>, <*add.php?AType=<? print encHTML($AType); ?>*>)
<? } ?>


<P>
<? 
    query ("SHOW COLUMNS FROM X$AType LIKE 'F%'", 'q_col'); 
    if ($NUM_ROWS) {
	todefnum('AFOffs');
	if ($AFOffs <= 0)   $AFOffs= 0;
	$lpp=20;
	$be= $AFOffs;
	$en= 0;
	$color= 0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Name*>)
		X_LIST_TH(<*Type*>)
	<? if ($mata != 0) { ?>
		X_LIST_TH(<*Delete*>, <*1%*>)
	<? } ?>
	E_LIST_HEADER
<?
    $nr=$NUM_ROWS;
    for ($loop=0;$loop<$nr;$loop++) {
	fetchRowNum($q_col);
	if (0 < $be)
	    $be--;
	else {
	    if ($en < $lpp) {
	    $table=substr ( getNumVar ( $q_col,0),1);
	    ?>
	B_LIST_TR
		B_LIST_ITEM
			<? print encHTML($table); ?>&nbsp;
    		E_LIST_ITEM
		B_LIST_ITEM
<?
    $desc=getNumVar($q_col,1);
    $desc=str_replace('mediumblob',getGS('Article body'),$desc);
    $desc=str_replace('varchar(100)',getGS('Text'),$desc);
    $desc=str_replace('date',getGS('Date'),$desc);
    print encHTML($desc);
?>&nbsp;
		E_LIST_ITEM
	<? if ($mata != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<? putGS('Delete field $1',encHTML($table)); ?>*>, <*icon/x.gif*>, <*a_types/fields/del.php?AType=<? print encURL($AType); ?>&Field=<? print encURL($table); ?>*>)
		E_LIST_ITEM
	<? } ?>
	E_LIST_TR
<? }
    $en++;
    
    }	    
}
    ?>dnl
	B_LIST_FOOTER
<?
    if ($AFOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*X_ROOT/a_types/fields/?AType=<? print encURL($AType); ?>&AFOffs=<? print ($AFOffs - $lpp); ?>*>)
<? }
    if ($lpp < $en) { ?>dnl
		X_NEXT_A(<*X_ROOT/a_types/fields/?AType=<? print encURL($AType); ?>&AFOffs=<? print ($AFOffs + $lpp); ?>*>)
<? } else { ?>dnl
		X_NEXT_I
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No fields.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

