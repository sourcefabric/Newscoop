B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Duplicate article*>)
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

<?php 
	todefnum('Language');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('Article');
	todefnum('dstPub');
?>dnl

<FRAMESET ROWS="50, *" BORDER="1">
    <FRAME SRC="iss.php?Language=<?php  pencURL($Language); ?>&Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pencURL($Issue); ?>&Section=<?php  pencURL($Section); ?>&Article=<?php  pencURL($Article); ?>&dstPub=<?php  pencURL($dstPub); ?>" NAME="fiss" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
    <FRAME SRC="copyright.php" NAME="cr" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
</FRAMESET>

<?php  } ?>

E_DATABASE
E_HTML
