B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Languages*>)
<? query ("SELECT Id, Name FROM Languages WHERE 1=0", 'q_lang');
?>dnl
E_HEAD

<? if ($access) { 
SET_ACCESS(<*mla*>, <*ManageLanguages*>)
?>dnl
B_STYLE
E_STYLE

B_PBODY1

<?
    query ("SELECT Id, Name FROM Languages ORDER BY Name", 'q_lang');
?>dnl
B_PBAR
    X_PBUTTON(<*X_ROOT/languages/*>, <*Languages*>)
<? if ($mla) { ?>dnl
    X_PBUTTON(<*X_ROOT/languages/add.php*>, <*Add new language*>)
<? } ?>dnl
X_PSEP2
<FORM NAME="FORM_LANG" METHOD="GET">
<? if ($NUM_ROWS) { ?>dnl
<SELECT NAME="lng" ONCHANGE="var f=this.form.lng; var v = f.options[f.selectedIndex].value; var x = 'X_ROOT/popup/i1.php?lang=' + v; if (v != 0) { parent.frames[1].location.href = x; }">
	<OPTION VALUE="0"><? putGS('---Select language---'); ?>
		<?  $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($q_lang);
			pcomboVar(getVar($q_lang,'Id'),'',getVar($q_lang,'Name'));
		    }

		?>
</SELECT>
<? } else { ?>dnl
<SELECT DISABLED><OPTION><? putGS('No languages'); ?></SELECT>
<? } ?>dnl
</FORM>
E_PBAR

E_BODY

<? } ?>dnl

E_DATABASE
E_HTML

