B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Edit article details*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? }
    query ("SELECT IdLanguage FROM Articles WHERE 1=0", 'q_al');
    query ("SELECT Name FROM Languages WHERE 1=0", 'q_ls');
    query ("SHOW COLUMNS FROM Articles LIKE 'XXYYZZ'", 'q_fld');
?>dnl

E_HEAD

B_STYLE
E_STYLE

<? if ($access) {
SET_ACCESS(<*dla*>, <*DeleteArticle*>)
?>dnl
B_BODY


<?
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Section');
    todefnum('Language');
    todefnum('sLanguage');
    todefnum('Article');
    todefnum('LockOk');
    todef('eField');

    	$fldname=substr ( $eField, 1);

?>
	
B_HEADER(<*Edit article details*>)
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
    if (getNumVar($q_xperm,0)) {
	$edit_ok= 0;
	if (getVar($q_art,'LockUser') == 0)
	    $LockOk= 1;

	if ($LockOk) {
	    query ("UPDATE Articles SET LockUser=".getSVar($Usr,'Id').", LockTime=NOW() WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article AND IdLanguage=$sLanguage");
	    $edit_ok= 1;
	} else {
	    if (getVar($q_art,'LockUser') == getVar($Usr,'Id')) {
		query ("UPDATE Articles SET LockTime=NOW() WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article AND IdLanguage=$sLanguage");
		$edit_ok= 1;
	    } else {
	    ?>dnl
<P>
B_MSGBOX(<*Article is locked*>)
<?
    query ("SELECT *, NOW() AS Now FROM Users WHERE Id=".getSVar($q_art,'LockUser'), 'q_luser');
    fetchRow($q_luser);
    ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('This article has been locked by $1 ($2) at','<B>'.getHVar($q_luser,'Name'),getHVar($q_luser,'UName').'</B>' ); ?>
		<B><? pgetHVar($q_art,'LockTime'); ?></B></LI>
		<LI><? putGS('Now is $1','<B>'.getHVar($q_luser,'Now').'</B>'); ?></LI>
		<LI><? putGS('Are you sure you want to unlock it?'); ?></LI>
	*>)
	B_MSGBOX_BUTTONS
		<A HREF="<? p($REQUEST_URI); ?>&LockOk=1"><IMG SRC="X_ROOT/img/button/yes.gif" BORDER="0" ALT="Yes"></A>
		<A HREF="X_ROOT/pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
	<? } ?>dnl
<? } ?>dnl

<? if ($edit_ok) { ?>dnl
<P>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
<TR><TD>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
<TR><TD>
<? if (getVar($q_art,'Published') == "Y") { ?>dnl
X_NEW_BUTTON(<*Unpublish*>, <*X_ROOT/pub/issues/sections/articles/status.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&Back=<? pencURL($REQUEST_URI); ?>*>)
<? } elseif (getVar($q_art,'Published') == "S") { ?>dnl
X_NEW_BUTTON(<*Publish*>, <*X_ROOT/pub/issues/sections/articles/status.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&Back=<? pencURL($REQUEST_URI); ?>*>)
<? } else { ?>dnl
X_NEW_BUTTON(<*Submit*>, <*X_ROOT/pub/issues/sections/articles/status.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&Back=<? pencURL($REQUEST_URI); ?>*>)
<? } ?>dnl
</TD>
<TD>
X_NEW_BUTTON(<*Images*>, <*X_ROOT/pub/issues/sections/articles/images/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>*>)
</TD><TD>
X_NEW_BUTTON(<*Unlock*>, <*X_ROOT/pub/issues/sections/articles/do_unlock.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>*>)
</TD></TR>
<TR><TD>
X_NEW_BUTTON(<*Preview*>, <**>, <*window.open('X_ROOT/pub/issues/sections/articles/preview.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>', 'fpreview', PREVIEW_OPT); return false*>)
</TD><TD>
X_NEW_BUTTON(<*Translate*>, <*X_ROOT/pub/issues/sections/articles/translate.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&Back=<? pencURL($REQUEST_URI); ?>*>)
<? if ($dla) { ?>dnl
</TD><TD>
X_NEW_BUTTON(<*Delete*>, <*X_ROOT/pub/issues/sections/articles/del.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&Back=<? pencURL($REQUEST_URI); ?>*>)
</TD><TD>
X_NEW_BUTTON(<*Edit details*>, <*X_ROOT/pub/issues/sections/articles/edit.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>*>)
</TD><TD>
<? } ?>dnl
</TD></TR>
</TABLE>
</TD><TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*edit.php*>)
		<TD><? putGS('Language'); ?>:</TD>
		<TD><SELECT NAME="sLanguage">
<?
    query ("SELECT IdLanguage FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article", 'q_al');
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_al);
	query ("SELECT Name FROM Languages WHERE Id=".getVar($q_al,'IdLanguage'), 'q_ls');
	$nr2=$NUM_ROWS;
	for($loop2=0;$loop2<$nr2;$loop2++) {
	    fetchRow($q_ls);
	    pcomboVar(getHVar($q_al,'IdLanguage'),$sLanguage,getHVar($q_ls,'Name'));
	}
    }
?>dnl
</SELECT></TD>
		<TD><INPUT TYPE="IMAGE" SRC="X_ROOT/img/button/search.gif" BORDER="0"></TD>
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<? p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<? p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<? p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<? p($Article); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? p($Language); ?>">
	E_SEARCH_DIALOG
</TD></TR>
</TABLE>


B_DIALOG(<*Edit field: $1","$fldname*>, <*POST*>, <*do_edit_t.php*>)
	<tr><td><TEXTAREA rows=15 cols=50 NAME="cField"></TEXTAREA></td></tr>

<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<? p($Pub); ?>">
<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<? p($Issue); ?>">
<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<? p($Section); ?>">
<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<? p($Article); ?>">
<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? p($Language); ?>">
<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<? p($sLanguage); ?>">
<INPUT TYPE="HIDDEN" NAME="query" VALUE="">
<INPUT TYPE="HIDDEN" NAME="eField" VALUE="<? pencHTML($eField); ?>">

<?
    $fld= "";
?>

</FORM>

<FORM NAME="fields">


	B_DIALOG_BUTTONS
<SCRIPT>

	function do_submit()
	{
		document.dialog.submit();
	}
</SCRIPT>
		X_HR
		<A HREF="javascript:void(do_submit())"><IMG SRC="X_ROOT/img/button/save.gif" BORDER="0" ALT="OK"></A>
		<A HREF="X_ROOT/pub/issues/sections/articles/edit.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage);?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG


<? } ?>dnl

<? } else { ?>dnl
    X_XAD(<*You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users.*>, <*pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>*>)
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

