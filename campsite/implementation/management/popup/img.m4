B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Images*>)
<?php 
    query ("SELECT Number, Description FROM Images WHERE 1=0", 'q_img');
?>dnl
E_HEAD

<?php  if ($access) { 
SET_ACCESS(<*aia*>, <*AddImage*>)
SET_ACCESS(<*aaa*>, <*AddArticle*>)
?>dnl
B_STYLE
E_STYLE

B_PBODY2
<?php 
    todefnum('lang');
    todefnum('slang');
    todefnum('pub');
    todefnum('iss');
    todefnum('ssect');
    todefnum('art');
    
    query ("SELECT Number, Description FROM Images WHERE IdPublication=$pub AND NrIssue=$iss AND NrSection=$ssect AND NrArticle=$art ORDER BY Number", 'q_img');
?>dnl
B_PBAR
	X_PBUTTON(<*X_ROOT/pub/issues/sections/articles/images/?Pub=<?php  print $pub; ?>&Issue=<?php  print $iss; ?>&Section=<?php  print $ssect; ?>&Article=<?php  print $art; ?>&Language=<?php  print $lang; ?>*>, <*Images*>)
<?php  if ($aia) { ?>dnl
	X_PBUTTON(<*X_ROOT/pub/issues/sections/articles/images/add.php?Pub=<?php  print $pub; ?>&Issue=<?php  print $iss; ?>&Section=<?php  print $ssect; ?>&Article=<?php  print $art; ?>&Language=<?php  print $lang; ?>*>, <*Add image*>)
<?php  } ?>dnl
	X_PSEP
	X_PLABEL2(<*Article*>)
<?php  if ($aaa) { ?>dnl
	X_ABUTTON2(<*X_ROOT/pub/issues/sections/articles/edit_b.php?Pub=<?php  print $pub; ?>&Issue=<?php  print $iss; ?>&Section=<?php  print $ssect; ?>&Article=<?php  print $art; ?>&Language=<?php  print $lang; ?>&sLanguage=<?php  print $slang; ?>*>, <*Edit*>)
	X_ABUTTON2(<*X_ROOT/pub/issues/sections/articles/edit.php?Pub=<?php  print $pub; ?>&Issue=<?php  print $iss; ?>&Section=<?php  print $ssect; ?>&Article=<?php  print $art; ?>&Language=<?php  print $lang; ?>&sLanguage=<?php  print $slang; ?>*>, <*Details*>)
<?php  } ?>dnl
	X_ABUTTON2(<**>, <*Preview*>, <*window.open('X_ROOT/pub/issues/sections/articles/preview.php?Pub=<?php  print $pub; ?>&Issue=<?php  print $iss; ?>&Section=<?php  print $ssect; ?>&Article=<?php  print $art; ?>&Language=<?php  print $lang; ?>&sLanguage=<?php  print $slang; ?>', 'fpreview', PREVIEW_OPT); return false*>)
X_PSEP2
<FORM METHOD="GET" ACTION="X_ROOT/pub/issues/sections/articles/images/view.php" TARGET="fmain" NAME="FORM_IMG">
<?php  if ($NUM_ROWS) { ?>dnl
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
<TR><TD>
<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  pencURL($pub); ?>">
<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  pencURL($iss); ?>">
<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  pencURL($ssect); ?>">
<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  pencURL($art); ?>">
<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  pencURL($lang); ?>">
<SELECT NAME="Image"><?php 
		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($q_img);
			pcomboVar(getVar($q_img,'Number'),'',getHVar($q_img,'Description'));
		    }

?>
</SELECT>
</TD><TD>
X_NEW_BUTTON(<*View*>, <*javascript:void(document.FORM_IMG.submit())*>)
</TD></TR>
</TABLE>
<?php  } else { ?>dnl
<SELECT DISABLED><OPTION><?php  putGS('No images'); ?></SELECT>
<?php  } ?>dnl
</FORM>
E_PBAR

E_BODY

<?php  } ?>dnl

E_DATABASE
E_HTML
