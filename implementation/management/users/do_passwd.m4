B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageUsers})

B_HEAD
	X_EXPIRES
	X_TITLE({Changing User Password})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to change user passwords.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Changing User Password})
B_HEADER_BUTTONS
X_HBUTTON({Users}, {users/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault User 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT UName FROM Users WHERE Id=?User" users>dnl
<!sql if $NUM_ROWS>dnl

B_CURRENT
X_CURRENT({User account:}, {<B><!sql print ~users.UName></B>})
E_CURRENT

<!sql set ok 1>dnl
<P>
B_MSGBOX({Changing user password})
	X_MSGBOX_TEXT({
<!sql query "SELECT (STRCMP('?cPass1', '?cPass2') = 0 && LENGTH('?cPass1') >= 6)" pass_ok>dnl
<!sql if @pass_ok.0 == 0>dnl
	<LI>The password must be at least 6 characters long and both passwords should match.</LI>
<!sql set ok 0>dnl
<!sql endif>dnl	
<!sql if $ok>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "UPDATE Users SET Password=password('?cPass1') WHERE Id=?User">dnl
<!sql if $AFFECTED_ROWS == 0>dnl
	<LI>The password could not be changed.</LI>
X_AUDIT({54}, {Password changed for ~users.UName})
<!sql set ok 0>dnl
<!sql else>dnl
	<LI>The password has been changed.</LI>
<!sql endif>dnl
<!sql endif>dnl
	})
	B_MSGBOX_BUTTONS
<!sql if $ok>dnl
		<A HREF="X_ROOT/users/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>dnl
		<A HREF="X_ROOT/users/"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such user account.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
