B_HTML
INCLUDE_PHP_LIB(<*../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Duplicate section*>)
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

<?
SET_ACCESS(<*aaa*>, <*AddArticle*>)
SET_ACCESS(<*msa*>, <*ManageSection*>)
if ($aaa != 0 && $msa != 0) {
	todefnum('Language');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
?>dnl

<FRAMESET ROWS="210, *" BORDER="1">
    <FRAME SRC="duplicate.php?Language=<? pencURL($Language); ?>&Pub=<? pencURL($Pub); ?>&Issue=<? pencURL($Issue); ?>&Section=<? pencURL($Section); ?>" NAME="fpub" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
    <FRAME SRC="i0.php?Language=<? pencURL($Language); ?>&Pub=<? pencURL($Pub); ?>&Issue=<? pencURL($Issue); ?>&Section=<? pencURL($Section); ?>" NAME="fpub" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
</FRAMESET>

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

<? } ?>dnl

E_DATABASE
E_HTML
