B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageUsers})

B_HEAD
	X_EXPIRES
	X_TITLE({Delete IP Group})
<!sql if $access == 0>dnl
		X_AD({You do not have the right to delete IP Groups.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Delete IP Group})
B_HEADER_BUTTONS
X_HBUTTON({IP Access List}, {users/ipaccesslist.xql?User=<!sql print #User>})
X_HBUTTON({Users}, {users/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault User 0>dnl
<!sql setdefault StartIP 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT (StartIP & 0xff000000) >> 24, (StartIP & 0x00ff0000) >> 16, (StartIP & 0x0000ff00) >> 8, StartIP & 0x000000ff, Addresses FROM SubsByIP WHERE IdUser=?User and StartIP=?StartIP" u>dnl
<!sql if $NUM_ROWS>dnl
<P>
B_MSGBOX({Delete IP Group})
	X_MSGBOX_TEXT({<LI>Are you sure you want to delete the IP Group <B><!sql print ~u.0.~u.1.~u.2.~u.3:~u.4></B>?</LI>})
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_ipdel.xql">
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<!sql print ~User>">
		<INPUT TYPE="HIDDEN" NAME="StartIP" VALUE="<!sql print ~StartIP>">
		<INPUT TYPE="IMAGE" NAME="Yes" SRC="X_ROOT/img/button/yes.gif" BORDER="0">
		<A HREF="X_ROOT/users/ipaccesslist.xql?User=<!sql print ?User>"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such IP Group.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
