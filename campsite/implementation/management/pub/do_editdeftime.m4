B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManagePub})

B_HEAD
	X_EXPIRES
	X_TITLE({Changing Default Subscription Time})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to change publication information.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault cPub 0>dnl
<!sql setdefault cCountryCode "">dnl
<!sql setdefault Language 1>dnl
<!sql setdefault cPayeTime 0>dnl
<!sql setdefault cTrialTime 0>dnl
B_HEADER({Changing Default Subscription Time})
B_HEADER_BUTTONS
X_HBUTTON({Subscriptions}, {pub/deftime.xql?Pub=<!sql print #Pub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set created 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Publications WHERE Id=?cPub" q_pub>dnl
<!sql if $NUM_ROWS>dnl

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Countries WHERE Code='?cCountryCode'" q_ctr>dnl
<!sql if $NUM_ROWS>dnl

B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
X_CURRENT({Country:}, {<B><!sql print ~q_ctr.Name></B>})
E_CURRENT

<P>
B_MSGBOX({Changing default subscription time})
	X_MSGBOX_TEXT({
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "UPDATE SubsDefTime SET TrialTime='?cTrialTime', PaidTime='?cPaidTime' WHERE CountryCode='?cCountryCode' AND IdPublication=?cPub">dnl
<!sql setexpr created ($AFFECTED_ROWS != 0)>dnl
<!sql if $created>dnl
		<LI>The default subscription time for <B><!sql print ~q_pub.Name>:<!sql print ~q_ctr.Name></B> has been successfuly updated.</LI>`
X_AUDIT({6}, {Default subscription time for ?q_pub.Name:?cCountryCode changed})
<!sql else>dnl
		<LI>The default subscription time could not be updated.</LI>
<!sql endif>dnl
		})
	B_MSGBOX_BUTTONS
<!sql if $created>dnl
		<A HREF="X_ROOT/pub/deftime.xql?Pub=<!sql print #Pub>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>
		<A HREF="X_ROOT/pub/deftime.xql?Pub=<!sql print #Pub>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such country.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

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
