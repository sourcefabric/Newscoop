B_HTML
INCLUDE_PHP_LIB(<*../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Duplicate article*>)
<?
    query ("SELECT Number, Name FROM Sections WHERE 1=0", 'q_sect');
?>dnl
E_HEAD

<?
SET_ACCESS(<*aaa*>, <*AddArticle*>)
SET_ACCESS(<*msa*>, <*ManageSection*>)
if ($aaa != 0 && $msa != 0) {
?>dnl
B_STYLE
E_STYLE

<?
	todefnum('Language');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('dstPub');
	todefnum('dstIssue');
	todefnum('dstSection');
?>dnl

<DIV><TABLE>
<?
	if ($Pub == $dstPub && $Issue == $dstIssue) {
?>
	<TR><TD COLSPAN="2">
<?
		putGS("The destination issue is the same as the source issue."); echo "<BR>\n";
?>
	</TD></TR>
<?
	}
?>
<FORM NAME="ART_DUP" METHOD="GET">
	<TR>
		<TD><? putGS("Destination section number"); ?>:</TD>
		<TD><INPUT TYPE="text" NAME="dstSection" SIZE="10" MAXLEN="10" VALUE="<? echo $Section; ?>"></TD>
	</TR>
	<TR><TD COLSPAN="2" ALIGN="CENTER">
		<INPUT TYPE="Button" Name="Duplicate" Value="Duplicate section" ONCLICK="var s=this.form.dstSection.value; var h='X_ROOT/pub/issues/sections/do_duplicate.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Language=<? p($Language); ?>&dstPub=<? p($dstPub); ?>&dstIssue=<? p($dstIssue); ?>&dstSection=' + s; if (top.fmenu) { top.fmain.location.href=h; } else { top.location.href=h; }">
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&sLanguage=<? p($Language); ?>&Language=<? p($Language); ?>*>)
	</TD></TR>
</FORM>
</TD></TR></TABLE></DIV>

E_BODY

<?
} else {
	if ($aaa == 0) {
?>
		X_AD(<*You do not have the right to add articles.*>)
<?
	}
	if ($msa == 0) {
?>
		X_AD(<*You do not have the right to add sections.*>)
<?
	}
}
?>dnl

E_DATABASE
E_HTML
