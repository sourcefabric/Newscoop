B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/popup*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Sections*>)
<?php 
    query ("SELECT Number, Name FROM Sections WHERE 1=0", 'q_sect');
?>dnl
E_HEAD

<?php  if ($access) { 
SET_ACCESS(<*msa*>, <*ManageSection*>)
?>dnl
B_STYLE
E_STYLE

B_PBODY2

<?php 
    todefnum('lang');
    todefnum('pub');
    todefnum('iss');
    query ("SELECT Number, Name FROM Sections WHERE IdPublication=$pub AND NrIssue=$iss AND IdLanguage=$lang ORDER BY Number", 'q_sect');
?>dnl
B_PBAR
	X_PBUTTON(<*X_ROOT/pub/issues/sections/?Pub=<?php  pencURL($pub); ?>&Issue=<?php  pencURL($iss); ?>&Language=<?php  pencURL($lang); ?>*>, <*Sections*>)<?php  if ($msa) { ?>dnl
	X_PBUTTON(<*X_ROOT/pub/issues/sections/add.php?Pub=<?php  pencURL($pub); ?>&Issue=<?php  pencURL($iss); ?>&Language=<?php  pencURL($lang); ?>*>, <*Add new section*>)
<?php  } ?>dnl
	X_PSEP
	X_PLABEL2(<*Issue*>)
	X_ABUTTON2(<**>, <*Preview*>, <*window.open('X_ROOT/pub/issues/preview.php?Pub=<?php  pencURL($pub); ?>&Issue=<?php  pencURL($iss); ?>&Language=<?php  pencURL($lang); ?>', 'fpreview', PREVIEW_OPT); return false*>)
X_PSEP2
<FORM NAME="FORM_SECT" METHOD="GET">
<?php  if ($NUM_ROWS) { ?>dnl
<SELECT NAME="ssect" class="input_select" ONCHANGE="var f = this.form.ssect; var v = f.options[f.selectedIndex].value; var x = 'X_ROOT/popup/i4.php?lang=<?php  pencURL($lang); ?>&amp;pub=<?php  pencURL($pub); ?>&amp;iss=<?php  pencURL($iss); ?>&amp;ssect=' + v; if (v != 0) { parent.frames[1].location.href = x; }">
	<OPTION VALUE="0"><?php  putGS('---Select section---'); ?>
	<?php 
		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($q_sect);
			pcomboVar(getVar($q_sect,'Number'),'',getVar($q_sect,'Number').'. '.getVar($q_sect,'Name'));
		    }


	?>
</SELECT>
<?php  } else { ?>dnl
<SELECT class="input_select" DISABLED><OPTION><?php  putGS('No sections'); ?></SELECT>
<?php  } ?>dnl
</FORM>
E_PBAR

E_BODY

<?php  } ?>dnl

E_DATABASE
E_HTML


