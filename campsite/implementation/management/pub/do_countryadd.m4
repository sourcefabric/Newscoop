B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManagePub})

B_HEAD
	X_EXPIRES
	X_TITLE({Adding New Country Default Subscription Time})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to manage publications.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault cPub 0>dnl
<!sql setdefault cCountryCode "">dnl
<!sql setdefault cTrialTime 0>dnl
<!sql setdefault cPaidTime 0>dnl
<!sql setdefault Language 1>dnl
<!sql set correct 1><!sql set created 0>dnl

B_HEADER({Adding New Country Default Subscription Time})
B_HEADER_BUTTONS
X_HBUTTON({Subscriptions}, {pub/deftime.xql?Pub=<!sql print #cPub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Publications WHERE Id=?cPub" q_pub>dnl
<!sql if $NUM_ROWS>dnl
B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
E_CURRENT

<P>
B_MSGBOX({Adding new country default subscription time})
	X_MSGBOX_TEXT({
<!sql query "SELECT TRIM('?cCountryCode')" q_tr>dnl
<!sql if (@q_tr.0 == "" || @q_tr.0 == " ")>dnl
<!sql set correct 0>dnl
		<LI>You must select a country.</LI>
<!sql endif>dnl
<!sql if $correct>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "INSERT IGNORE INTO SubsDefTime SET CountryCode='?cCountryCode', IdPublication='?cPub', TrialTime='?cTrialTime', PaidTime='?cPaidTime'">dnl
<!sql setexpr created ($AFFECTED_ROWS != 0)>dnl
<!sql endif>dnl
<!sql if $created>dnl
		<LI>The default subscription time for <B><!sql print ~q_pub.Name>:<!sql print ~cCountryCode></B> has been successfuly added.</LI>
X_AUDIT({4}, {Default subscription time for ?q_pub.Name:?cCountryCode added})
<!sql else>dnl
<!sql if ($correct != 0)>dnl
		<LI>The default subscription time for country <!sql print ~q_pub.Name>:<!sql print ~cCountryCode> could not be added.</LI><LI>Please check if another entry with the same country code does not already exist.</LI>
<!sql endif>dnl
<!sql endif>dnl
		})
<!sql if $correct && $created>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/countryadd.xql?Pub=<!sql print #cPub>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another country"></A>
		<A HREF="X_ROOT/pub/deftime.xql?Pub=<!sql print #cPub>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<!sql else>
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/countryadd.xql?Pub=<!sql print #cPub>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
	E_MSGBOX_BUTTONS
<!sql endif>dnl
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
