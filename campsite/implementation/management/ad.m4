B_HTML
INCLUDE_PHP_LIB(<*.*>)
B_HEAD
	X_EXPIRES
	X_TITLE(<*Access denied*>)
E_HEAD

B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Access denied*>)
X_HEADER_NO_BUTTONS
E_HEADER

<? todef('ADReason',getGS('You do not have the right to access this page.'));?>dnl
B_MSGBOX(<*Access denied*>, <**>, <*red*>)
	X_MSGBOX_TEXT(<*<font color=red><li><? print encHTML($ADReason); ?></li></font>*>)
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/home.php*>)
	E_MSGBOX_BUTTONS
E_DIALOG

X_HR
X_COPYRIGHT
E_BODY

E_HTML


