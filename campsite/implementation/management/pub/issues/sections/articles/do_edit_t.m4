B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Changing article details*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? } ?>dnl
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
    todefnum('sLanguage');
    todefnum('Article');
    todefnum('eField');
    todefnum('cField');
    todefnum('query');
    todef('cName');
    $fldname=substr ( $eField, 1);
?>
define(<*x_init*>, <*<?
    todef('$1');
    if ($$1 == "on")
	$$1= "Y";
    else
	$$1= "N";
?>*>)dnl
x_init(<*cOnFrontPage*>)
x_init(<*cOnSection*>)
x_init(<*cPublic*>)
<? todef('cKeywords'); ?>dnl
B_HEADER(<*Changing article details*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Articles*>, <*pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>*>)
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT * FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article AND IdLanguage=$sLanguage", 'q_art');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	    if ($NUM_ROWS) {
		query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
		if ($NUM_ROWS) {
		    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
		    query ("SELECT Name FROM Languages WHERE Id=$sLanguage", 'q_slang');

		    fetchRow($q_art);
		    fetchRow($q_sect);
		    fetchRow($q_iss);
		    fetchRow($q_pub);
		    fetchRow($q_lang);
		    fetchRow($q_slang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($q_iss,'Number'); ?>. <? pgetHVar($q_iss,'Name'); ?> (<? pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><? pgetHVar($q_sect,'Number'); ?>. <? pgetHVar($q_sect,'Name'); ?></B>*>)
X_CURRENT(<*Article*>, <*<B><? pgetHVar($q_art,'Name'); ?> (<? pgetHVar($q_slang,'Name'); ?>)</B>*>)
X_CURRENT(<*Field*>, <*<B><? p($fldname); ?></B>*>)
E_CURRENT

CHECK_XACCESS(<*ChangeArticle*>)

<?
    query ("SELECT ($xaccess != 0) or ((".getVar($q_art,'IdUser')." = ".getVar($Usr,'Id').") and ('".getVar($q_art,'Published')."' = 'N'))", 'q_xperm');
    fetchRowNum($q_xperm);
    if (getNumVar($q_xperm,0)) { ?>
<P>

<? $chngd= 0; ?>dnl

B_MSGBOX(<*Changing article details*>)
	X_MSGBOX_TEXT(<*
<?
    query ("UPDATE X".getSvar($q_art,'Type')." SET $eField='".encHTML($cField)."' WHERE NrArticle=$Article AND IdLanguage=$sLanguage");
    if ($AFFECTED_ROWS > 0)
	$chngd= 1;

    if ($chngd) { ?>dnl
	<LI><? putGS('The article has been updated.'); ?></LI>
<? } else { ?>dnl
	<LI><? putGS('The article cannot be updated or no changes have been made.'); ?></LI>
<? } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/issues/sections/articles/edit.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&sLanguage=<? p($sLanguage); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
E_MSGBOX

<P>
<? } else { ?>dnl
    X_XAD(<*You do not have the right to change this article status. Once submitted an article can only changed by authorized users.*>, <*pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>*>)
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

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such article.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
