B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Articles*>)
<?php 
    query ("SELECT Number, Name, IdLanguage FROM Articles WHERE 1=0", 'q_art');
?>dnl
E_HEAD

<?php  if ($access) { 
SET_ACCESS(<*aaa*>, <*AddArticle*>)
?>dnl
B_STYLE
E_STYLE

B_PBODY1

<?php 
    todefnum('lang');
    todefnum('pub');
    todefnum('iss');
    todefnum('ssect');
    query ("SELECT Number, Name, IdLanguage FROM Articles WHERE IdPublication=$pub AND NrIssue=$iss AND NrSection=$ssect ORDER BY Number, IdLanguage", 'q_art');
?>dnl
B_PBAR
    X_PBUTTON(<*X_ROOT/pub/issues/sections/articles/?Pub=<?php  pencURL($pub); ?>&Issue=<?php  pencURL($iss); ?>&Section=<?php  pencURL($ssect); ?>&Language=<?php  pencURL($lang); ?>*>, <*Articles*>)
<?php  if ($aaa) { ?>dnl
    X_PBUTTON(<*X_ROOT/pub/issues/sections/articles/add.php?Pub=<?php  pencURL($pub); ?>&Issue=<?php  pencURL($iss); ?>&Section=<?php  pencURL($ssect); ?>&Language=<?php  pencURL($lang); ?>*>, <*Add new article*>)
<?php  } ?>dnl
X_PSEP2
<FORM NAME="FORM_ART" METHOD="GET">
<?php 
    if ($NUM_ROWS) { ?>dnl
<SELECT NAME="art" ONCHANGE="var f = this.form.art; var v = f.options[f.selectedIndex].value; var x = 'X_ROOT/popup/img.php?lang=<?php  pencURL($lang); ?>&amp;pub=<?php  pencURL($pub); ?>&amp;iss=<?php  pencURL($iss); ?>&amp;ssect=<?php  pencURL($ssect); ?>&amp;' + v; if (v != 0) { parent.frames[1].location.href = x; }">
	<OPTION VALUE="0"><?php  putGS('---Select article---'); ?>
	<?php 
		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($q_art);
			query ('SELECT Name FROM Languages WHERE Id='.getVar($q_art,'IdLanguage'), 'q_ll');
			fetchRow($q_ll);
			pcomboVar('art='.getVar($q_art,'Number').'&slang='.getVar($q_art,'IdLanguage'),'',getHVar($q_art,'Name').'('.getHVar($q_ll,'Name').')');
		    }
	
	?>
</SELECT>
<?php  } else { ?>dnl
<SELECT DISABLED><OPTION><?php  putGS('No articles'); ?></SELECT>
<?php  } ?>dnl
</FORM>
E_PBAR

E_BODY

<?php  } ?>dnl

E_DATABASE
E_HTML
