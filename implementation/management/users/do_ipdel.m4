B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageUsers})

B_HEAD
	X_EXPIRES
	X_TITLE({Deleting IP Group})
<!sql if $access == 0>dnl
		X_AD({You do not have the right to delete IP Groups.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Deleting IP Group})
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
<!sql query "SELECT (StartIP & 0xff000000) >> 24, (StartIP & 0x00ff0000) >> 16, (StartIP & 0x0000ff00) >> 8, StartIP & 0x000000ff, Addresses FROM SubsByIP WHERE IdUser=?User and StartIP=?StartIP" ig>dnl
<!sql set onlyone 0>
<!sql if $NUM_ROWS>dnl
 <!sql query "DELETE FROM SubsByIP WHERE IdUser=?User and StartIP=?StartIP" dq>
 <!sql set del 1>dnl
<!sql else>dnl
 <!sql set del 0>dnl
<!sql endif>dnl
<P>
B_MSGBOX({Deleting IP Group})
<!sql if $del>
X_AUDIT({58}, {IP Group ~ig.0.~ig.1.~ig.2.~ig.3:~ig.4 deleted})
	X_MSGBOX_TEXT({<LI>The IP Group <B><!sql print ~ig.0.~ig.1.~ig.2.~ig.3:~ig.4></B> has been deleted.</LI>})
<!sql else>
	X_MSGBOX_TEXT({<LI>The IP Group <B><!sql print ~ig.0.~ig.1.~ig.2.~ig.3:~ig.4></B> could not be deleted.</LI>})
<!sql endif>
	B_MSGBOX_BUTTONS
<!sql if $del>
		<A HREF="X_ROOT/users/ipaccesslist.xql?User=<!sql print #User>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>
		<A HREF="X_ROOT/users/ipaccesslist.xql?User=<!sql print #User>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No IP Group.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
