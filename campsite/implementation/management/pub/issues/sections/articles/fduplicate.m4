B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Duplicate article*>)
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE
CHECK_XACCESS(<*AddArticle*>)
<?
    if ($xaccess) {
?>

<?
	todefnum('Language');
	todefnum('sLanguage');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('Article');
?>dnl

<FRAMESET ROWS="280, *" BORDER="1">
    <FRAME SRC="duplicate.php?Language=<? pencURL($Language); ?>&sLanguage=<? pencURL($sLanguage); ?>&Pub=<? pencURL($Pub); ?>&Issue=<? pencURL($Issue); ?>&Section=<? pencURL($Section); ?>&Article=<? pencURL($Article); ?>" NAME="fpub" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
    <FRAME SRC="i0.php?Language=<? pencURL($Language); ?>&sLanguage=<? pencURL($sLanguage); ?>&Pub=<? pencURL($Pub); ?>&Issue=<? pencURL($Issue); ?>&Section=<? pencURL($Section); ?>&Article=<? pencURL($Article); ?>" NAME="fpub" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
</FRAMESET>

<? } else { ?>dnl
	X_AD(<*You do not have the right to add articles.*>)
<? } ?>dnl

<? } ?>dnl

E_DATABASE
E_HTML
