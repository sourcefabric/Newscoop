B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({DeleteUsers})

B_HEAD
	X_EXPIRES
	X_TITLE({Deleting User Account})
<!sql if $access == 0>dnl
		X_AD({You do not have the right to delete user accounts.})
<!sql endif>dnl
<!sql query "SELECT Id FROM Subscriptions WHERE 1=0" s>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Deleting User Account})
B_HEADER_BUTTONS
X_HBUTTON({Users}, {users/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault User 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT UName FROM Users WHERE Id=?User" uu>dnl
<!sql if $NUM_ROWS>dnl
<!sql set del 1>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "DELETE FROM Users WHERE Id=?User">dnl
<!sql if $AFFECTED_ROWS>dnl
<!sql query "DELETE FROM UserPerm WHERE IdUser=?User">dnl
<!sql query "SELECT Id FROM Subscriptions WHERE IdUser=?User" s>dnl
<!sql print_loop s>dnl
<!sql query "DELETE FROM SubsSections WHERE IdSubscription=?s.0">dnl
<!sql done>dnl
<!sql query "DELETE FROM Subscriptions WHERE IdUser=?User">dnl
<!sql query "DELETE FROM SubsByIP WHERE IdUser=?User">dnl
<!sql else>dnl
<!sql set del 0>dnl
<!sql endif>dnl
<P>
B_MSGBOX({Deleting user account})
<!sql if $del>
X_AUDIT({52}, {User account ~uu.0 deleted})
	X_MSGBOX_TEXT({<LI>The user account <B><!sql print ~uu.0></B> has been deleted.</LI>})
<!sql else>
	X_MSGBOX_TEXT({<LI>The user account <B><!sql print ~uu.0></B> could not be deleted.</LI>})
<!sql endif>
	B_MSGBOX_BUTTONS
<!sql if $del>
		<A HREF="X_ROOT/users/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>
		<A HREF="X_ROOT/users/"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such user.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
