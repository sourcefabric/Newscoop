B_HTML
INCLUDE_PHP_LIB(<*../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Menu*>)
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

<?
	todefnum('Language');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('dstPub');
	todefnum('dstIssue');
?>dnl

<FRAMESET ROWS="90, *" BORDER="1">
    <FRAME SRC="dupform.php?Language=<? pencURL($Language); ?>&Pub=<? pencURL($Pub); ?>&Issue=<? pencURL($Issue); ?>&Section=<? pencURL($Section); ?>&dstPub=<? pencURL($dstPub); ?>&dstIssue=<? pencURL($dstIssue); ?>" NAME="fsect" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
    <FRAME SRC="copyright.php" NAME="cr" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
</FRAMESET>

<? } ?>dnl

E_DATABASE
E_HTML
