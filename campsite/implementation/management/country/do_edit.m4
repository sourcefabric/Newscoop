B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageCountries})

B_HEAD
	X_EXPIRES
	X_TITLE({Changing country name})
<!sql if $access == 0>dnl
	    X_AD({You do not have the right to change country names.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault cName "">dnl
<!sql setdefault Language 0>dnl
<!sql setdefault Code "">dnl
B_HEADER({Changing country name})
B_HEADER_BUTTONS
X_HBUTTON({Countries}, {country/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Countries WHERE IdLanguage=?Language AND Code='?Code'" q_country>dnl
<!sql if $NUM_ROWS>dnl

<!sql set correct 1>dnl
<P>
B_MSGBOX({Changing country name})
	X_MSGBOX_TEXT({
<!sql query "SELECT TRIM('?cName')" q_var>dnl
<!sql if (@q_var.0 == "" || @q_var.0 == " ")>dnl
<!sql set correct 0>dnl
	<LI>You must complete the <B>Name</B> field.</LI>
<!sql endif>dnl
<!sql if $correct>dnl
<!sql query "SELECT COUNT(*) FROM Countries WHERE Name = '?cName' AND IdLanguage = ?Language" q_cnt>dnl
<!sql if $q_cnt.0 == 0>dnl
<!sql set AFFECTED_ROWS 0>dnl
	<!sql query "UPDATE Countries SET Name = '?cName' WHERE Code='?Code' AND IdLanguage = ?Language">dnl
<!sql else>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql endif>dnl
<!sql if $AFFECTED_ROWS>dnl
	<LI>The country name <B><!sql print ~cName></B> has been changed</LI>
X_AUDIT({133}, {Country name ?cName changed})
<!sql else>dnl
	<LI>The country name <B><!sql print ~cName></B> could not be changed</LI>
<!sql endif>dnl
<!sql endif>dnl
	})
	B_MSGBOX_BUTTONS
<!sql if $AFFECTED_ROWS>dnl
		<A HREF="X_ROOT/country/"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="No"></A>
<!sql else>dnl
		<A HREF="X_ROOT/country/edit.xql?Code=<!sql print #Code>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such country name.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
