B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Changing Your Password})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Changing Your Password})
B_HEADER_BUTTONS
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set ok 1>dnl
<P>
B_MSGBOX({Changing your password})
	X_MSGBOX_TEXT({
<!sql query "SELECT COUNT(*) FROM Users WHERE Id=@Usr.Id AND Password=password('?cOldPass')" urec>dnl
<!sql if @urec.0 == 0>dnl
	<LI>The password you typed is incorrect.</LI>
<!sql set ok 0>dnl
<!sql endif>dnl
<!sql if $ok>dnl
<!sql query "SELECT (STRCMP('?cNewPass1', '?cNewPass2') = 0 && LENGTH('?cNewPass1') >= 6)" pass_ok>dnl
<!sql if @pass_ok.0 == 0>dnl
	<LI>The password must be at least 6 characters long and both passwords should match.</LI>
<!sql set ok 0>dnl
<!sql endif>dnl	
<!sql endif>dnl
<!sql if $ok>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "UPDATE Users SET Password=password('?cNewPass1') WHERE Id=@Usr.Id">dnl
<!sql if $AFFECTED_ROWS == 0>dnl
	<LI>The password could not be changed.</LI>
<!sql set ok 0>dnl
<!sql else>dnl
X_AUDIT({53}, {User @Usr.UName changed his password})
	<LI>The password has been changed successfully.</LI>
<!sql endif>dnl
<!sql endif>dnl
	})
	B_MSGBOX_BUTTONS
<!sql if $ok>dnl
		<A HREF="X_ROOT/home.xql"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>dnl
		<A HREF="X_ROOT/users/chpwd.xql"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
