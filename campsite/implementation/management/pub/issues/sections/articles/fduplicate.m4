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
CHECK_XACCESS(<*AddArticle*>)
<?php 
    if ($xaccess) {
?>

<?php 
	todefnum('Language');
	todefnum('sLanguage');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('Article');
?>dnl

<FRAMESET ROWS="280, *" BORDER="1">
    <FRAME SRC="duplicate.php?Language=<?php  pencURL($Language); ?>&sLanguage=<?php  pencURL($sLanguage); ?>&Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pencURL($Issue); ?>&Section=<?php  pencURL($Section); ?>&Article=<?php  pencURL($Article); ?>" NAME="fpub" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
    <FRAME SRC="i0.php?Language=<?php  pencURL($Language); ?>&sLanguage=<?php  pencURL($sLanguage); ?>&Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pencURL($Issue); ?>&Section=<?php  pencURL($Section); ?>&Article=<?php  pencURL($Article); ?>" NAME="fpub" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
</FRAMESET>

<?php  } else { ?>dnl
	X_AD(<*You do not have the right to add articles.*>)
<?php  } ?>dnl

<?php  } ?>dnl

E_DATABASE
E_HTML
