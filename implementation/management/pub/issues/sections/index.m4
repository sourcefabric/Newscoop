B_HTML
INCLUDE_PHP_LIB(<*../../..*>)
B_DATABASE

<? query ("SELECT * FROM Sections WHERE 1=0", 'q_sect'); ?>dnl
CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Sections*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE
<?
SET_ACCESS(<*msa*>, <*ManageSection*>)
SET_ACCESS(<*dsa*>, <*DeleteSection*>)
?>
B_BODY

<? 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
?>dnl

B_HEADER(<*Sections*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? pencURL($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS) {
	fetchRow($q_iss);
	query ("SELECT * FROM Publications WHERE Id=".getVar($q_iss,'IdPublication'), 'q_pub');
	if ($NUM_ROWS) {
	    fetchRow($q_pub);
	    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_language');
	    fetchRow($q_language);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($q_iss,'Number'); ?>. <? pgetHVar($q_iss,'Name'); ?> (<? pgetHVar($q_language,'Name'); ?>)</B>*>)
E_CURRENT

<? if ($msa != 0) { ?>
<P>X_NEW_BUTTON(<*Add new section*>, <*add.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>*>)
<? } ?>

<P><?
    todefnum('SectOffs');
    if ($SectOffs < 0)	$SectOffs= 0;
    todefnum('lpp', 20);
    
    query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language ORDER BY Number LIMIT $SectOffs, ".($lpp+1), 'q_sect');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i=$lpp;
                if($nr < $lpp) $i = $nr;
	$color=0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Nr*>, <*1%*>)
		X_LIST_TH(<*Name<BR><SMALL>(click to see articles)</SMALL>*>)
	<? if ($msa != 0) { ?>
		X_LIST_TH(<*Change*>, <*1%*>)
	<? }

	    if($dsa != 0) { ?>
		X_LIST_TH(<*Delete*>, <*1%*>)
	<? } ?>
	E_LIST_HEADER
<?
    for($loop=0;$loop<$i;$loop++) {
	fetchRow($q_sect); ?>dnl
	B_LIST_TR
		B_LIST_ITEM(<*RIGHT*>)
			<? pgetHVar($q_sect,'Number'); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? pgetUVar($q_sect,'NrIssue'); ?>&Section=<? pgetUVar($q_sect,'Number'); ?>&Language=<? pgetUVar($q_sect,'IdLanguage'); ?>"><? pgetHVar($q_sect,'Name'); ?></A>
		E_LIST_ITEM
	<? if ($msa != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/pub/issues/sections/edit.php?Pub=<? p($Pub); ?>&Issue=<? pgetUVar($q_sect,'NrIssue'); ?>&Section=<? pgetUVar($q_sect,'Number'); ?>&Language=<? pgetUVar($q_sect,'IdLanguage'); ?>">Change</A>
		E_LIST_ITEM
	<? }

	    if ($dsa != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<? putGS('Delete section $1',getHVar($q_sect,'Name')); ?>*>, <*icon/x.gif*>, <*pub/issues/sections/del.php?Pub=<? p($Pub); ?>&Issue=<? pgetUVar($q_sect,'NrIssue'); ?>&Section=<? pgetUVar($q_sect,'Number'); ?>&Language=<? pgetUVar($q_sect,'IdLanguage'); ?>*>)
		E_LIST_ITEM
	<? } ?>
	E_LIST_TR
<?
}
?>dnl
	B_LIST_FOOTER
<? if ($SectOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&SectOffs=<? p ($SectOffs - $lpp); ?>*>)
<? }

    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&SectOffs=<? p ($SectOffs + $lpp); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No sections.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

