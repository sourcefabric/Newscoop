B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageUsers})

B_HEAD
	X_EXPIRES
	X_TITLE({Add New IP Group})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add IP groups.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Add New IP Group})
B_HEADER_BUTTONS
X_HBUTTON({IP Access List}, {users/ipaccesslist.xql?User=<!sql print #User>})
X_HBUTTON({Users}, {users/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<P>
B_DIALOG({Add new IP Group}, {POST}, {do_ipadd.xql})
        <INPUT TYPE="HIDDEN" NAME="User" VALUE="<!sql print #User>" SIZE="3" MAXLENGTH="3">.
	B_DIALOG_INPUT({Start IP:})
		<INPUT TYPE="TEXT" NAME="cStartIP1" SIZE="3" MAXLENGTH="3">.
		<INPUT TYPE="TEXT" NAME="cStartIP2" SIZE="3" MAXLENGTH="3">.
		<INPUT TYPE="TEXT" NAME="cStartIP3" SIZE="3" MAXLENGTH="3">.
		<INPUT TYPE="TEXT" NAME="cStartIP4" SIZE="3" MAXLENGTH="3">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Number of addresses:})
		<INPUT TYPE="TEXT" NAME="cAddresses" SIZE="10" MAXLENGTH="10">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/users/ipaccesslist.xql?User=<!sql print #User>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
