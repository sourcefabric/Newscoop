B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Duplicate article*>)
E_HEAD

B_STYLE
E_STYLE

<?php 
	todefnum('Language');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('Article');

	query ("SELECT Id, Name FROM Publications ORDER BY Name ASC", 'q_pub');
?>dnl

<DIV><TABLE BORDER="0">
	<TR>
		<TD VALIGN="TOP" ALIGN="RIGHT" WIDTH="150"><?php  putGS('Publication'); ?>: </TD>
		<TD ALIGN="LEFT">
<FORM NAME="FORM_PUB" METHOD="GET">
<?php  if ($NUM_ROWS) { ?>dnl
<SELECT NAME="pub" ONCHANGE="var f = this.form.pub; var v = f.options[f.selectedIndex].value; var x = 'X_ROOT/pub/issues/sections/articles/i1.php?Language=<?php  pencURL($Language); ?>&Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pencURL($Issue); ?>&Section=<?php  pencURL($Section); ?>&Article=<?php  pencURL($Article); ?>&dstPub=' + v; if (v != 0) { parent.frames[1].location.href = x; }">
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
		</TD>
	</TR>
</TABLE></DIV>

E_BODY

E_DATABASE
E_HTML
