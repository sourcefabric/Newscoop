B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Duplicate article*>)
<?
    query ("SELECT Number, Name FROM Sections WHERE 1=0", 'q_sect');
?>dnl
E_HEAD

<? if ($access) {
?>dnl
B_STYLE
E_STYLE

<?
	todefnum('Language');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('Article');
	todefnum('dstPub');
	todefnum('dstIssue');
	todefnum('dstSection');
?>dnl

<DIV><TABLE><TR><TD WIDTH="150"></TD><TD>
<?
	if ($Pub == $dstPub && $Issue == $dstIssue && $Section == $dstSection) {
		putGS("The destination section is the same as the source section."); echo "<BR>\n";
	}
?>
<FORM NAME="ART_DUP" METHOD="GET">
	<INPUT TYPE="Button" Name="Duplicate" Value="Duplicate article" ONCLICK="var h='X_ROOT/pub/issues/sections/articles/do_duplicate.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&dstPub=<? p($dstPub); ?>&dstIssue=<? p($dstIssue); ?>&dstSection=<? p($dstSection); ?>'; if (top.fmenu) { top.fmain.location.href=h; } else { top.location.href=h; }">
	REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/issues/sections/articles/edit.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&sLanguage=<? p($Language); ?>&Language=<? p($Language); ?>*>)
</FORM>
</TD></TR></TABLE></DIV>

E_BODY

<? } ?>dnl

E_DATABASE
E_HTML
