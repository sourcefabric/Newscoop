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
<SCRIPT language="JavaScript">

function campfire(atr){
	if(navigator.appName.indexOf("Netscape") != -1) {
		location.href="X_ROOT/pub/issues/sections/articles/edit_b_ns.php?"+atr,null,"location=no,toolbar=no,menubar=no,scrollbars=no,resizable=yes";
	}else if(navigator.userAgent.indexOf("Mac") != -1) {
		location.href="X_ROOT/pub/issues/sections/articles/edit_b_apple.php?"+atr,null,"location=no,toolbar=no,menubar=no,scrollbars=no,resizable=yes";
	}else {
	    location.href="X_ROOT/pub/issues/sections/articles/edit_b.php?"+atr,null,"location=no,toolbar=no,menubar=no,scrollbars=no,resizable=yes";
	}
}

</SCRIPT>
<?
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Section');
    todefnum('Language');
    todefnum('sLanguage');
    todefnum('Article');
    todefnum('LockOk');
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
    query ("SELECT *, NOW() AS Now FROM Users WHERE Id=".getVar($q_art,'LockUser'), 'q_luser');
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
	<? }
    }

    if ($edit_ok) { ?>dnl
<P>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
<TR><TD>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
<TR><TD>
<?
    if (getVar($q_art,'Published') == "Y") { ?>dnl
X_NEW_BUTTON(<*Unpublish*>, <*X_ROOT/pub/issues/sections/articles/status.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&Back=<? pencURL($REQUEST_URI); ?>*>)
<? } elseif (getVar($q_art,'Published') == "S") { ?>dnl
X_NEW_BUTTON(<*Publish*>, <*X_ROOT/pub/issues/sections/articles/status.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&Back=<? pencURL($REQUEST_URI); ?>*>)
<? } else { ?>dnl
X_NEW_BUTTON(<*Submit*>, <*X_ROOT/pub/issues/sections/articles/status.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&Back=<? pencURL($REQUEST_URI); ?>*>)
<? } ?>dnl
</TD>
<TD>
X_NEW_BUTTON(<*Images*>, <*X_ROOT/pub/issues/sections/articles/images/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>*>)
</TD>
<TD>
X_NEW_BUTTON(<*Topics*>, <*X_ROOT/pub/issues/sections/articles/topics/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>*>)
</TD>
<TD>
X_NEW_BUTTON(<*Unlock*>, <*X_ROOT/pub/issues/sections/articles/do_unlock.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>*>)
</TD>
</TR>
<TR>
<TD>
X_NEW_BUTTON(<*Preview*>,
<**>,
 <*window.open('X_ROOT/pub/issues/sections/articles/preview.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>', 'fpreview', PREVIEW_OPT); return false*>)
</TD>
<TD>
X_NEW_BUTTON(<*Translate*>, <*X_ROOT/pub/issues/sections/articles/translate.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&Back=<? pencURL($REQUEST_URI); ?>*>)
<? if ($dla) { ?>dnl
</TD><TD>
X_NEW_BUTTON(<*Delete*>, <*X_ROOT/pub/issues/sections/articles/del.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&Back=<? pencURL($REQUEST_URI); ?>*>)
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


B_DIALOG(<*Edit article details*>, <*POST*>, <*do_edit.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="64" MAXLENGTH="64" VALUE="<? pgetHVar($q_art,'Name'); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Type*>)
		<B><? pgetHVar($q_art,'Type'); ?></B>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Uploaded*>)
		<B><? pgetHVar($q_art,'UploadDate'); ?> <? putGS('(yyyy-mm-dd)'); ?></B>
	E_DIALOG_INPUT
	B_DIALOG_PACKEDINPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cOnFrontPage"<? if (getVar($q_art,'OnFrontPage') == "Y") { ?> CHECKED<? } ?>>*>)
		<? putGS('Show article on front page'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cOnSection"<? if (getVar($q_art,'OnSection') == "Y") { ?> CHECKED<? } ?>>*>)
		<? putGS('Show article on section page'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cPublic"<? if (getVar($q_art,'Public') == "Y") { ?> CHECKED<? } ?>>*>)
		<?putGS('Allow users without subscriptions to view the article'); ?>
	E_DIALOG_INPUT
	E_DIALOG_PACKEDINPUT
	B_DIALOG_INPUT(<*Keywords*>)
		<INPUT TYPE="TEXT" NAME="cKeywords" VALUE="<? pgetHVar($q_art,'Keywords'); ?>" SIZE="64" MAXLENGTH="255">
	E_DIALOG_INPUT

<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<? p($Pub); ?>">
<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<? p($Issue); ?>">
<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<? p($Section); ?>">
<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<? p($Article); ?>">
<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? p($Language); ?>">
<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<? p($sLanguage); ?>">
<INPUT TYPE="HIDDEN" NAME="query" VALUE="">

<?
    $fld= "";
    $ftyp= "";
?>

<?
    query ("SHOW COLUMNS FROM X".getSVar($q_art,'Type')." LIKE 'F%'", 'q_fld');
    $nr3=$NUM_ROWS;
    for($loop3=0;$loop3<$nr3;$loop3++) {
	fetchRowNum($q_fld);
	///	query ("SELECT SUBSTRING('?q_fld.0', 2), LOCATE('char', '?q_fld.1', 1), LOCATE('date', '?q_fld.1', 1)" q_substr
	$table= substr ( getNumVar($q_fld,0),1);
	$posc=strpos(getNumVar($q_fld,1),'char');
	$posd=strpos(getNumVar($q_fld,1),'date');

	if (!($posc === false))
	    $type=0;
	elseif (!($posd === false))
	    $type=1;
	else
	    $type=2;

	if ($type != 2) {
	    if ($fld != "")
		$fld= "$fld, \"F$table\"";
	    else
		$fld= "\"F$table\"";

	    if ($ftyp != "")
		$ftyp= "$ftyp, $type";
	    else
		$ftyp= "$type";
	}

	if ($type == 0) { ?>dnl
		<!-- text -->
		B_X_DIALOG_INPUT(<*<? pencHTML($table); ?>:*>)
		<? query ("SELECT ".getNumVar($q_fld,0)." FROM X".getSVar($q_art,'Type')." WHERE NrArticle=$Article AND IdLanguage=$sLanguage", 'q_afld'); ?>dnl
			
                                                <INPUT NAME="<? pencHTML(getNumVar($q_fld,0)); ?>" TYPE="TEXT" VALUE="<? fetchRowNum($q_afld); pgetNumVar($q_afld,0); ?>" SIZE="64" MAXLENGTH="100">
	<? } elseif ($type == 1) { ?>dnl
		<!-- date -->
				<!-- setez data curenta la cimpurile de tip data -->
				<?
				    query ("SELECT F$table from X".getSVar($q_art,'Type')." where NrArticle=$Article AND IdLanguage=$sLanguage", 'q_vd');
				    fetchRowNum($q_vd);
				    if (getNumVar($q_vd,0) == "0000-00-00")
					query ("UPDATE X".getSVar($q_art,'Type')." SET F$table=curdate() WHERE NrArticle=$Article AND IdLanguage=$sLanguage");
				?>dnl
		B_X_DIALOG_INPUT(<*<? pencHTML($table); ?>:*>)
		<? query ("SELECT ".getNumVar($q_fld,0)." FROM X".getSVar($q_art,'Type')." WHERE NrArticle=$Article AND IdLanguage=$sLanguage", 'q_afld'); ?>dnl
			<INPUT NAME="<? pencHTML(getNumVar($q_fld,0)); ?>" TYPE="TEXT" VALUE="<? fetchRowNum($q_afld); pencHTML(getNumVar($q_afld,0)); ?>" SIZE="10" MAXLENGTH="10"> <? putGS('YYYY-MM-DD'); ?>

	<? } else { ?>dnl
		<!-- blob -->

		<?
		    query ("SELECT ".getNumVar($q_fld,0).", length(".getNumVar($q_fld,0).") FROM X".getSVar($q_art,'Type')." WHERE NrArticle=$Article AND IdLanguage=$sLanguage", 'q_afld');
		    fetchRowNum($q_afld);   ?>
			B_X_DIALOG_INPUT(<*<BR><? pencHTML($table); ?>:<BR> X_NEW_BUTTON(<*Edit*>, <*javascript:campfire('Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&eField=<? pencURL(getNumVar($q_fld,0)); ?>')*>)*>, <*TOP*>)
		X_HR
		<table width=100% border=2><tr bgcolor=LightBlue><td><? pgetNumVar($q_afld,0); ?></td></tr></table>
		<BR><P>
	<? } ?>dnl

		E_DIALOG_INPUT

<? }  ?>dnl

	B_DIALOG_BUTTONS
<SCRIPT>
	function do_submit()
	{
		document.dialog.submit();
	}
</SCRIPT>
		X_HR
		<A HREF="javascript:void(do_submit())"><IMG SRC="X_ROOT/img/button/save.gif" BORDER="0" ALT="OK"></A>
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
