B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Menu})
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

<!sql setdefault lang 0>dnl
<!sql setdefault pub 0>dnl
<!sql setdefault iss 0>dnl

<FRAMESET ROWS="70, *" BORDER="0">
    <FRAME SRC="sect.xql?lang=<!sql print #lang>&pub=<!sql print #pub>&iss=<!sql print $iss>" NAME="fsect" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
    <FRAME SRC="empty.xql?bg=1" NAME="f4" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
</FRAMESET>

<!sql endif>dnl

E_DATABASE
E_HTML
