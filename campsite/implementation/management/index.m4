B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE({CAMPSITE})
<!sql if $access == 0>dnl
	X_REFRESH({0; URL=X_ROOT/login.xql})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
<FRAMESET COLS="12%, *" BORDER="0">
    <FRAME SRC="menu.xql" NAME="fmenu" FRAMEBORDER="0" MARGINWIDTH="0" SCROLLING="NO">
    <FRAME SRC="home.xql" NAME="fmain" FRAMEBORDER="0" MARGINWIDTH="0" SCROLLING="AUTO">
</FRAMESET>
<!sql endif>dnl

E_DATABASE
E_HTML
