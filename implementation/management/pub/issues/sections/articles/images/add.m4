B_HTML
INCLUDE_PHP_LIB(<*../../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*AddImage*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add new image*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add images*>)
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
    todefnum('Article');
    todefnum('Language');
    todefnum('sLanguage');
?>
B_HEADER(<*Add new image*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Images*>, <*pub/issues/sections/articles/images/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&Section=<? p($Section); ?>*>)
X_HBUTTON(<*Articles*>, <*pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>*>)
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT * FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article", 'q_art');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	    if ($NUM_ROWS) {
		query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
		if ($NUM_ROWS) {
		    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');

		    fetchRow($q_art);
		    fetchRow($q_sect);
		    fetchRow($q_iss);
		    fetchRow($q_pub);
		    fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($q_iss,'Number'); ?>. <? pgetHVar($q_iss,'Name'); ?> (<? pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><? pgetHVar($q_sect,'Number'); ?>. <? pgetHVar($q_sect,'Name'); ?></B>*>)
X_CURRENT(<*Article*>, <*<B><? pgetHVar($q_art,'Name'); ?></B>*>)
E_CURRENT

<?
    query ("SELECT MAX(Number) + 1 FROM Images WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND NrArticle=$Article", 'q_nr');
    fetchRowNum($q_nr);
    if (getNumVar($q_nr,0) == "")
	$nr= 1;
    else
	$nr=getNumVar($q_nr,0);
?>dnl
<P>
B_DIALOG(<*Add new image*>, <*POST*>, <*/cgi-bin/upload_i*>, <*multipart/form-data*>)
	B_DIALOG_INPUT(<*Number*>)
		<INPUT TYPE="TEXT" NAME="cNumber" VALUE="<? p($nr); ?>" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Description*>)
		<INPUT TYPE="TEXT" NAME="cDescription" VALUE="Image <? p($nr); ?>" SIZE="32" MAXLENGTH="128">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Photographer*>)
		<INPUT TYPE="TEXT" NAME="cPhotographer" VALUE="<? pgetHVar($Usr,'Name'); ?>" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Place*>)
		<INPUT TYPE="TEXT" NAME="cPlace" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Date*>)
<? 
    query ("SELECT LEFT(NOW(), 10)", 'q_now');
    fetchRowNum($q_now);
?>dnl
		<INPUT TYPE="TEXT" NAME="cDate" VALUE="<? pgetNumVar($q_now,0); ?>" SIZE="10" MAXLENGTH="10"> <? putGS('YYYY-MM-DD'); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Image*>)
		<INPUT TYPE="FILE" NAME="cImage" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
	    <INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<? p($Pub); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<? p($Issue); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Section" VALUE="<? p($Section); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Article" VALUE="<? p($Article); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? p($Language); ?>">
	    <INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<? p($sLanguage); ?>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/pub/issues/sections/articles/images/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&Section=<? p($Section); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
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
