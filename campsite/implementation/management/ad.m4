B_HTML

B_HEAD
	X_EXPIRES
	X_TITLE({Access denied})
E_HEAD

B_STYLE
E_STYLE

B_BODY

B_HEADER({Access denied})
X_HEADER_NO_BUTTONS
E_HEADER

<!sql setdefault ADReason "You do not have the right to access this page.">dnl
B_MSGBOX({Access denied!})
	X_MSGBOX_TEXT({<LI><!sql print ~ADReason></LI>})
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/home.xql"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
	E_MSGBOX_BUTTONS
E_DIALOG

X_HR
X_COPYRIGHT
E_BODY

E_HTML
