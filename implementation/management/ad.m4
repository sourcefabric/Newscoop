INCLUDE_PHP_LIB(<*$ADMIN_DIR*>)

<?php  todef('ADReason',getGS('You do not have the right to access this page.'));?>dnl
B_MSGBOX(<*Access denied*>, <**>, <*red*>)
	X_MSGBOX_TEXT(<*<font color=red><li><?php  print encHTML($ADReason); ?></li></font>*>)
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/home.php*>)
	E_MSGBOX_BUTTONS
E_DIALOG

X_HR
X_COPYRIGHT
E_BODY

E_HTML


