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
	X_TITLE(<*Move article*>)
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
	todefnum('Article');
?>dnl
B_HEADER(<*Move article*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Articles*>, <*pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php p($Section); ?>*>)
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

    $sql = "SELECT * FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND IdLanguage=$Language AND Number=$Article";
	query($sql, 'q_article');
    if ($NUM_ROWS) {
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
		fetchRow($q_article);
		fetchRow($q_lang);

?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></B>*>)
X_CURRENT(<*Article*>, <*<B><?php  pgetHVar($q_article,'Number'); ?>. <?php  pgetHVar($q_article,'Name'); ?></B>*>)
E_CURRENT

<P>
B_DIALOG(<*Move article*>, <*POST*>, <*index.php*>)
<?php
	$sql = "select count(*) from Articles where IdPublication = $Pub and NrIssue = $Issue and "
	     . "NrSection = $Section and IdLanguage = $Language and ArticleOrder < "
	     . getVar($q_article,'ArticleOrder');
	query($sql, 'q_art_pos');
	fetchRowNum($q_art_pos);
	$art_pos = getNumVar($q_art_pos, 0) + 1;
?>
	<tr><td colspan=2><?php putGS("Move article \"$1\" from position $2", getHVar($q_article,'Name'), $art_pos) ?></td></tr>
	B_DIALOG_INPUT(<*To position*>)
		<SELECT NAME="art_pos">
<?php
	$sql = "select * from Articles where IdPublication = $Pub and NrIssue = $Issue and NrSection = $Section and IdLanguage = $Language order by ArticleOrder asc";
	query($sql, 'q_art_order');
	$nr = $NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) { 
		fetchRow($q_art_order);
		if (getVar($q_art_order, 'Number') != $Article)
			pcomboVar(($loop + 1), 0, ($loop + 1) . ". " . getHVar($q_art_order,'Name'));
	}
?>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG

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

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such article.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

