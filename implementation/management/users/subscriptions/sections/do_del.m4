B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageSubscriptions})

B_HEAD
	X_EXPIRES
	X_TITLE({Deleting Subscription})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to delete subscriptions.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Subs 0>dnl
<!sql setdefault Sect 0>dnl
<!sql setdefault Pub 0>dnl
<!sql setdefault User 0>dnl
B_HEADER({Deleting Subscription})
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
<!sql query "SELECT SectionNumber FROM SubsSections WHERE IdSubscription=?Subs" q_ssubs>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl

B_CURRENT
X_CURRENT({User account:}, {<B><!sql print ~q_usr.UName></B>})
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
E_CURRENT

<P>
B_MSGBOX({Deleting subscription})
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "DELETE FROM SubsSections WHERE IdSubscription=?Subs AND SectionNumber=?Sect">dnl
<!sql if $AFFECTED_ROWS>dnl
	X_MSGBOX_TEXT({<LI>The subscription to the section <B><!sql print ~q_ssubs.SectionNumber></B> has been deleted.</LI>})
<!sql else>dnl
	X_MSGBOX_TEXT({<LI>The subscription to the section <B><!sql print ~q_ssubs.SectionNumber></B> could not be deleted.</LI>})
<!sql endif>dnl
	B_MSGBOX_BUTTONS
<!sql if $AFFECTED_ROWS>dnl
		<A HREF="X_ROOT/users/subscriptions/sections/?Pub=<!sql print #Pub>&User=<!sql print #User>&Subs=<!sql print #Subs>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>dnl
		<A HREF="X_ROOT/users/subscriptions/sections/del.xql?Pub=<!sql print #Pub>&User=<!sql print #User>&Subs=<!sql print #Subs>&Sec=<!sql print #Sect>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
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
