B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Publications*>)
<?php 
    query ("SELECT Id, Name FROM Publications WHERE 1=0", 'q_pub');
    ?>dnl
E_HEAD

<?php  if ($access) { 
SET_ACCESS(<*mpa*>, <*ManagePub*>)
?>dnl
B_STYLE
E_STYLE

B_PBODY2

<?php 
    todefnum('lang');
    query ("SELECT Id, Name FROM Publications ORDER BY Name", 'q_pub');
?>dnl
B_PBAR
    X_PBUTTON(<*X_ROOT/pub/*>, <*Publications*>)
<?php  if ($mpa) { ?>dnl
    X_PBUTTON(<*X_ROOT/pub/add.php*>, <*Add new publication*>)
<?php  } ?>dnl
X_PSEP2
<FORM NAME="FORM_PUB" METHOD="GET">
<?php  if ($NUM_ROWS) { ?>dnl
<SELECT NAME="pub" ONCHANGE="f = this.form.pub; var v = f.options[f.selectedIndex].value; var x = 'X_ROOT/popup/i2.php?lang=<?php  pencURL($lang); ?>&pub=' + v; if (v != 0) { parent.frames[1].location.href = x; }">
	<OPTION VALUE="0"><?php  putGS('---Select publication---'); ?>
		<?php 
		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($q_pub);
			pcomboVar(getVar($q_pub,'Id'),'',getVar($q_pub,'Name'));
		    }
		?>
</SELECT>
<?php  } else { ?>dnl
<SELECT DISABLED><OPTION><?php  putGS('No publications'); ?></SELECT>
<?php  } ?>dnl
</FORM>
E_PBAR

E_BODY

<?php  } ?>dnl

E_DATABASE
E_HTML


