B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Articles*>)
<?
    query ("SELECT Number, Name, IdLanguage FROM Articles WHERE 1=0", 'q_art');
?>dnl
E_HEAD

<? if ($access) { 
SET_ACCESS(<*aaa*>, <*AddArticle*>)
?>dnl
B_STYLE
E_STYLE

B_PBODY1

<?
    todefnum('lang');
    todefnum('pub');
    todefnum('iss');
    todefnum('ssect');
    query ("SELECT Number, Name, IdLanguage FROM Articles WHERE IdPublication=$pub AND NrIssue=$iss AND NrSection=$ssect ORDER BY Number, IdLanguage", 'q_art');
?>dnl
B_PBAR
    X_PBUTTON(<*X_ROOT/pub/issues/sections/articles/?Pub=<? pencURL($pub); ?>&Issue=<? pencURL($iss); ?>&Section=<? pencURL($ssect); ?>&Language=<? pencURL($lang); ?>*>, <*Articles*>)
<? if ($aaa) { ?>dnl
    X_PBUTTON(<*X_ROOT/pub/issues/sections/articles/add.php?Pub=<? pencURL($pub); ?>&Issue=<? pencURL($iss); ?>&Section=<? pencURL($ssect); ?>&Language=<? pencURL($lang); ?>*>, <*Add new article*>)
<? } ?>dnl
X_PSEP2
<FORM NAME="FORM_ART" METHOD="GET">
<?
    if ($NUM_ROWS) { ?>dnl
<SELECT NAME="art" ONCHANGE="var f = this.form.art; var v = f.options[f.selectedIndex].value; var x = 'X_ROOT/popup/img.php?lang=<? pencURL($lang); ?>&amp;pub=<? pencURL($pub); ?>&amp;iss=<? pencURL($iss); ?>&amp;ssect=<? pencURL($ssect); ?>&amp;' + v; if (v != 0) { parent.frames[1].location.href = x; }">
	<OPTION VALUE="0"><? putGS('---Select article---'); ?>
	<?
		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($q_art);
			query ('SELECT Name FROM Languages WHERE Id='.getVar($q_art,'IdLanguage'), 'q_ll');
			fetchRow($q_ll);
			pcomboVar('art='.getVar($q_art,'Number').'&slang='.getVar($q_art,'IdLanguage'),'',getHVar($q_art,'Name').'('.getHVar($q_ll,'Name').')');
		    }
	
	?>
</SELECT>
<? } else { ?>dnl
<SELECT DISABLED><OPTION><? putGS('No articles'); ?></SELECT>
<? } ?>dnl
</FORM>
E_PBAR

E_BODY

<? } ?>dnl

E_DATABASE
E_HTML
