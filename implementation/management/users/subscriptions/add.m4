B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageSubscriptions})

B_HEAD
	X_EXPIRES
	X_TITLE({Add New Subscription})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add subscriptions.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault User 0>dnl
B_HEADER({Add New Subscription})
B_HEADER_BUTTONS
X_HBUTTON({Subscriptions}, {users/subscriptions/?User=<!sql print #User>})
X_HBUTTON({Users}, {users/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT UName FROM Users WHERE Id=?User" q_usr>dnl
<!sql if $NUM_ROWS>dnl

B_CURRENT
X_CURRENT({User account:}, {<B><!sql print ~q_usr.UName></B>})
E_CURRENT

<P>
B_DIALOG({Add new subscription}, {POST}, {do_add.xql})
	B_DIALOG_INPUT({Publication:})
<!sql query "SELECT Id, Name FROM Publications ORDER BY Name" q_pub>dnl
		<SELECT NAME="cPub">
		<!sql print_rows q_pub "<OPTION VALUE=\"~q_pub.Id\">~q_pub.Name">
		</SELECT>
<!sql free q_pub>dnl
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cActive">})
		Active
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<!sql print ~User>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/users/subscriptions/?User=<!sql print #User>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
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
