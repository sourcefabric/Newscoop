B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManagePub})

B_HEAD
	X_EXPIRES
	X_TITLE({Change Subscription Default Time})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to edit publication information.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>dnl
<!sql setdefault Language 0>dnl
<!sql setdefault CountryCode "">dnl
B_HEADER({Change Subscription Default Time})
B_HEADER_BUTTONS
X_HBUTTON({Subscriptions}, {pub/deftime.xql?Pub=<!sql print #Pub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Countries WHERE Code='?CountryCode' AND IdLanguage=?Language" q_ctr>dnl
<!sql if $NUM_ROWS>dnl

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM SubsDefTime WHERE CountryCode='~CountryCode' AND IdPublication=~Pub" q_deft>
<!sql if $NUM_ROWS>dnl

B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
X_CURRENT({Country:}, {<B><!sql print ~q_ctr.Name></B>})
E_CURRENT

<P>
B_DIALOG({Change subscription default time}, {POST}, {do_editdeftime.xql})
	<INPUT TYPE=HIDDEN NAME=cPub VALUE="<!sql print #Pub>">
	<INPUT TYPE=HIDDEN NAME=cCountryCode VALUE="<!sql print #CountryCode>">
	<INPUT TYPE=HIDDEN NAME=Language VALUE="<!sql print #Language>">
	B_DIALOG_INPUT({Trial time:})
		<INPUT TYPE="TEXT" NAME="cTrialTime" VALUE="<!sql print ~q_deft.TrialTime>" SIZE="5" MAXLENGTH="5"> days
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Paid time:})
		<INPUT TYPE="TEXT" NAME="cPaidTime" VALUE="<!sql print ~q_deft.PaidTime>" SIZE="5" MAXLENGTH="5"> days
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<!sql print ~Pub>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/pub/deftime.xql?Pub=<!sql print #Pub>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No default time entry for that country.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

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
