B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Issues*>)
<?php  query ("SELECT Number, Name FROM Issues WHERE 1=0", 'q_iss'); ?>dnl
E_HEAD

<?php  if ($access) { 
SET_ACCESS(<*mpa*>, <*ManagePub*>)
SET_ACCESS(<*mia*>, <*ManageIssue*>)
?>dnl
B_STYLE
E_STYLE

B_PBODY1

<?php 
    todefnum('lang');
    todefnum('lang');
    todefnum('pub');    
    query ("SELECT Number, Name FROM Issues WHERE IdPublication=$pub AND IdLanguage=$lang ORDER BY Number DESC", 'q_iss');
?>dnl
B_PBAR
    X_PBUTTON(<*X_ROOT/pub/issues/?Pub=<?php  pencURL($pub); ?>*>, <*Issues*>)
<?php  if ($mia) { ?>dnl
    X_PBUTTON(<*X_ROOT/pub/issues/qadd.php?Pub=<?php  pencURL($pub); ?>*>, <*Add new issue*>)
<?php  } 
    if ($mpa) { ?>dnl
	X_PSEP
	X_PLABEL1(<*Publication*>)
	X_ABUTTON1(<*X_ROOT/pub/edit.php?Pub=<?php  pencURL($pub); ?>*>, <*Edit publication*>)
<?php  } ?>dnl
X_PSEP2
<FORM NAME="FORM_ISS" METHOD="GET">
<?php  if ($NUM_ROWS) { ?>dnl
<SELECT NAME="iss" ONCHANGE="var f = this.form.iss; var v = f.options[f.selectedIndex].value; var x = 'X_ROOT/popup/i3.php?lang=<?php  pencURL($lang); ?>&amp;pub=<?php  pencURL($pub); ?>&amp;iss=' + v; if (v != 0) { parent.frames[1].location.href = x; }">
	<OPTION VALUE="0"><?php  putGS('---Select issue---'); ?>
		<?php 
		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($q_iss);
			pcomboVar(getVar($q_iss,'Number'),'',getVar($q_iss,'Number').'. '.getVar($q_iss,'Name'));
		    }
		?>
</SELECT>
<?php  } else { ?>dnl
<SELECT DISABLED><OPTION><?php  putGS('No issues'); ?></SELECT>
<?php  } ?>dnl
</FORM>
E_PBAR

E_BODY

<?php  } ?>dnl

E_DATABASE
E_HTML


