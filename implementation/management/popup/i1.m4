B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/popup*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Menu*>)
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

<?php  todefnum('lang'); ?>dnl

<FRAMESET ROWS="70, *" BORDER="0">
    <FRAME SRC="pub.php?lang=<?php  pencURL($lang); ?>" NAME="fpub" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
    <FRAME SRC="empty.php?bg=1" NAME="f2" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
</FRAMESET>

<?php  } ?>dnl

E_DATABASE
E_HTML
