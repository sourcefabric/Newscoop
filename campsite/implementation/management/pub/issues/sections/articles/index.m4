B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
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
	X_TITLE(<*Articles*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  }
    
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'ls');
    query ("SELECT * FROM Articles WHERE 1=0", 'q_art');
?>dnl
E_HEAD

<?php  if ($access) {
SET_ACCESS(<*aaa*>, <*AddArticle*>)
SET_ACCESS(<*caa*>, <*ChangeArticle*>)
SET_ACCESS(<*daa*>, <*DeleteArticle*>)

?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Section');
    todefnum('Language');
    todefnum('sLanguage');
?>dnl
B_HEADER(<*Articles*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
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
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></B>*>)
E_CURRENT

<P><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
<?php  if ($aaa != 0) { ?>
	<TD>X_NEW_BUTTON(<*Add new article*>, <*add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&Back=<?php  pencURL($REQUEST_URI); ?>*>)</TD>
<?php  } ?>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*index.php*>)
		<TD><?php  putGS('Language'); ?>:</TD>
		<TD><SELECT NAME="sLanguage"><OPTION><?php 

		    query ("SELECT Id, Name FROM Languages ORDER BY Name", 'ls');
		    $nr=$NUM_ROWS;
		for($loop=0;$loop<$nr;$loop++) {
			fetchRow($ls);
			pcomboVar(getHVar($ls,'Id'),'',getHVar($ls,'Name'));
	        }
		?>dnl
		    </SELECT></TD>
		<TD>SUBMIT(<*Search*>, <*Search*>)</TD>
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<?php 
    if ($sLanguage) {
	$ll= "AND IdLanguage=$sLanguage";
	$oo= "";
    } else {
	$ll= "";
	$oo= ", LangOrd asc, IdLanguage asc";
    }

    $kwdid= "ssssssssss";
?>dnl
<P><?php 
    todefnum('ArtOffs');
    if ($ArtOffs < 0) $ArtOffs= 0;
    todefnum('lpp', 20);

	$sql = "SELECT *, abs($Language - IdLanguage) as LangOrd FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section $ll ORDER BY Number DESC $oo LIMIT $ArtOffs, ".($lpp+1);
	query($sql, 'q_art');
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
<?php  if ($aaa != 0) { ?>dnl
		X_LIST_TH(<*Duplicate*>, <*1%*>)
<?php  } ?>dnl
<?php  if ($daa != 0) { ?>dnl
		X_LIST_TH(<*Delete*>, <*1%*>)
<?php  } ?>dnl
	E_LIST_HEADER
<?php 
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_art);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<?php  if (getVar($q_art,'Number') == $kwdid) { ?>&nbsp;<?php  } ?><A HREF="X_ROOT/pub/issues/sections/articles/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>"><?php  pgetHVar($q_art,'Name'); ?>&nbsp;</A>
		E_LIST_ITEM
		B_LIST_ITEM(<*RIGHT*>)
			<?php  pgetHVar($q_art,'Type'); ?>
		E_LIST_ITEM

		B_LIST_ITEM
<?php 
    query ("SELECT Name FROM Languages WHERE Id=".getVar($q_art,'IdLanguage'), 'q_ail');
    fetchRow($q_ail);
    pgetHVar($q_ail,'Name');
?>dnl
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
<?php  if (getVar($q_art,'Published') == "Y") { ?>dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>&Back=<?php  pencURL($REQUEST_URI); ?>"><?php  putGS("Published"); ?></A>
<?php  } elseif (getVar($q_art,'Published') == "N") { ?>dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>&Back=<?php  pencURL($REQUEST_URI); ?>"><?php  putGS("New"); ?></A>
<?php  } else { ?>dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>&Back=<?php  pencURL($REQUEST_URI); ?>"><?php  putGS("Submitted"); ?></A>
<?php  } ?>dnl
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
<?php  if (getVar($q_art,'Number') != $kwdid) { ?>dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language);?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>"><?php  putGS("Images"); ?></A>
<?php  } else { ?>dnl
		&nbsp;
<?php  } ?>dnl
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="" ONCLICK="window.open('X_ROOT/pub/issues/sections/articles/preview.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>', 'fpreview', PREVIEW_OPT); return false"><?php  putGS("Preview"); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
<?php  if (getVar($q_art,'Number') != $kwdid) { ?>dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/translate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&Back=<?php  pencURL($REQUEST_URI); ?>"><?php  putGS("Translate"); ?></A>
<?php  } else { ?>dnl
		&nbsp;
<?php  } ?>dnl
		E_LIST_ITEM
<?php  if ($aaa != 0) { ?>dnl
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/pub/issues/sections/articles/fduplicate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>"><?php  putGS("Duplicate"); ?></A>
		E_LIST_ITEM
<?php  } ?>dnl
	<?php  if ($daa != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<?php  putGS('Delete article $1',getHVar($q_art,'Name')); ?>*>, <*icon/x.gif*>, <*pub/issues/sections/articles/del.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>&Back=<?php  pencURL($REQUEST_URI); ?>*>)
		E_LIST_ITEM
	<?php  }
		if (getVar($q_art,'Number') != $kwdid)
			$kwdid=getVar($q_art,'Number');
		?>dnl
	E_LIST_TR
<?php 
    $i--;
    }
}
?>dnl
	B_LIST_FOOTER
<?php 
    if ($ArtOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*index.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&ArtOffs=<?php  p($ArtOffs - $lpp); ?>*>)
<?php  }

    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*index.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&ArtOffs=<?php  p($ArtOffs + $lpp); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No articles.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such section.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

