B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Topics*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? } 
    query ("SELECT * FROM Topics WHERE 1=0", 'categ');
?>dnl
E_HEAD

<? if ($access) { 
SET_ACCESS(<*mca*>, <*ManageTopics*>)
?>dnl

B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Topics*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
	todefnum('IdCateg');
	todef('Path');
	todef('Top');
	if($IdCateg != 0) $Top="<A HREF=index.php> Top </A>";
	todef('cCateg');
?>dnl

<?
	if($cCateg != ""){
		query ("SELECT * FROM Topics WHERE Name = '$cCateg'", 'q_cat');
		if($NUM_ROWS) {
			fetchRow($q_cat);
			$IdCateg = getVar($q_cat, 'Id');
		}
	}
?>

B_CURRENT
	<?
		$crtCat = $IdCateg;
		while($crtCat != 0){
			query ("SELECT * FROM Topics WHERE Id = $crtCat", 'q_cat');
			fetchRow($q_cat);									//should I release the resource ?
			$Path= "<A HREF=index.php?IdCateg=".getVar($q_cat, 'Id')."> ".getVar($q_cat,'Name')."</A>/".$Path;
			$crtCat =getVar($q_cat, 'ParentId');
		}
		$Path=$Top."/".$Path;
		if($Path == '') $Path="/";
	?>
	X_CURRENT(<*Topic*>, <*<B><?p($Path);?></B>*>)
E_CURRENT
<P>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<TD ALIGN="LEFT">X_NEW_BUTTON(<*Add new topic*>, <*add.php?IdCateg=<?p($IdCateg);?>&Back=<? pencURL($REQUEST_URI); ?>*>)</TD>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*index.php*>)
		<TD><? putGS('Topic'); ?>:</TD>
		<TD><INPUT TYPE="TEXT" NAME="cCateg" SIZE="8" MAXLENGTH="20"></TD>
		<TD>SUBMIT(<*Search*>, <*Search*>)</TD>
		<INPUT TYPE="HIDDEN" NAME="IdCateg" VALUE="<? p($IdCateg); ?>">
	E_SEARCH_DIALOG
	</TD>
</TABLE>
<?
	todefnum('CatOffs');
	if ($CatOffs < 0) $CatOffs= 0;
	$lpp=10;
    
	query ("SELECT * FROM Topics WHERE ParentId = $IdCateg ORDER BY Name LIMIT $CatOffs, ".($lpp+1), 'categ');
	if ($NUM_ROWS) {
		$nr= $NUM_ROWS;
		$i= $lpp;
		$color= 0;
	?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Name*>)
		X_LIST_TH(<*Change*>, <*1%*>)
		X_LIST_TH(<*Delete*>, <*1%*>)
	E_LIST_HEADER
<?
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($categ);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<A HREF="index.php?IdCateg=<?pgetVar($categ,'Id');?>"><? pgetHVar($categ,'Name'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="edit.php?IdCateg=<?p($IdCateg);?>&EdCateg=<? pgetVar($categ,'Id'); ?>"><? putGS("Change"); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<? putGS('Delete topic $1',getHVar($categ,'Name')); ?>*>, <*icon/x.gif*>, <*topics/del.php?IdCateg=<?p($IdCateg);?>&DelCateg=<? pgetVar($categ,'Id'); ?>*>)
		E_LIST_ITEM
    E_LIST_TR
<?
    $i--;
    }
} ?>dnl
	B_LIST_FOOTER
<? if ($CatOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?IdCateg=<?p($IdCateg);?>&CatOffs=<? print ($CatOffs - $lpp); ?>*>)
<? } ?>dnl
<? if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?IdCateg=<?p($IdCateg);?>&CatOffs=<? print ($CatOffs + $lpp); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No topics'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
