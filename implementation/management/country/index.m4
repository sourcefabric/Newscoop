B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Countries*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? }
    query ("SELECT * FROM Countries WHERE 1=0", 'q_countries');
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'ls');
?>dnl
E_HEAD

<? if ($access) { 
SET_ACCESS(<*mca*>, <*ManageCountries*>)
SET_ACCESS(<*dca*>, <*DeleteCountries*>)
}
?>dnl

B_STYLE
E_STYLE

B_BODY

<? todefnum('sLanguage'); ?>dnl
B_HEADER(<*Countries*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<P><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<? if ($mca != 0) { ?>
	<TD>X_NEW_BUTTON(<*Add new country*>, <*add.php?Back=<? print ($REQUEST_URI); ?>*>)</TD>
	<? } ?>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*index.php*>)
		<TD><? putGS('Language') ?>:</TD>
		<TD><SELECT NAME="sLanguage"><OPTION><?
		    query ("SELECT Id, Name FROM Languages ORDER BY Name", 'ls');
		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($ls);
			pcomboVar(getVar($ls,'Id'),$sLanguage,getVar($ls,'Name'));
		    }
		?></SELECT></TD>
		<TD><INPUT TYPE="IMAGE" SRC="X_ROOT/img/button/search.gif" BORDER="0"></TD>
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<?
    if ($sLanguage) {
	$ll= " AND IdLanguage=$sLanguage";
        $oo= ", IdLanguage";
    }
    else {
	$ll= "";
	$oo= "";
    }

    $kwdid= "ssssssssss";
    todefnum('CtrOffs');
    if ($CtrOffs < 0) $CtrOffs= 0;
    $lpp=20;
    query ("SELECT * FROM Countries WHERE Code != \"\"$ll ORDER BY Code$oo LIMIT $CtrOffs, ".($lpp+1), 'q_countries');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i= $lpp;
	if($nr < $lpp) $i = $nr;
	$color= 0; ?>dnl
B_LIST
	B_LIST_HEADER
<? if ($mca != 0) { ?>dnl
		X_LIST_TH(<*Name<BR><SMALL>(click to edit)</SMALL>*>)
<? } else { ?>dnl
		X_LIST_TH(<*Name*>)
<? } ?>dnl
		X_LIST_TH(<*Language*>, <*1%*>)
		X_LIST_TH(<*Code*>, <*1%*>)
<? if ($mca != 0) { ?>dnl
		X_LIST_TH(<*Translate*>, <*1%*>)
<? }
if ($dca != 0) { ?>
		X_LIST_TH(<*Delete*>, <*1%*>)
<? } ?>dnl
	E_LIST_HEADER
<? for ($loop=0;$loop<$i;$loop++) {
    fetchRow($q_countries); ?>dnl
	B_LIST_TR
<? if ($mca != 0) { ?>dnl
		B_LIST_ITEM
			<? if (getVar($q_countries,'Code') == $kwdid) print '&nbsp;'; ?><A HREF="X_ROOT/country/edit.php?Code=<? encURL(pgetUVar($q_countries,'Code')); ?>&Language=<? pgetUVar($q_countries,'IdLanguage'); ?>"><? pgetHVar($q_countries,'Name'); ?>&nbsp;</A>
		E_LIST_ITEM
<? } else { ?>dnl
		B_LIST_ITEM
			<? if (getVar($q_countries,'Code') == $kwdid) print '&nbsp;'; ?><? pgetHVar($q_countries,'Name'); ?>&nbsp;
		E_LIST_ITEM
<? } ?>dnl
		B_LIST_ITEM
<? query ("SELECT Name FROM Languages WHERE Id=".getVar($q_countries,'IdLanguage'), 'q_ail');
    fetchRow($q_ail);
    pgetHVar($q_ail,'Name'); ?>dnl
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
<? if (getVar($q_countries,'Code') != $kwdid)
	pgetHVar($q_countries,'Code');
    else
	print '&nbsp;'; ?>dnl
		E_LIST_ITEM
	<? if ($mca != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
<? if (getVar($q_countries,'Code') != $kwdid) { ?>dnl
			<A HREF="X_ROOT/country/translate.php?Code=<? encURL(pgetUVar($q_countries,'Code')); ?>&Language=<? pgetUVar($q_countries,'IdLanguage'); ?>">Translate</A>
<? } else { ?>dnl
&nbsp;
<? } ?>dnl
		E_LIST_ITEM
	<? }
	if ($dca != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*Delete country <? pgetHVar($q_countries,'Name'); ?>*>, <*icon/x.gif*>, <*country/del.php?Code=<? encURL(pgetUVar($q_countries,'Code')); ?>&Language=<? pgetUVar($q_countries,'IdLanguage'); ?>*>)
		E_LIST_ITEM
	<? } ?>
	E_LIST_TR
<? $kwdid=getVar($q_countries,'Code');
} ?>dnl
	B_LIST_FOOTER
<? if ($CtrOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?sLanguage=<? print encURL($sLanguage); ?>&CtrOffs=<? print ($CtrOffs - $lpp); ?>*>)
<? } ?>dnl
<? if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?sLanguage=<? print encURL($sLanguage); ?>&CtrOffs=<? print ($CtrOffs + $lpp); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No countries.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY

E_DATABASE
E_HTML

