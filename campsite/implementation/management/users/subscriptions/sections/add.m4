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

<!sql setdefault Subs 0>dnl
<!sql setdefault Pub 0>dnl
<!sql setdefault User 0>dnl
B_HEADER({Add New Subscription})
B_HEADER_BUTTONS
X_HBUTTON({Sections}, {users/subscriptions/sections/?User=<!sql print #User>&Pub=<!sql print #Pub>&Subs=<!sql print #Subs>})
X_HBUTTON({Subscriptions}, {users/subscriptions/?User=<!sql print #User>})
X_HBUTTON({Users}, {users/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT UName FROM Users WHERE Id=?User" q_usr>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl

B_CURRENT
X_CURRENT({User account:}, {<B><!sql print ~q_usr.UName></B>})
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
E_CURRENT

<P>
B_DIALOG({Add new subscription}, {POST}, {do_add.xql})

	B_DIALOG_INPUT({Section:})
		<SELECT NAME="cSection">
		<OPTION VALUE=0>All sections</OPTION>
<!sql query "SELECT DISTINCT Number, Name FROM Sections WHERE IdPublication=?Pub" q_sect>dnl
<!sql print_loop q_sect>dnl
		<OPTION VALUE="<!sql print ~q_sect.Number>"><!sql print ~q_sect.Name></OPTION>
<!sql done>dnl
		</SELECT>
	E_DIALOG_INPUT

	B_DIALOG_INPUT({Start:})
		<INPUT TYPE="TEXT" NAME="cStartDate" SIZE="10" VALUE="<!sql ftime "%Y-%m-%d">" MAXLENGTH="10"> (YYYY-MM-DD)
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Days:})
		<INPUT TYPE="TEXT" NAME="cDays" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<!sql print ~User>">
		<INPUT TYPE="HIDDEN" NAME="Subs" VALUE="<!sql print ~Subs>">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<!sql print ~Pub>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/users/subscriptions/sections/?Pub=<!sql print #Pub>&User=<!sql print #User>&Subs=<!sql print #Subs>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
	<tr><td colspan=2 width=250>WARNING: If you subscribe to all sections, the periods for previously added sections will be overriden!</td></tr>
E_DIALOG
<P>

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such publication.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

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
