B_HTML
INCLUDE_PHP_LIB(<*../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Duplicate article*>)
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

<?
	todefnum('Language');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
?>dnl

<FRAMESET ROWS="50, *" BORDER="1">
    <FRAME SRC="pub.php?Language=<? pencURL($Language); ?>&Pub=<? pencURL($Pub); ?>&Issue=<? pencURL($Issue); ?>&Section=<? pencURL($Section); ?>" NAME="fiss" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
    <FRAME SRC="copyright.php" NAME="cr" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
</FRAMESET>

<? } ?>

E_DATABASE
E_HTML
