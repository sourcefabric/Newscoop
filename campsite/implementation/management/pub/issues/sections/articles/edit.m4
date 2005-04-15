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
	X_TITLE(<*Edit article details*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  }
    query ("SELECT IdLanguage FROM Articles WHERE 1=0", 'q_al');
    query ("SELECT Name FROM Languages WHERE 1=0", 'q_ls');
    query ("SHOW COLUMNS FROM Articles LIKE 'XXYYZZ'", 'q_fld');
?>dnl

E_HEAD

B_STYLE
E_STYLE

<?php  if ($access) {
SET_ACCESS(<*aaa*>, <*AddArticle*>)
SET_ACCESS(<*caa*>, <*ChangeArticle*>)
SET_ACCESS(<*pa*>, <*Publish*>)
SET_ACCESS(<*dla*>, <*DeleteArticle*>)
?>dnl
B_BODY
<SCRIPT language="JavaScript">

function campfire(atr){
	if(navigator.userAgent.indexOf("Firebird") != -1) {
		location.href="/priv/pub/issues/sections/articles/edit_b_firebird.php?"+atr,null,"location=no,toolbar=no,menubar=no,scrollbars=no,resizable=yes";
	}else if(navigator.appName.indexOf("Netscape") != -1) {
		location.href="X_ROOT/pub/issues/sections/articles/edit_b_ns.php?"+atr,null,"location=no,toolbar=no,menubar=no,scrollbars=no,resizable=yes";
	}else if(navigator.userAgent.indexOf("Mac") != -1) {
		location.href="X_ROOT/pub/issues/sections/articles/edit_b_apple.php?"+atr,null,"location=no,toolbar=no,menubar=no,scrollbars=no,resizable=yes";
	}else if(navigator.userAgent.indexOf("IE") != -1) {
	    location.href="X_ROOT/pub/issues/sections/articles/edit_b.php?"+atr,null,"location=no,toolbar=no,menubar=no,scrollbars=no,resizable=yes";
	}else {
	    location.href="X_ROOT/pub/issues/sections/articles/edit_b_ns.php?"+atr,null,"location=no,toolbar=no,menubar=no,scrollbars=no,resizable=yes";
	}
}

</SCRIPT>
<?php 
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
X_HBUTTON(<*Articles*>, <*pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>*>)
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
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
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></B>*>)
X_CURRENT(<*Article*>, <*<B><?php  pgetHVar($q_art,'Name'); ?> (<?php  pgetHVar($q_slang,'Name'); ?>)</B>*>)
E_CURRENT

CHECK_XACCESS(<*ChangeArticle*>)

<?php 
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
<?php
    query ("SELECT *, NOW() AS Now FROM Users WHERE Id=".getVar($q_art,'LockUser'), 'q_luser');
    fetchRow($q_luser);
	$user_name = getHVar($q_luser,'Name');
	$user_uname = getHVar($q_luser,'UName');
	if ($user_uname == "") {
		$user_name = "(deleted user)";
		$user_uname = "---";
		query ("SELECT NOW() as Now", 'q_luser');
		fetchRow($q_luser);
	}
	$now = getHVar($q_luser,'Now');
?>dnl
	X_MSGBOX_TEXT(<*<LI><?php putGS('This article has been locked by $1 ($2) at',"<B>$user_name</B>","<B>$user_uname</B>"); ?>
		<B><?php pgetHVar($q_art,'LockTime'); ?></B></LI>
		<LI><?php putGS('Now is $1',"<B>$now</B>"); ?></LI>
		<LI><?php putGS('Are you sure you want to unlock it?'); ?></LI>
	*>)
	B_MSGBOX_BUTTONS
		REDIRECT(<*Yes*>, <*Yes*>, <*X_ROOT/pub/issues/sections/articles/edit.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&sLanguage=<?php p($sLanguage); ?>&Language=<?php p($Language); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&LockOk=1*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>*>)
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
	<?php  }
    }

    if ($edit_ok) { ?>dnl
<P>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
<TR><TD>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
<TR>
<?php 
    if (getVar($q_art,'Published') == "Y") { ?>dnl
<?php  if ($pa) { ?>dnl
<TD>X_NEW_BUTTON(<*Unpublish*>, <*X_ROOT/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Back=<?php  pencURL($REQUEST_URI); ?>*>)</TD>
<?php  } ?>
<?php  } elseif (getVar($q_art,'Published') == "S") { ?>dnl
<?php  if ($pa) { ?>dnl
<TD>X_NEW_BUTTON(<*Publish*>, <*X_ROOT/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Back=<?php  pencURL($REQUEST_URI); ?>*>)</TD>
<?php  } ?>
<?php  } else { ?>dnl
<TD>X_NEW_BUTTON(<*Submit*>, <*X_ROOT/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Back=<?php  pencURL($REQUEST_URI); ?>*>)</TD>
<?php  } ?>dnl

<TD>
X_NEW_BUTTON(<*Images*>, <*X_ROOT/pub/issues/sections/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>*>)
</TD>
<TD>
X_NEW_BUTTON(<*Topics*>, <*X_ROOT/pub/issues/sections/articles/topics/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>*>)
</TD>
<TD>
X_NEW_BUTTON(<*Unlock*>, <*X_ROOT/pub/issues/sections/articles/do_unlock.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>*>)
</TD>
</TR>
<TR>
<TD>
X_NEW_BUTTON(<*Preview*>,
<**>,
 <*window.open('X_ROOT/pub/issues/sections/articles/preview.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>', 'fpreview', PREVIEW_OPT); return false*>)
</TD>
<?php  if ($aaa) { ?>dnl
<TD>
X_NEW_BUTTON(<*Translate*>, <*X_ROOT/pub/issues/sections/articles/translate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&Back=<?php  pencURL($REQUEST_URI); ?>*>)
</TD>
<?php  } ?>
<?php  if ($dla) { ?>dnl
<TD>
X_NEW_BUTTON(<*Delete*>, <*X_ROOT/pub/issues/sections/articles/del.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Back=<?php  pencURL($REQUEST_URI); ?>*>)
<?php  } ?>dnl
</TD>
<?php  if ($aaa) { ?>dnl
<TD>
X_NEW_BUTTON(<*Duplicate*>, <*X_ROOT/pub/issues/sections/articles/fduplicate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>*>)
</TD>
<?php  } ?>dnl
</TR>
</TABLE>
</TD><TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*edit.php*>)
		<TD><?php  putGS('Language'); ?>:</TD>
		<TD><SELECT NAME="sLanguage">
<?php 
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
		<TD>SUBMIT(<*Search*>, <*Search*>)</TD>
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
	E_SEARCH_DIALOG
</TD></TR>
</TABLE>


B_DIALOG(<*Edit article details*>, <*POST*>, <*do_edit.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="64" MAXLENGTH="140" VALUE="<?php  pgetHVar($q_art,'Name'); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Type*>)
		<B><?php  pgetHVar($q_art,'Type'); ?></B>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Uploaded*>)
		<B><?php  pgetHVar($q_art,'UploadDate'); ?> <?php  putGS('(yyyy-mm-dd)'); ?></B>
	E_DIALOG_INPUT
	B_DIALOG_PACKEDINPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cOnFrontPage"<?php  if (getVar($q_art,'OnFrontPage') == "Y") { ?> CHECKED<?php  } ?>>*>)
		<?php  putGS('Show article on front page'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cOnSection"<?php  if (getVar($q_art,'OnSection') == "Y") { ?> CHECKED<?php  } ?>>*>)
		<?php  putGS('Show article on section page'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cPublic"<?php  if (getVar($q_art,'Public') == "Y") { ?> CHECKED<?php  } ?>>*>)
		<?php putGS('Allow users without subscriptions to view the article'); ?>
	E_DIALOG_INPUT
	E_DIALOG_PACKEDINPUT
	B_DIALOG_INPUT(<*Keywords*>)
		<INPUT TYPE="TEXT" NAME="cKeywords" VALUE="<?php  pgetHVar($q_art,'Keywords'); ?>" SIZE="64" MAXLENGTH="255">
	E_DIALOG_INPUT
	<?php
	if (function_exists ("incModFile"))
		incModFile ();
	?>
<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php  p($sLanguage); ?>">
<INPUT TYPE="HIDDEN" NAME="query" VALUE="">

<?php 
    $fld= "";
    $ftyp= "";
?>

<?php 
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
		B_X_DIALOG_INPUT(<*<?php  pencHTML($table); ?>:*>)
		<?php  query ("SELECT ".getNumVar($q_fld,0)." FROM X".getSVar($q_art,'Type')." WHERE NrArticle=$Article AND IdLanguage=$sLanguage", 'q_afld'); ?>dnl
			
                                                <INPUT NAME="<?php  pencHTML(getNumVar($q_fld,0)); ?>" TYPE="TEXT" VALUE="<?php  fetchRowNum($q_afld); pgetNumVar($q_afld,0); ?>" SIZE="64" MAXLENGTH="100">
	<?php  } elseif ($type == 1) { ?>dnl
		<!-- date -->
				<!-- setez data curenta la cimpurile de tip data -->
				<?php 
				    query ("SELECT F$table from X".getSVar($q_art,'Type')." where NrArticle=$Article AND IdLanguage=$sLanguage", 'q_vd');
				    fetchRowNum($q_vd);
				    if (getNumVar($q_vd,0) == "0000-00-00")
					query ("UPDATE X".getSVar($q_art,'Type')." SET F$table=curdate() WHERE NrArticle=$Article AND IdLanguage=$sLanguage");
				?>dnl
		B_X_DIALOG_INPUT(<*<?php  pencHTML($table); ?>:*>)
		<?php  query ("SELECT ".getNumVar($q_fld,0)." FROM X".getSVar($q_art,'Type')." WHERE NrArticle=$Article AND IdLanguage=$sLanguage", 'q_afld'); ?>dnl
			<INPUT NAME="<?php  pencHTML(getNumVar($q_fld,0)); ?>" TYPE="TEXT" VALUE="<?php  fetchRowNum($q_afld); pencHTML(getNumVar($q_afld,0)); ?>" SIZE="10" MAXLENGTH="10"> <?php  putGS('YYYY-MM-DD'); ?>

	<?php  } else { ?>dnl
		<!-- blob -->

		<?php 
		    query ("SELECT ".getNumVar($q_fld,0).", length(".getNumVar($q_fld,0).") FROM X".getSVar($q_art,'Type')." WHERE NrArticle=$Article AND IdLanguage=$sLanguage", 'q_afld');
		    fetchRowNum($q_afld);   ?>
			B_X_DIALOG_INPUT(<*<BR><?php  pencHTML($table); ?>:<BR> X_NEW_BUTTON(<*Edit*>, <*javascript:campfire('Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&eField=<?php  pencURL(getNumVar($q_fld,0)); ?>')*>)*>, <*TOP*>)
		X_HR
		<table width=100% border=2><tr bgcolor=LightBlue><td><?php  pgetNumVar($q_afld,0); ?></td></tr></table>
		<BR><P>
	<?php  } ?>dnl

		E_DIALOG_INPUT

<?php  }  ?>dnl

	B_DIALOG_BUTTONS
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG


<?php  } ?>dnl

<?php  } else { ?>dnl
    X_XAD(<*You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users.*>, <*pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>*>)
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
