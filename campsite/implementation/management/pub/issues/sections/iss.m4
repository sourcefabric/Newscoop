B_HTML
INCLUDE_PHP_LIB(<*../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Duplicate article*>)
<?php  query ("SELECT Number, Name FROM Issues WHERE 1=0", 'q_iss'); ?>dnl
E_HEAD

B_STYLE
E_STYLE

<?php 
	todefnum('Language');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('dstPub');
	todefnum('dstIssue');

	query ("SELECT Number, Name FROM Issues WHERE IdPublication=$dstPub AND IdLanguage=$Language ORDER BY Number DESC", 'q_iss');
?>dnl

<DIV><TABLE BORDER="0">
	<TR>
		<TD VALIGN="TOP" ALIGN="RIGHT" WIDTH="150"><?php  putGS('Issue'); ?>: </TD>
		<TD ALIGN="LEFT">
<FORM NAME="FORM_ISS" METHOD="GET">
<?php  if ($NUM_ROWS) { ?>dnl
<SELECT NAME="iss" ONCHANGE="var f = this.form.iss; var v = f.options[f.selectedIndex].value; var x = 'X_ROOT/pub/issues/sections/i2.php?Language=<?php  pencURL($Language); ?>&Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pencURL($Issue); ?>&Section=<?php  pencURL($Section); ?>&dstPub=<?php  pencURL($dstPub); ?>&dstIssue=' + v; if (v != 0) { parent.frames[1].location.href = x; }">
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
		</TD>
	</TR>
</TABLE></DIV>

E_BODY

E_DATABASE
E_HTML
