B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Articles*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? }
    
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'ls');
    query ("SELECT * FROM Articles WHERE 1=0", 'q_art');
?>dnl
E_HEAD

<? if ($access) {
SET_ACCESS(<*aaa*>, <*AddArticle*>)
SET_ACCESS(<*caa*>, <*ChangeArticle*>)
SET_ACCESS(<*daa*>, <*DeleteArticle*>)

?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Section');
    todefnum('Language');
    todefnum('sLanguage');
?>dnl
B_HEADER(<*Articles*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    if ($sLanguage == "")
	$sLanguage= 0;

    query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	    if ($NUM_ROWS) {

		query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
		fetchRow($q_pub);
		fetchRow($q_iss);
		fetchRow($q_sect);
		fetchRow($q_lang);

?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($q_iss,'Number'); ?>. <? pgetHVar($q_iss,'Name'); ?> (<? pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><? pgetHVar($q_sect,'Number'); ?>. <? pgetHVar($q_sect,'Name'); ?></B>*>)
E_CURRENT

<P><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
<? if ($aaa != 0) { ?>
	<TD>X_NEW_BUTTON(<*Add new article*>, <*add.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Language=<? p($Language); ?>&Back=<? pencURL($REQUEST_URI); ?>*>)</TD>
<? } ?>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*index.php*>)
		<TD><? putGS('Language'); ?>:</TD>
		<TD><SELECT NAME="sLanguage"><OPTION><?

		    query ("SELECT Id, Name FROM Languages ORDER BY Name", 'ls');
		    $nr=$NUM_ROWS;
		for($loop=0;$loop<$nr;$loop++) {
			fetchRow($ls);
			pcomboVar(getHVar($ls,'Id'),'',getHVar($ls,'Name'));
	        }
		?>dnl
		    </SELECT></TD>
		<TD>SUBMIT(<*Search*>, <*Search*>)</TD>
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<? p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<? p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<? p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? p($Language); ?>">
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<?
    if ($sLanguage) {
	$ll= "AND IdLanguage=$Language";
	$oo= "";
    } else {
	$ll= "";
	$oo= ", LangOrd asc, IdLanguage asc";
    }

    $kwdid= "ssssssssss";
?>dnl
<P><?
    todefnum('ArtOffs');
    if ($ArtOffs < 0) $ArtOffs= 0;
    todefnum('lpp', 20);

	query ("SELECT *, abs($Language - IdLanguage) as LangOrd FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section $ll ORDER BY Number DESC $oo LIMIT $ArtOffs, ".($lpp+1), 'q_art');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i= $lpp;
	$color= 0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Name<BR><SMALL>(click to edit)</SMALL>*>)
		X_LIST_TH(<*Type*>, <*1%*>)
		X_LIST_TH(<*Language*>, <*1%*>)
		X_LIST_TH(<*Status*>, <*1%*>)
		X_LIST_TH(<*Images*>, <*1%*>)
		X_LIST_TH(<*Preview*>, <*1%*>)
		X_LIST_TH(<*Translate*>, <*1%*>)
<? if ($daa != 0) { ?>dnl
		X_LIST_TH(<*Delete*>, <*1%*>)
<? } ?>dnl
	E_LIST_HEADER
<?
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_art);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<? if (getVar($q_art,'Number') == $kwdid) { ?>&nbsp;<? } ?><A HREF="X_ROOT/pub/issues/sections/articles/edit.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? pgetUVar($q_art,'Number'); ?>&Language=<? p($Language); ?>&sLanguage=<? pgetUVar($q_art,'IdLanguage'); ?>"><? pgetHVar($q_art,'Name'); ?>&nbsp;</A>
		E_LIST_ITEM
		B_LIST_ITEM(<*RIGHT*>)
			<? pgetHVar($q_art,'Type'); ?>
		E_LIST_ITEM

		B_LIST_ITEM
<?
    query ("SELECT Name FROM Languages WHERE Id=".getVar($q_art,'IdLanguage'), 'q_ail');
    fetchRow($q_ail);
    pgetHVar($q_ail,'Name');
?>dnl
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
<? if (getVar($q_art,'Published') == "Y") { ?>dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/status.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? pgetUVar($q_art,'Number'); ?>&Language=<? p($Language); ?>&sLanguage=<? pgetUVar($q_art,'IdLanguage'); ?>&Back=<? pencURL($REQUEST_URI); ?>"><? putGS("Published"); ?></A>
<? } elseif (getVar($q_art,'Published') == "N") { ?>dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/status.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? pgetUVar($q_art,'Number'); ?>&Language=<? p($Language); ?>&sLanguage=<? pgetUVar($q_art,'IdLanguage'); ?>&Back=<? pencURL($REQUEST_URI); ?>"><? putGS("New"); ?></A>
<? } else { ?>dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/status.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? pgetUVar($q_art,'Number'); ?>&Language=<? p($Language); ?>&sLanguage=<? pgetUVar($q_art,'IdLanguage'); ?>&Back=<? pencURL($REQUEST_URI); ?>"><? putGS("Submitted"); ?></A>
<? } ?>dnl
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
<? if (getVar($q_art,'Number') != $kwdid) { ?>dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/images/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? pgetUVar($q_art,'Number'); ?>&Language=<? p($Language);?>&sLanguage=<? pgetUVar($q_art,'IdLanguage'); ?>"><? putGS("Images"); ?></A>
<? } else { ?>dnl
		&nbsp;
<? } ?>dnl
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="" ONCLICK="window.open('X_ROOT/pub/issues/sections/articles/preview.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? pgetUVar($q_art,'Number'); ?>&Language=<? p($Language); ?>&sLanguage=<? pgetUVar($q_art,'IdLanguage'); ?>', 'fpreview', PREVIEW_OPT); return false"><? putGS("Preview"); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
<? if (getVar($q_art,'Number') != $kwdid) { ?>dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/translate.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? pgetUVar($q_art,'Number'); ?>&Language=<? p($Language); ?>&Back=<? pencURL($REQUEST_URI); ?>"><? putGS("Translate"); ?></A>
<? } else { ?>dnl
		&nbsp;
<? } ?>dnl
		E_LIST_ITEM
	<? if ($daa != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<? putGS('Delete article $1',getHVar($q_art,'Name')); ?>*>, <*icon/x.gif*>, <*pub/issues/sections/articles/del.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? pgetUVar($q_art,'Number'); ?>&Language=<? p($Language); ?>&sLanguage=<? pgetUVar($q_art,'IdLanguage'); ?>&Back=<? pencURL($REQUEST_URI); ?>*>)
		E_LIST_ITEM
	<? }
		if (getVar($q_art,'Number') != $kwdid)
			$kwdid=getVar($q_art,'Number');
		?>dnl
	E_LIST_TR
<? 
    $i--;
    }
}
?>dnl
	B_LIST_FOOTER
<?
    if ($ArtOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&ArtOffs=<? p($ArtOffs - $lpp); ?>*>)
<? }

    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&ArtOffs=<? p($ArtOffs + $lpp); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No articles.'); ?></LI>
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

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such section.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

