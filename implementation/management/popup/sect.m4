B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Sections*>)
<?
    query ("SELECT Number, Name FROM Sections WHERE 1=0", 'q_sect');
?>dnl
E_HEAD

<? if ($access) { 
SET_ACCESS(<*msa*>, <*ManageSection*>)
?>dnl
B_STYLE
E_STYLE

B_PBODY2

<?
    todefnum('lang');
    todefnum('pub');
    todefnum('iss');
    query ("SELECT Number, Name FROM Sections WHERE IdPublication=$pub AND NrIssue=$iss AND IdLanguage=$lang ORDER BY Number", 'q_sect');
?>dnl
B_PBAR
	X_PBUTTON(<*X_ROOT/pub/issues/sections/?Pub=<? pencURL($pub); ?>&Issue=<? pencURL($iss); ?>&Language=<? pencURL($lang); ?>*>, <*Sections*>)<? if ($msa) { ?>dnl
	X_PBUTTON(<*X_ROOT/pub/issues/sections/add.php?Pub=<? pencURL($pub); ?>&Issue=<? pencURL($iss); ?>&Language=<? pencURL($lang); ?>*>, <*Add new section*>)
<? } ?>dnl
	X_PSEP
	X_PLABEL2(<*Issue*>)
	X_ABUTTON2(<**>, <*Preview*>, <*window.open('X_ROOT/pub/issues/preview.php?Pub=<? pencURL($pub); ?>&Issue=<? pencURL($iss); ?>&Language=<? pencURL($lang); ?>', 'fpreview', PREVIEW_OPT); return false*>)
X_PSEP2
<FORM NAME="FORM_SECT" METHOD="GET">
<? if ($NUM_ROWS) { ?>dnl
<SELECT NAME="ssect" ONCHANGE="var f = this.form.ssect; var v = f.options[f.selectedIndex].value; var x = 'X_ROOT/popup/i4.php?lang=<? pencURL($lang); ?>&amp;pub=<? pencURL($pub); ?>&amp;iss=<? pencURL($iss); ?>&amp;ssect=' + v; if (v != 0) { parent.frames[1].location.href = x; }">
	<OPTION VALUE="0"><? putGS('---Select section---'); ?>
	<?
		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($q_sect);
			pcomboVar(getVar($q_sect,'Number'),'',getVar($q_sect,'Number').'. '.getVar($q_sect,'Name'));
		    }


	?>
</SELECT>
<? } else { ?>dnl
<SELECT DISABLED><OPTION><? putGS('No sections'); ?></SELECT>
<? } ?>dnl
</FORM>
E_PBAR

E_BODY

<? } ?>dnl

E_DATABASE
E_HTML


