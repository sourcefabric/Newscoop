B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*AddArticle*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add new article*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add articles.*>)
<? }
    query ("SHOW TABLES LIKE 'Z'", 'q_tbl');
?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<? 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Section');
    todefnum('Language');
    todefnum('Wiz');
?>dnl
B_HEADER(<*Add new article*>)
B_HEADER_BUTTONS
<?
    if ($Wiz == 0) { ?>X_HBUTTON(<*Articles*>, <*pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>*>)<? } ?>
X_HBUTTON(<*Sections*>, <*pub/issues/sections/<? if ($Wiz) { ?>add_article.php<? } ?>?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/<? if ($Wiz) { ?>add_article.php<? } ?>?Pub=<? p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/<? if ($Wiz) { ?>add_article.php<? } ?>*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	    if ($NUM_ROWS) {
		query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
		fetchRow($q_sect);
		fetchRow($q_iss);
		fetchRow($q_pub);
		fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($q_iss,'Number'); ?>. <? pgetHVar($q_iss,'Name'); ?> (<? pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><? pgetHVar($q_sect,'Number'); ?>. <? pgetHVar($q_sect,'Name'); ?></B>*>)
E_CURRENT

<P>
B_DIALOG(<*Add new article*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Type*>)
			<SELECT NAME="cType">
<?
    query ("SHOW TABLES LIKE 'X%'", 'q_tbl');
    $nr=$NUM_ROWS;

		for($loop=0;$loop<$nr;$loop++) { 
			fetchRowNum($q_tbl);
			print '<OPTION>'.encHTML ( substr ( getNumVar ($q_tbl,0) ,1 ) );
	        }
		?>dnl
			</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
			<SELECT NAME="cLanguage">
<?
    query ("SELECT Id, Name FROM Languages", 'q_lng');
	$nr=$NUM_ROWS;
		for($loop=0;$loop<$nr;$loop++) { 
			fetchRow($q_lng);
			pcomboVar(getHVar($q_lng,'Id'), $Language,getHVar($q_lng,'Name'));
	        }
?>dnl
			</SELECT>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cFrontPage"*>)
		<? putGS('Show article on front page'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cSectionPage"*>)
		<? putGS('Show article on section page'); ?>
	E_DIALOG_INPUT
	X_DIALOG_TEXT(<*Enter keywords, comma separated*>)
	B_DIALOG_INPUT(<*Keywords*>)
		<INPUT TYPE="TEXT" NAME="cKeywords" SIZE="32" MAXLENGTH="255">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<? p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<? p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<? p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? p($Language); ?>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
<? todef('Back');

    if ($Back != "") { ?>dnl
		<A HREF="<? p($Back); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<? } ?>dnl
	E_DIALOG_BUTTONS
E_DIALOG
<P>

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
