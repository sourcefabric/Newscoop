B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageUserTypes})

B_HEAD
	X_EXPIRES
	X_TITLE({Delete User Type})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to delete user types.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Delete User Type})
B_HEADER_BUTTONS
X_HBUTTON({User Types}, {u_types/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault UType "">dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM UserTypes WHERE Name='?UType'" u>dnl
<P>
<!sql if $NUM_ROWS>dnl
B_MSGBOX({Delete user type})
	X_MSGBOX_TEXT({<LI>Are you sure you want to delete the user type <B><!sql print ~u.Name></B>?</LI>})
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.xql">
		<INPUT TYPE="HIDDEN" NAME="UType" VALUE="<!sql print ~u.Name>">
		<INPUT TYPE="IMAGE" SRC="X_ROOT/img/button/yes.gif" BORDER="0"></A>
		<A HREF="X_ROOT/u_types/"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0"></A>
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<!sql else>dnl
	<LI>No such user type.</LI>
<!sql endif>dnl
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
