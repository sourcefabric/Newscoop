INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues/sections*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE(<*Duplicate section*>)
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

<?php 
SET_ACCESS(<*aaa*>, <*AddArticle*>)
SET_ACCESS(<*msa*>, <*ManageSection*>)
if ($aaa != 0 && $msa != 0) {
	todefnum('Language');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
?>dnl

<FRAMESET ROWS="210, *" BORDER="1">
    <FRAME SRC="duplicate.php?Language=<?php  pencURL($Language); ?>&Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pencURL($Issue); ?>&Section=<?php  pencURL($Section); ?>" NAME="fpub" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
    <FRAME SRC="i0.php?Language=<?php  pencURL($Language); ?>&Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pencURL($Issue); ?>&Section=<?php  pencURL($Section); ?>" NAME="fpub" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
</FRAMESET>

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

<?php  } ?>dnl

E_DATABASE
E_HTML
