B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManagePub})

B_HEAD
	X_EXPIRES
	X_TITLE({Delete Subscription Default Time})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to manage publications.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Delete Subscription Default Time})
B_HEADER_BUTTONS
X_HBUTTON({Subscriptions}, {pub/deftime.xql?Pub=<!sql print #Pub>&Language=<!sql print #Language>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault Pub 0>dnl
<!sql setdefault Language 0>dnl
<!sql setdefault CountryCode "">dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Publications WHERE Id=?Pub" p>dnl
<!sql if $NUM_ROWS>dnl
<P>
B_MSGBOX({Delete subscription default time})
	X_MSGBOX_TEXT({<LI>Are you sure you want to delete the subscription default time for <B><!sql print ~p.Name>:<!sql print ~CountryCode></B>?</LI>})
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_deldeftime.xql">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<!sql print ~Pub>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<!sql print ~Language>">
		<INPUT TYPE="HIDDEN" NAME="CountryCode" VALUE="<!sql print ~CountryCode>">
		<INPUT TYPE="IMAGE" NAME="Yes" SRC="X_ROOT/img/button/yes.gif" BORDER="0">
		<A HREF="X_ROOT/pub/deftime.xql?Pub=<!sql print #Pub>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such publication.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
