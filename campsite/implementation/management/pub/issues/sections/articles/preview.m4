<HTML>
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
        X_COOKIE(<*TOL_Access=all*>)
	X_COOKIE(<*TOL_Preview=on*>)
	X_TITLE(<*Preview article*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? } ?>dnl
E_HEAD

<? 
    if ($access) {

        todefnum('Pub');
        todefnum('Issue');
        todefnum('Section');
        todefnum('Language');
        todefnum('sLanguage');
        todefnum('Article');


    query ("SELECT * FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article AND IdLanguage=$sLanguage", 'q_art');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	    if ($NUM_ROWS) {
		fetchRow($q_art);
    		fetchRow($q_sect);
		fetchRow($q_pub);
		query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
		fetchRow($q_iss);
	        if (($NUM_ROWS !=0)&&(getVar($q_iss,'SingleArticle') != "")) {
		    ?>dnl
<FRAMESET ROWS="60%,*" BORDER="2">
<FRAME SRC="<? pgetVar($q_iss,'SingleArticle'); ?>?IdPublication=<? p($Pub); ?>&NrIssue=<? p($Issue); ?>&NrSection=<? p($Section); ?>&NrArticle=<? p($Article); ?>&IdLanguage=<? p($sLanguage); ?>" NAME="body" FRAMEBORDER="1">
<FRAME NAME="e" SRC="empty.php" FRAMEBORDER="1">
</FRAMESET>
<? } else { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Preview article*>)
X_HEADER_NO_BUTTONS
E_HEADER

<?
    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
    query ("SELECT Name FROM Languages WHERE Id=$sLanguage", 'q_slang');
    fetchRow($q_lang);
    fetchRow($q_slang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($q_iss,'Number'); ?>. <? pgetHVar($q_iss,'Name'); ?> (<? pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><? pgetHVar($q_sect,'Number'); ?>. <? pgetHVar($q_sect,'Name'); ?></B>*>)
X_CURRENT(<*Article*>, <*<B><? pgetHVar($q_art,'Name'); ?> (<? pgetHVar($q_slang,'Name'); ?>)</B>*>)
E_CURRENT

<BLOCKQUOTE>
	<LI><? putGS('This article cannot be previewed. Please make sure it has a <B><I>single article</I></B> template selected.'); ?></LI>
</BLOCKQUOTE>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
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

<? } ?>dnl

E_DATABASE
E_HTML
