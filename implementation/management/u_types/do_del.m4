B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageUserTypes})

B_HEAD
	X_EXPIRES
	X_TITLE({Deleting User Type})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to delete user types.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Deleting User Type})
B_HEADER_BUTTONS
X_HBUTTON({User Types}, {u_types/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault UType "">dnl

<P>
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "DELETE FROM UserTypes WHERE Name='?UType'">dnl
B_MSGBOX({Deleting user type})
<!sql if $AFFECTED_ROWS>
	X_MSGBOX_TEXT({<LI>The user type has been deleted.</LI>})
X_AUDIT({122}, {User type ~UType deleted})
<!sql else>
	X_MSGBOX_TEXT({<LI>The user type could not be deleted.</LI>})
<!sql endif>
	B_MSGBOX_BUTTONS
<!sql if $AFFECTED_ROWS>
		<A HREF="X_ROOT/u_types/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>
		<A HREF="X_ROOT/u_types/"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
