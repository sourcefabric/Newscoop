B_HTML
INCLUDE_PHP_LIB(<*.*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE(<*CAMPSITE*>)
<? if ($access==0) { ?>dnl
	X_REFRESH(<*0; URL=X_ROOT/login.php*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
<FRAMESET COLS="12%, *" BORDER="0">
    <FRAME SRC="menu.php" NAME="fmenu" FRAMEBORDER="0" MARGINWIDTH="0" SCROLLING="NO">
    <FRAME SRC="home.php" NAME="fmain" FRAMEBORDER="0" MARGINWIDTH="0" SCROLLING="AUTO">
</FRAMESET>
<? } ?>dnl

E_DATABASE
E_HTML
