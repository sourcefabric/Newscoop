B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Edit article*>)
<SCRIPT LANGUAGE="JavaScript">

function ismodified(){
 if (campeditor.ismodified()==1){
  if (confirm("Do you want to save changes made to article field?")){
  campeditor.beforeunload()
  }
 }
}
</SCRIPT>
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  }
    query ("SELECT Number, Description FROM Images WHERE 1=0", 'q_img');
    query ("SHOW COLUMNS FROM Articles LIKE 'XXYYZZ'", 'q_fld1');
    query ("SELECT Id, Name FROM Classes WHERE 1=0", 'q_cls');
    $okf= 0;
?>dnl
E_HEAD

<?php  if ($access) { 
SET_ACCESS(<*dla*>, <*DeleteArticle*>)
?>dnl

B_STYLE
E_STYLE

B_BODY(<*onbeforeunload="ismodified()"*>)
<?php 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Section');
    todefnum('Language');
    todefnum('sLanguage');
    todefnum('Article');
    todefnum('LockOk');
    todef('Field');
    todef('eField');
    $fldname=substr ( $eField, 1);
?>
B_HEADER(<*Edit article*>)
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
X_CURRENT(<*Field*>, <*<B><?php  p($fldname); ?></B>*>)
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
	    query ("UPDATE Articles SET LockUser=".getVar($Usr,'Id').", LockTime=NOW() WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article AND IdLanguage=$sLanguage");
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
     ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('This article has been locked by $1 ($2) at','<B>'.getHVar($q_luser,'Name'),getHVar($q_luser,'UName').'</B>' ); ?>
		<B><?php  pgetHVar($q_art,'LockTime'); ?></B></LI>
		<LI><?php  putGS('Now is $1','<B>'.getHVar($q_luser,'Now').'</B>'); ?></LI>
		<LI><?php  putGS('Are you sure you want to unlock it?'); ?></LI>
	*>)
	B_MSGBOX_BUTTONS
		REDIRECT(<*Yes*>, <*Yes*>, <*<?php  p($REQUEST_URI); ?>&LockOk=1*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>*>)
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
	<?php  }
    }

    if ($edit_ok) { ?>dnl
<P><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
<TR><TD>

<?php  if (getVar($q_art,'Published') == "Y") { ?>dnl
X_NEW_BUTTON(<*Unpublish*>, <*X_ROOT/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Back=<?php  pencURL($REQUEST_URI); ?>*>)
<?php  } elseif (getVar($q_art,'Published') == "S") { ?>dnl
X_NEW_BUTTON(<*Publish*>, <*X_ROOT/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Back=<?php  pencURL($REQUEST_URI); ?>*>)
<?php  } else { ?>dnl
X_NEW_BUTTON(<*Submit*>, <*X_ROOT/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Back=<?php  pencURL($REQUEST_URI); ?>*>)
<?php  } ?>dnl
</TD>
<TD>
X_NEW_BUTTON(<*Images*>, <*X_ROOT/pub/issues/sections/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>*>)
</TD><TD>
X_NEW_BUTTON(<*Unlock*>, <*X_ROOT/pub/issues/sections/articles/do_unlock.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>*>)
</TD></TR>
<TR><TD>
X_NEW_BUTTON(<*Preview*>, <**>, <*window.open('X_ROOT/pub/issues/sections/articles/preview.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>', 'fpreview', PREVIEW_OPT); return false*>)
</TD><TD>
X_NEW_BUTTON(<*Translate*>, <*X_ROOT/pub/issues/sections/articles/translate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&Back=<?php  pencURL($REQUEST_URI); ?>*>)
<?php  if ($dla) { ?>dnl
</TD><TD>
X_NEW_BUTTON(<*Delete*>, <*X_ROOT/pub/issues/sections/articles/del.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Back=<?php  pencURL($REQUEST_URI); ?>*>)
</TD><TD>
X_NEW_BUTTON(<*Edit details*>, <*X_ROOT/pub/issues/sections/articles/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>*>)
</TD><TD>
<?php  } ?>dnl
</TD></TR>
</TABLE>

<P><?php 
    query ("SHOW COLUMNS FROM X".getSVar($q_art,'Type')." LIKE 'F%'", 'q_fld1');
    $idx= 0;
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRowNum($q_fld1);
	///	query ("SELECT SUBSTRING('?q_fld.0', 2), LOCATE('char', '?q_fld.1', 1), LOCATE('date', '?q_fld.1', 1)" q_substr
	$table= substr ( getNumVar($q_fld1,0),1);
	$posc=strpos(getNumVar($q_fld1,1),'char');
	$posd=strpos(getNumVar($q_fld1,1),'date');
	
	if (!($posc === false))
	    $type=0;
	elseif (!($posd === false))
	    $type=1;
	else
	    $type=2;
	
	$Field=$table;

	if ($eField == "")
	    $fedit_ok= 1;
	else
	    $fedit_ok= 0;

	if ($fedit_ok == 0) {
	    if ($eField == "F$Field")
		$fedit_ok= 1;
	}

	if ($fedit_ok) {
	    if ($type == 2) {
		$okf= 1;
		query ("SELECT F$Field FROM X".getSVar($q_art,'Type')." WHERE NrArticle=$Article AND IdLanguage=$sLanguage", 'q_fld');
		fetchRowNum($q_fld);
	?>
<P ALIGN="CENTER">
<TABLE BORDER="1" CELLSPACING="1" CELLPADDING="1" WIDTH="92%">
<TR><TD BGCOLOR="#C0D0FF"><B>&nbsp;Campfire</B></TD>
</TR>
<TR>
<TD>
<APPLET name="campeditor" 
    width="100%" height="420" align="baseline"
    >
   <PARAM NAME="code" VALUE="Campfire.class">
   <PARAM NAME="codebase" VALUE="java/">
   <PARAM NAME="archive" VALUE="campfire.jar">
    <PARAM NAME="type" VALUE="application/x-java-applet;version=1.3">
    <PARAM NAME="model" VALUE="models/HyaluronicAcid.xyz">
    <PARAM NAME="scriptable" VALUE="true">
<PARAM NAME="port" VALUE="<?php  p($SERVER_PORT); ?>">
<PARAM NAME="script" VALUE="X_ROOT/pub/issues/sections/articles/upload.php">
<PARAM NAME="debug_" VALUE="">
<PARAM NAME="linkscript" VALUE="http://<?php  pencHTML($SERVER_NAME); ?>:<?php  pencHTML($SERVER_PORT); ?>X_ROOT/pub/issues/sections/articles/list.php">
<PARAM NAME="clip" VALUE="">
<PARAM NAME="LangCode" VALUE="<?php  pLanguageCode(); ?>">
<PARAM NAME="UserId" VALUE="<?php  pgetHVar($Usr,'Id'); ?>">
<PARAM NAME="UserKey" VALUE="<?php  pgetHVar($Usr,'KeyId'); ?>">
<PARAM NAME="IdPublication" VALUE="<?php  p($Pub); ?>">
<PARAM NAME="NrIssue" VALUE="<?php  p($Issue); ?>">
<PARAM NAME="NrSection" VALUE="<?php  p($Section); ?>">
<PARAM NAME="NrArticle" VALUE="<?php  p($Article); ?>">
<PARAM NAME="IdLanguage" VALUE="<?php  p($sLanguage); ?>">
<PARAM NAME="Field" VALUE="<?php  p(encS($Field)); ?>">
<?php  $idx++; ?>
<PARAM NAME="idx" VALUE="<?php  p($idx); ?>">
<PARAM NAME="Content" VALUE="<?php  pencParam(getNumVar($q_fld,0)); ?>">
<?php 
    query ("SELECT Number, Description FROM Images WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND NrArticle=$Article", 'q_img');
    $v_i= 0;
    $nr2=$NUM_ROWS;
    for($loop2=0;$loop2<$nr2;$loop2++) {
	fetchRow($q_img);
	if ($okf) {
	    print ("<PARAM NAME=\"image$v_i\" VALUE=\"".getVar($q_img,'Number').", ".encParam(getVar($q_img,'Description'))."\">\n");
	    $v_i++;
	}
    }

    query ("SELECT Id, Name FROM Classes WHERE IdLanguage=$sLanguage ORDER BY Name", 'q_cls');
    $v_i= 0;
    $nr2=$NUM_ROWS;
    for($loop2=0;$loop2<$nr2;$loop2++) {
    fetchRow($q_cls);
        if ($okf) {
	    print ("<PARAM NAME=\"tol#$v_i\" VALUE=\"".encParam(getVar($q_cls,'Name'))."\">\n");
	    $v_i++;
	}
    }
?></APPLET>
</TD>
</TR>
</TABLE>
<P>
<?php 
    $okf= 0;
    }
    }

    }

    }

} else { ?>dnl
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
