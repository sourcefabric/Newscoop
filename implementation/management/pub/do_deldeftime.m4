B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManagePub})

B_HEAD
	X_EXPIRES
	X_TITLE({Deleting Subscription Default Time})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to manage publications.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Deleting Subscription Default Time})
B_HEADER_BUTTONS
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault Pub 0>dnl
<!sql setdefault Language 0>dnl
<!sql setdefault CountryCode "">dnl
<!sql setdefault del 1>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl
<P>
B_MSGBOX({Deleting subscription default time})
	X_MSGBOX_TEXT({
<!sql set AFFECTED_ROWS 0>dnl
<!sql if $del>dnl
<!sql query "DELETE FROM SubsDefTime WHERE CountryCode='?CountryCode' AND IdPublication=?Pub">dnl
<!sql endif>dnl
<!sql if $AFFECTED_ROWS>dnl
	<LI>The subscription default time for <B><!sql print ~q_pub.Name>:<!sql print ~CountryCode></B> has been deleted.</LI>
X_AUDIT({5}, {Subscription default time for ?q_pub.Name:?CountryCode deleted})
<!sql else>dnl
	<LI>The default subscription time for <B><!sql print ~q_pub.Name>:<!sql print ~CountryCode></B> could not be deleted.</LI>
<!sql endif>dnl
	})
	B_MSGBOX_BUTTONS
<!sql if $AFFECTED_ROWS>dnl
		<A HREF="X_ROOT/pub/deftime.xql?Pub=<!sql print #Pub>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>dnl
		<A HREF="X_ROOT/pub/deftime.xql?Pub=<!sql print #Pub>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such publication.</LI>
</BLOCKQUOTE>
<!sql endif>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
