B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageSubscriptions})

B_HEAD
	X_EXPIRES
	X_TITLE({Change Subscription Status})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to change subscriptions status.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault User 0>dnl
<!sql setdefault Subs 0>dnl
B_HEADER({Change Subscription Status})
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
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Active, IdPublication, ToPay, Currency FROM Subscriptions WHERE Id=?Subs" q_subs>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Publications WHERE Id=?q_subs.IdPublication" q_pub>dnl
<!sql if $NUM_ROWS>dnl

B_CURRENT
X_CURRENT({User account:}, {<B><!sql print ~q_usr.UName></B>})
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
E_CURRENT

<P>
B_DIALOG({Update payement}, {POST}, {do_topay.xql})
        B_DIALOG_INPUT({Left to pay:})
            <INPUT TYPE="TEXT" NAME="cToPay" VALUE="<!sql print ~q_subs.ToPay>" SIZE=10> <!sql print ~q_subs.Currency>
        E_DIALOG_INPUT
        B_DIALOG_BUTTONS
            <INPUT TYPE="HIDDEN" NAME="User" VALUE="<!sql print ~User>">
	    <INPUT TYPE="HIDDEN" NAME="Subs" VALUE="<!sql print ~Subs>">
            <INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
            <A HREF="X_ROOT/users/subscriptions/?User=<!sql print #User>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"
        E_DIALOG_BUTTONS
E_DIALOG
<P>

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such publication.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such subscription.</LI>
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
