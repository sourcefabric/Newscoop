B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Menu*>)
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

<?php 
    todefnum('lang');
    todefnum('pub');
?>dnl

<FRAMESET ROWS="70, *" BORDER="0">
    <FRAME SRC="iss.php?lang=<?php  pencURL($lang); ?>&pub=<?php  pencURL($pub); ?>" NAME="fiss" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
    <FRAME SRC="empty.php?bg=0" NAME="f3" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
</FRAMESET>

<?php  } ?>dnl

E_DATABASE
E_HTML
