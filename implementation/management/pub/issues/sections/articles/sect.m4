B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Duplicate article*>)
<?php 
    query ("SELECT Number, Name FROM Sections WHERE 1=0", 'q_sect');
?>dnl
E_HEAD

<?php  if ($access) {
?>dnl
B_STYLE
E_STYLE

<?php 
	todefnum('Language');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('Article');
	todefnum('dstPub');
	todefnum('dstIssue');

	query ("SELECT Number, Name FROM Sections WHERE IdLanguage=$Language AND IdPublication=$dstPub AND NrIssue=$dstIssue ORDER BY Number", 'q_sect');
?>dnl

<DIV><TABLE BORDER="0">
	<TR>
		<TD VALIGN="TOP" ALIGN="RIGHT" WIDTH="150"><?php  putGS('Section'); ?>: </TD>
		<TD ALIGN="LEFT">
<FORM NAME="FORM_SECT" METHOD="GET">
<?php  if ($NUM_ROWS) { ?>dnl
<SELECT NAME="ssect" ONCHANGE="var f = this.form.ssect; var v = f.options[f.selectedIndex].value; var x = 'X_ROOT/pub/issues/sections/articles/i3.php?Language=<?php  pencURL($Language); ?>&Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pencURL($Issue); ?>&Section=<?php  pencURL($Section); ?>&Article=<?php  pencURL($Article); ?>&dstPub=<?php  pencURL($dstPub); ?>&dstIssue=<?php  pencURL($dstIssue); ?>&dstSection=' + v; if (v != 0) { parent.frames[1].location.href = x; }">
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
<SELECT DISABLED><OPTION><?php  putGS('No sections'); ?></SELECT>
<?php  } ?>dnl
</FORM>
		</TD>
	</TR>
</TABLE></DIV>

E_BODY

<?php  } ?>dnl

E_DATABASE
E_HTML
