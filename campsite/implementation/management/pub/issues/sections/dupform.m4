B_HTML
INCLUDE_PHP_LIB(<*../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Duplicate article*>)
<?php 
    query ("SELECT Number, Name FROM Sections WHERE 1=0", 'q_sect');
?>dnl
E_HEAD

<?php 
SET_ACCESS(<*aaa*>, <*AddArticle*>)
SET_ACCESS(<*msa*>, <*ManageSection*>)
if ($aaa != 0 && $msa != 0) {
?>dnl
B_STYLE
E_STYLE

<?php 
	todefnum('Language');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('dstPub');
	todefnum('dstIssue');
	todefnum('dstSection');
?>dnl

<DIV><TABLE>
<?php 
	if ($Pub == $dstPub && $Issue == $dstIssue) {
?>
	<TR><TD COLSPAN="2">
<?php 
		putGS("The destination issue is the same as the source issue."); echo "<BR>\n";
?>
	</TD></TR>
<?php 
	}
?>
<FORM NAME="ART_DUP" METHOD="GET">
	<TR>
		<TD><?php  putGS("Destination section number"); ?>:</TD>
		<TD><INPUT TYPE="text" NAME="dstSection" SIZE="10" MAXLEN="10" VALUE="<?php  echo $Section; ?>"></TD>
	</TR>
	<TR><TD COLSPAN="2" ALIGN="CENTER">
		<INPUT TYPE="Button" Name="Duplicate" Value="Duplicate section" ONCLICK="var s=this.form.dstSection.value; var h='X_ROOT/pub/issues/sections/do_duplicate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&dstPub=<?php  p($dstPub); ?>&dstIssue=<?php  p($dstIssue); ?>&dstSection=' + s; if (top.fmenu) { top.fmain.location.href=h; } else { top.location.href=h; }">
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&sLanguage=<?php  p($Language); ?>&Language=<?php  p($Language); ?>*>)
	</TD></TR>
</FORM>
</TD></TR></TABLE></DIV>

E_BODY

<?php 
} else {
	if ($aaa == 0) {
?>
		X_AD(<*You do not have the right to add articles.*>)
<?php 
	}
	if ($msa == 0) {
?>
		X_AD(<*You do not have the right to add sections.*>)
<?php 
	}
}
?>dnl

E_DATABASE
E_HTML
