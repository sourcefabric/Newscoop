B_HTML
INCLUDE_PHP_LIB(<*.*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE(<*CAMPSITE*>)
<?php  if ($access==0) { ?>dnl
	X_REFRESH(<*0; URL=X_ROOT/login.php*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
<FRAMESET COLS="150,*" BORDER="0">
    <FRAME SRC="menu.php" NAME="fmenu" FRAMEBORDER="0" MARGINWIDTH="0" SCROLLING="AUTO">
    <FRAME SRC="home.php" NAME="fmain" FRAMEBORDER="0" MARGINWIDTH="0" SCROLLING="AUTO">
</FRAMESET>
<?php  } ?>dnl

E_DATABASE
E_HTML
