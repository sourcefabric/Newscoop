B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManagePub})

B_HEAD
	X_EXPIRES
	X_TITLE({Add New Country Default Subscription Time})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to manage publications.})
<!sql endif>dnl
<!sql query "SELECT Code, Name FROM Countries WHERE 1=0" q_ctr>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql set default Language 1>
<!sql set default Pub 0>
B_HEADER({Add New Country Default Subscription Time})
B_HEADER_BUTTONS
X_HBUTTON({Subscriptions}, {pub/deftime.xql?Pub=<!sql print #Pub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl
B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
E_CURRENT
 
<P>
B_DIALOG({Add new country default subscription time}, {POST}, {do_countryadd.xql})
	<INPUT TYPE=HIDDEN NAME=cPub VALUE="<!sql print ~Pub>">
	B_DIALOG_INPUT({Country:})
	    <SELECT NAME="cCountryCode">
<!sql query "SELECT Code, Name FROM Countries WHERE IdLanguage = ?Language" q_ctr>dnl
<!sql print_loop q_ctr>
<!sql set $NUM_ROWS 0>
<!sql query "SELECT * FROM SubsDefTime WHERE CountryCode = '?q_ctr.Code' AND IdPublication=?Pub" q_subs>
<!sql if $NUM_ROWS == 0>
	    <OPTION VALUE="<!sql print ~q_ctr.Code>"><!sql print ~q_ctr.Name>dnl
<!sql endif>dnl
<!sql done>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Trial Time:})
		<INPUT TYPE="TEXT" NAME="cTrialTime" VALUE="1" SIZE="5" MAXLENGTH="5"> days
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Paid Time:})
		<INPUT TYPE="TEXT" NAME="cPaidTime" VALUE="1" SIZE="5" MAXLENGTH="5"> days
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/pub/deftime.xql?Pub=<!sql print #Pub>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
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
