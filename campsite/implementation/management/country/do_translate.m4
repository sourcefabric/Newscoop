B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageCountries})

B_HEAD
	X_EXPIRES
	X_TITLE({Adding New Translation})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to translate country names.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault cName "">dnl
<!sql setdefault cLanguage 0>dnl
<!sql setdefault Language 0>dnl
<!sql setdefault cCode "">dnl
B_HEADER({Adding New Translation})
B_HEADER_BUTTONS
X_HBUTTON({Countries}, {country/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Countries WHERE Code='?cCode' AND IdLanguage=?Language" q_country>dnl
<!sql if $NUM_ROWS>dnl

<!sql set correct 1><!sql set created 0>dnl
<P>
B_MSGBOX({Adding new translation})
	X_MSGBOX_TEXT({
<!sql query "SELECT TRIM('?cName')" q_var>dnl
<!sql if (@q_var.0 == "" || @q_var.0 == " ")>dnl
<!sql set correct 0>dnl
	<LI>You must complete the <B>Name</B> field.</LI>
<!sql endif>dnl
<!sql if $correct>dnl
<!sql if $cLanguage == ""><!sql set cLanguage 0><!sql endif>dnl
<!sql query "SELECT COUNT(*) FROM Languages WHERE Id=?cLanguage" q_xlang>dnl
<!sql if @q_xlang.0 == 0>dnl
<!sql set correct 0>dnl
	<LI>You must select a language.</LI>
<!sql endif>dnl
<!sql endif>dnl
<!sql if $correct>dnl
	<!sql set AFFECTED_ROWS 0>dnl
	<!sql query "INSERT IGNORE INTO Countries SET Code='?cCode', IdLanguage = ?cLanguage, Name = '?cName'">dnl
	<!sql if $AFFECTED_ROWS>dnl
		<!sql set created 1>dnl
	<!sql endif>
<!sql endif>dnl
<!sql if $correct>dnl
<!sql if $created>dnl
	<LI>The country name <B><!sql print ~q_country.Name></B> has been translated</LI>
X_AUDIT({132}, {Country name ?q_country.Name translated})
<!sql else>dnl
	<LI>The country name <B><!sql print ~cName></B> could not be translated</LI>
<!sql endif>dnl
<!sql endif>dnl
	})
	B_MSGBOX_BUTTONS
<!sql if $created>dnl
		<A HREF="X_ROOT/country/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>dnl
		<A HREF="X_ROOT/country/"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
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
