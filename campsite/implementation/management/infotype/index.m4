B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
B_HEAD
	X_EXPIRES
	X_TITLE(<*Glossary infotypes*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? }
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'ls');
    query ("SELECT Id, IdLanguage, Name FROM Classes WHERE 1=0", 'q_cls');
?>dnl
E_HEAD

<? if ($access) {
SET_ACCESS(<*mca*>, <*ManageClasses*>)
?>dnl

B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Glossary infotypes*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<? todef('sLang');
    todef('sName');
?>dnl
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<? if ($mca != 0) { ?>
	<TD>X_NEW_BUTTON(<*Add new glossary infotype*>, <*add.php*>)</TD>
	<? } ?>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*index.php*>)
		<TD><? putGS('Infotype'); ?>:</TD>
		<TD><INPUT TYPE="TEXT" NAME="sName" VALUE="<? print encHTML(decS($sName)); ?>"></TD>
		<TD><SELECT NAME="sLang"><? query ("SELECT Id, Name FROM Languages ORDER BY Name", 'ls');
		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($ls);
			pcomboVar(getVar($ls,'Id'),$sLang,getVar($ls,'Name'));
		    } ?>
			</SELECT></TD>
		<TD>SUBMIT(<*Search*>, <*Search*>)</TD>
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<?
    if ($sName != "")
	$kk= "Name LIKE '$sName%'";
    else
	$kk= "";

    if ($sLang != "")
	$ll= "IdLanguage = $sLang";
    else
	$ll= "";

    $ww= '';
    $aa= '';

    if ($sLang != "")
	$ww= "WHERE ";

    if ($sName != "") {
	if ($ww != "") {
	    $aa= " AND ";
	}
	$ww= "WHERE ";
    }

    $kwdid= "xxxxxx";

?>dnl

<P><?
    todefnum('ClsOffs');
    if ($ClsOffs < 0) $ClsOffs= 0;
    $lpp=20;

    query ("SELECT Id, IdLanguage, Name FROM Classes $ww$ll$aa$kk ORDER BY Id, IdLanguage LIMIT $ClsOffs, ".($lpp+1), 'q_cls');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i= $lpp;
	$color= 0;
    ?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Infotype*>)
		X_LIST_TH(<*Language*>)
	<? if ($mca != 0) { ?> 
		X_LIST_TH(<*Translate*>, <*1%*>)
		X_LIST_TH(<*Delete*>, <*1%*>)
	<? } ?>
	E_LIST_HEADER
<?
    for ($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_cls);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<? if (getVar($q_cls,'Id') == $kwdid) { ?>&nbsp; <? } pgetHVar($q_cls,'Name'); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
<? 
    query ("SELECT Name FROM Languages WHERE Id=".getVar($q_cls,'IdLanguage'), 'l');
    $nr2=$NUM_ROWS;
    for ($loop2=0;$loop2<$nr2;$loop2++) {
	fetchRow($l);
	pgetHVar($l,'Name');
	print '&nbsp;';
    }
    ?>&nbsp;
		E_LIST_ITEM
	<? if ($mca != 0) { ?> 
		B_LIST_ITEM(<*CENTER*>)
<? if (getVar($q_cls,'Id') != $kwdid) { ?>dnl
			<A HREF="X_ROOT/infotype/translate.php?Class=<? pgetUVar($q_cls,'Id'); ?>"><? putGS("Translate"); ?></A>
<? } ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<? putGS('Delete glossary infotype $1',getHVar($q_cls,'Name')); ?>*>, <*icon/x.gif*>, <*infotype/del.php?Class=<? pgetUVar($q_cls,'Id'); ?>&Lang=<? pgetUVar($q_cls,'IdLanguage'); ?>*>)
		E_LIST_ITEM
	<? }
if (getVar($q_cls,'Id') != $kwdid)
    $kwdid=getVar($q_cls,'Id');
?>dnl
	E_LIST_TR
<?
    $i--;
    }
}
?>dnl
	B_LIST_FOOTER
<? if ($ClsOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?sName=<? print encURL($sName); ?>&sLang=<?  print encURL($sLang); ?>&ClsOffs=<? print ($ClsOffs - $lpp);?>*>)
<? }
    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?sName=<? print encURL($sName); ?>&sLang=<?  print encURL($sLang); ?>&ClsOffs=<? print ($ClsOffs + $lpp);?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No glossary infotypes.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
