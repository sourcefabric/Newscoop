<HTML>
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_COOKIE(<*TOL_Access=all*>)
	X_COOKIE(<*TOL_Preview=on*>)
	X_TITLE(<*Preview issue*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? } ?>dnl
E_HEAD

<? if ($access) {

    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
    
    query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	fetchRow($q_iss);
	fetchRow($q_pub);
	if ($NUM_ROWS != 0 && getVar($q_iss,'FrontPage') != "") { ?>dnl
<FRAMESET ROWS="60%,*" BORDER="1">
<FRAME SRC="<? pgetVar($q_iss,'FrontPage'); ?>?IdPublication=<? pencURL($Pub); ?>&NrIssue=<? pencURL($Issue); ?>&IdLanguage=<? pencURL($Language); ?>" NAME="body" FRAMEBORDER="1" MARGINWIDTH="0" MARGINHEIGHT="0">
<FRAME NAME="e" SRC="empty.php" FRAMEBORDER="1" MARGINWIDTH="0" MARGINHEIGHT="0">
</FRAMESET>
<? } else { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Preview issue*>)
X_HEADER_NO_BUTTONS
E_HEADER

<?
    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
    fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($q_iss,'Number'); ?>. <? pgetHVar($q_iss,'Name'); ?> (<? pgetHVar($q_lang,'Name'); ?>)</B>*>)
E_CURRENT

<BLOCKQUOTE>
	<LI><? putGS('This issue cannot be previewed. Please make sure it has a $1 template selected.','<B><I>'.getGS('front page').'</I></B>'); ?></LI>
</BLOCKQUOTE>

X_HR
X_COPYRIGHT
E_BODY
<? }
} else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } ?>dnl

E_DATABASE
E_HTML

