INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues/sections*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE(<*Menu*>)
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

<?php 
	todefnum('Language');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('dstPub');
	todefnum('dstIssue');
?>dnl

<FRAMESET ROWS="90, *" BORDER="1">
    <FRAME SRC="dupform.php?Language=<?php  pencURL($Language); ?>&Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pencURL($Issue); ?>&Section=<?php  pencURL($Section); ?>&dstPub=<?php  pencURL($dstPub); ?>&dstIssue=<?php  pencURL($dstIssue); ?>" NAME="fsect" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
    <FRAME SRC="copyright.php" NAME="cr" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
</FRAMESET>

<?php  } ?>dnl

E_DATABASE
E_HTML
