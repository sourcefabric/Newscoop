B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Glossary*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? } 
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'ls');
    query ("SELECT Id, IdLanguage, Keyword FROM Dictionary WHERE 1=0", 'Dict');
?>dnl
E_HEAD

<? if ($access) { 
SET_ACCESS(<*mda*>, <*ManageDictionary*>)
SET_ACCESS(<*dda*>, <*DeleteDictionary*>)
?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Glossary*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todef('sKeyword');
    todef('sLang');
?>dnl
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<? if ($mda != 0) { ?>
	<TD>X_NEW_BUTTON(<*Add new keyword*>, <*add.php*>)</TD>
	<? } ?>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*index.php*>)
		<TD><? putGS('Keyword'); ?>:</TD>
		<TD><INPUT TYPE="TEXT" NAME="sKeyword" VALUE="<? print encHTML(decS($sKeyword)); ?>" SIZE="16" MAXLENGTH="32"></TD>
		<TD><SELECT NAME="sLang"><OPTION><?
		    query ("SELECT Id, Name FROM Languages ORDER BY Name", 'ls');

		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($ls);
			pcomboVar(getVar($ls,'Id'),$sLang,getVar($ls,'Name'));
		    } ?>
		    </SELECT></TD>
		<TD><INPUT TYPE="IMAGE" SRC="X_ROOT/img/button/search.gif" BORDER="0"></TD>
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<?
    if ($sKeyword != "")
	$kk= "Keyword LIKE '$sKeyword%'";
    else
	$kk= "";

    if ($sLang != "")
	$ll="IdLanguage = $sLang";
    else
	$ll= "";
	
    $ww= "";
    $aa='';
    
    if ($sLang != "")
	$ww= "WHERE ";

    if ($sKeyword != "") {
	if ($ww != "")
	    $aa= " AND ";
	$ww= "WHERE ";
    }

    $kwdid= "xxxxxx";
    ?>dnl

<P><?
    todefnum('DictOffs');
    if ($DictOffs < 0) $DictOffs= 0;
    $lpp = 20;

    query ("SELECT Id, IdLanguage, Keyword FROM Dictionary $ww$ll$aa$kk ORDER BY Id, IdLanguage LIMIT $DictOffs, ".($lpp+1), 'Dict');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i= $lpp;
	$color= 0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Keyword*>)
		X_LIST_TH(<*Language*>)
	<? if ($mda != 0) { ?>
		X_LIST_TH(<*Translate*>, <*1%*>)
	<? } ?>
		X_LIST_TH(<*Infotypes*>, <*1%*>)
	<? if ($dda != 0) { ?>
		X_LIST_TH(<*Delete*>, <*1%*>)
	<? } ?>
	E_LIST_HEADER
	<? 
	    for($loop=0;$loop<$nr;$loop++) {
		fetchRow($Dict);
		if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<? if (getVar($Dict,'Id') == $kwdid) { ?>&nbsp; <? } print getHVar($Dict,'Keyword'); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
<? 
    query ("SELECT Name FROM Languages WHERE Id=".getVar($Dict,'IdLanguage'), 'l');

    $nr2=$NUM_ROWS;
    for ($loop2=0;$loop2<$nr2;$loop2++) {
	fetchRow($l);
	pgetHVar($l,'Name');
	print '&nbsp;';
    }
    ?>&nbsp;
		E_LIST_ITEM
	<? if ($mda != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
<? if (getVar($Dict,'Id') != $kwdid) { ?>dnl
			<A HREF="X_ROOT/glossary/translate.php?Keyword=<? pgetUVar($Dict,'Id'); ?>">Translate</A>
<? } ?>&nbsp;
		E_LIST_ITEM
	<? } ?>
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/glossary/keyword/?Keyword=<? pgetHVar($Dict,'Id'); ?>&Language=<? pgetHVar($Dict,'IdLanguage'); ?>">Infotypes</A>
		E_LIST_ITEM

	<? if ($dda != 0) { ?> 
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*Delete keyword <? pgetHVar($Dict,'Keyword'); ?>*>, <*icon/x.gif*>, <*glossary/del.php?Keyword=<? pgetVar($Dict,'Id'); ?>&Language=<? pgetVar($Dict,'IdLanguage'); ?>*>)
		E_LIST_ITEM
	<? } ?>
<?
    if (getVar($Dict,'Id') != $kwdid)
	$kwdid= getVar($Dict,'Id');
?>dnl
	E_LIST_TR
	<?
	    $i--;
	    }
	}
	?>dnl    
	B_LIST_FOOTER
<?
    if ($DictOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?sKeyword=<? print encURL($sKeyword); ?>&sLang=<? print encURL($sLang); ?>&DictOffs=<? print ($DictOffs - $lpp); ?>*>)
<? }
    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?sKeyword=<? print encURL($sKeyword); ?>&sLang=<? print encURL($sLang); ?>&DictOffs=<? print ($DictOffs + $lpp); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No keywords.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

