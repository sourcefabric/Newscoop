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
    todefnum('iss');
?>dnl

<FRAMESET ROWS="70, *" BORDER="0">
    <FRAME SRC="sect.php?lang=<?php  pencURL($lang); ?>&pub=<?php  pencURL($pub); ?>&iss=<?php  pencURL($iss); ?>" NAME="fsect" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
    <FRAME SRC="empty.php?bg=1" NAME="f4" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
</FRAMESET>

<?php  } ?>dnl

E_DATABASE
E_HTML
