B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Duplicate article*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? } ?>dnl

E_HEAD

B_STYLE
E_STYLE

<? if ($access) {
?>dnl
B_BODY
<?
	todefnum('Language');
	todefnum('sLanguage');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('Article');
?>
B_HEADER(<*Duplicate article*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Back to article details*>, <*pub/issues/sections/articles/edit.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>*>)
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
E_CURRENT

CHECK_XACCESS(<*AddArticle*>)
<?
    if ($xaccess) {
?>
<P><CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" BGCOLOR="#C0D0FF" ALIGN="CENTER">

	B_DIALOG_INPUT(<*Name*>)
		<INPUT DISABLED TYPE="TEXT" NAME="cName" SIZE="64" MAXLENGTH="64" VALUE="<? pgetHVar($q_art,'Name'); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Type*>)
		<B><? pgetHVar($q_art,'Type'); ?></B>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Uploaded*>)
		<B><? pgetHVar($q_art,'UploadDate'); ?> <? putGS('(yyyy-mm-dd)'); ?></B>
	E_DIALOG_INPUT

</TABLE></CENTER>

<P><DIV><TABLE><TR><TH WIDTH="150"></TH><TH><? putGS("Select destination"); ?></TH></TR></TABLE></DIV></P>

<? } else { ?>dnl
	X_AD(<*You do not have the right to add articles.*>)
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

<? } // access ?>dnl

E_BODY

E_DATABASE
E_HTML
