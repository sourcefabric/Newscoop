B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Issues*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? }
    query ("SELECT * FROM Issues WHERE 1=0", 'q_iss');
?>dnl
E_HEAD

<? if ($access) { 
SET_ACCESS(<*mia*>, <*ManageIssue*>)
SET_ACCESS(<*dia*>, <*DeleteIssue*>)

?>dnl
B_STYLE
E_STYLE

B_BODY

<? todefnum('Pub'); ?>dnl

<? function tplRedirect($s){
	if (file_exists(getenv("DOCUMENT_ROOT")."/".decURL($s))){
		$dotpos=strrpos($s,"/");
		if($dotpos){
			$tplpath=substr ($s,0,$dotpos);
		} else $tplpath="LOOK_PATH";
	}	
	else $tplpath="LOOK_PATH";
	return $tplpath;
}?>dnl

B_HEADER(<*Issues*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?  query ("SELECT Name, IdDefaultLanguage FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) { 
	fetchRow($q_pub);
	$IdLang = getVar($q_pub,'IdDefaultLanguage');
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
E_CURRENT

<? if ($mia != 0) {
	query ("SELECT MAX(Number) FROM Issues WHERE IdPublication=$Pub", 'q_nr');
	fetchRowNum($q_nr);
	if (getNumVar($q_nr,0) == "") { ?>
	<P>X_NEW_BUTTON(<*Add new issue*>, <*add_new.php?Pub=<? pencURL($Pub); ?>*>)
	<? } else { ?>dnl
	<P>X_NEW_BUTTON(<*Add new issue*>, <*qadd.php?Pub=<? pencURL($Pub); ?>*>)
	<? }
    }
    $IssNr= "xxxxxxxxx";
?>
<P><?
    todefnum('IssOffs');
    if ($IssOffs < 0) $IssOffs= 0;
    $lpp=20;

    query ("SELECT Name, IdLanguage, abs(IdLanguage-$IdLang) as IdLang, Number, Name, if(Published='Y', PublicationDate, if($mia, 'Publish', 'No')) as Pub, FrontPage, SingleArticle  FROM Issues WHERE IdPublication=$Pub ORDER BY Number DESC, IdLang ASC LIMIT $IssOffs, ".($lpp+1), 'q_iss');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i=$lpp;
	$color=0;
?>dnl
B_LIST
	B_LIST_HEADER
	<? if ($mia != 0) { ?>
		X_LIST_TH(<*Nr*>, <*1%*>)
		X_LIST_TH(<*Name<BR><SMALL>(click to see sections)</SMALL>*>)
		X_LIST_TH(<*Language*>)
		X_LIST_TH(<*Front Page Template<BR><SMALL>(click to change)</SMALL>*>)
		X_LIST_TH(<*Single Article Template<BR><SMALL>(click to change)</SMALL>*>)
		X_LIST_TH(<*Published<BR><SMALL>(yyyy-mm-dd)</SMALL>*>, <*1%*>)
		X_LIST_TH(<*Translate*>, <*1%*>)
		X_LIST_TH(<*Change*>, <*1%*>) 
		X_LIST_TH(<*Preview*>, <*1%*>)
	<? } else { ?>
		X_LIST_TH(<*Nr*>, <*1%*>)
		X_LIST_TH(<*Name<BR><SMALL>(click to see sections)</SMALL>*>)
		X_LIST_TH(<*Language*>)
		X_LIST_TH(<*Published<BR><SMALL>(yyyy-mm-dd)</SMALL>*>, <*1%*>)
		X_LIST_TH(<*Preview*>, <*1%*>)
	<? }
	
	if ($dia != 0) { ?>
		X_LIST_TH(<*Delete*>, <*1%*>)
	<? } ?>
	E_LIST_HEADER

<?
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_iss);
	if ($i) { ?>dnl
	B_LIST_TR
<? if ($mia != 0) { ?>
		B_LIST_ITEM(<*RIGHT*>)
	<? if ($IssNr != getVar($q_iss,'Number'))
		pgetHVar($q_iss,'Number');
	    else
		print '&nbsp;';
	 ?>dnl
		E_LIST_ITEM
		B_LIST_ITEM
		<? if ($IssNr == getVar($q_iss,'Number')) print "&nbsp;";?>
			<A HREF="X_ROOT/pub/issues/sections/?Pub=<? pencURL($Pub); ?>&Issue=<? pgetUVar($q_iss,'Number'); ?>&Language=<? pgetUVar($q_iss,'IdLanguage'); ?>"><? pgetHVar($q_iss,'Name'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM
	<? query ("SELECT Name FROM Languages WHERE Id=".getVar($q_iss,'IdLanguage'), 'language');
	    for($loop2=0;$loop2<$NUM_ROWS;$loop2++) {
		fetchRow($language);
		print getHVar($language,'Name');
	    } 
	 ?>dnl
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="<? p(tplRedirect(getHVar($q_iss,'FrontPage')));?>/?What=1&Pub=<? pencURL($Pub); ?>&Issue=<? pgetUVar($q_iss,'Number'); ?>&Language=<? pgetUVar($q_iss,'IdLanguage'); ?>"><? if (getVar($q_iss,'FrontPage') != "") { pdecURL(getHVar($q_iss,'FrontPage')); } else { ?>Click here to set...<? } ?></A>
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="<? p(tplRedirect(getHVar($q_iss,'SingleArticle')));?>/?What=2&Pub=<? pencURL($Pub); ?>&Issue=<? pgetUVar($q_iss,'Number'); ?>&Language=<? pgetUVar($q_iss,'IdLanguage'); ?>"><? if (getVar($q_iss,'SingleArticle') != "") { pdecURL(getHVar($q_iss,'SingleArticle')); } else { ?>Click here to set...<? } ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/pub/issues/status.php?Pub=<? pencURL($Pub); ?>&Issue=<? pgetUVar($q_iss,'Number'); ?>&Language=<? pgetUVar($q_iss,'IdLanguage'); ?>"><? pgetHVar($q_iss,'Pub'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
	<? if ($IssNr == getVar($q_iss,'Number')) { ?>dnl
			&nbsp;
	<? } else { ?>dnl
			<A HREF="X_ROOT/pub/issues/translate.php?Pub=<? pencURL($Pub); ?>&Issue=<? pgetUVar($q_iss,'Number'); ?>&Language=<? pgetUVar($q_iss,'IdLanguage'); ?>">Translate</A>
	<? } ?>dnl
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/pub/issues/edit.php?Pub=<? pencURL($Pub); ?>&Issue=<? pgetUVar($q_iss,'Number'); ?>&Language=<? pgetUVar($q_iss,'IdLanguage'); ?>">Change</A>
                E_LIST_ITEM 		B_LIST_ITEM(<*CENTER*>)
			<A HREF="" ONCLICK="window.open('X_ROOT/pub/issues/preview.php?Pub=<? pencURL($Pub); ?>&Issue=<? pgetUVar($q_iss,'Number'); ?>&Language=<? pgetUVar($q_iss,'IdLanguage'); ?>', 'fpreview', PREVIEW_OPT); return false">Preview</A>
		E_LIST_ITEM
<? } else { ?>
		B_LIST_ITEM(<*RIGHT*>)
	<? if ($IssNr != getVar($q_iss,'Number')) {
		pgetHVar($q_iss,'Number');
	    } else { ?>dnl
		&nbsp;
	<? } ?>dnl
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/?Pub=<? pencURL($Pub); ?>&Issue=<? pgetUVar($q_iss,'Number'); ?>&Language=<? pgetUVar($q_iss,'IdLanguage'); ?>"><? pgetHVar($q_iss,'Name'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM
	<? query ("SELECT Name FROM Languages WHERE Id=".getVar($q_iss,'IdLanguage'), 'language');
	    for($loop2=0;$loop2<$NUM_ROWS;$loop2++) {
		fetchRow($language);
		print getHVar($language,'Name');
	    } 
	    
	?>dnl
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<? pgetHVar($q_iss,'Pub'); ?>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="" ONCLICK="window.open('X_ROOT/pub/issues/preview.php?Pub=<? pencURL($Pub); ?>&Issue=<? pgetUVar($q_iss,'Number'); ?>&Language=<? pgetUVar($q_iss,'IdLanguage'); ?>', 'fpreview', PREVIEW_OPT); return false">Preview</A>
		E_LIST_ITEM
<? }
    
    if ($dia != 0) { ?> 
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<? putGS('Delete issue $1',getHVar($q_iss,'Name')); ?>*>, <*icon/x.gif*>, <*pub/issues/del.php?Pub=<? pencURL($Pub); ?>&Issue=<? pgetUVar($q_iss,'Number'); ?>&Language=<? pgetUVar($q_iss,'IdLanguage'); ?>*>)
		E_LIST_ITEM
<? } ?>
	E_LIST_TR
<?
    $IssNr=getVar($q_iss,'Number');
    $i--;
    }
}

?>dnl
	B_LIST_FOOTER
<? if ($IssOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?Pub=<? pencURL($Pub); ?>&IssOffs=<? print ($IssOffs - $lpp); ?>*>)
<? }
    
    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?Pub=<? pencURL($Pub); ?>&IssOffs=<? print ($IssOffs + $lpp); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No issues.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
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
