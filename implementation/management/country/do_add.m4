B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageCountries})

B_HEAD
	X_EXPIRES
	X_TITLE({Adding New Country})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add countries.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Language 0>dnl
B_HEADER({Adding New Country})
B_HEADER_BUTTONS
X_HBUTTON({Countries}, {country/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault cCode "">dnl
<!sql setdefault cName "">dnl
<!sql setdefault cLanguage 0>dnl
<!sql set correct 1><!sql set created 0>dnl
<P>
B_MSGBOX({Adding new country})
	X_MSGBOX_TEXT({
<!sql query "SELECT TRIM('?cCode'), TRIM('?cName'), '?cLanguage'" q_var>dnl
<!sql if (@q_var.0 == "" || @q_var.0 == " ")>dnl
<!sql set correct 0>dnl
	<LI>You must complete the <B>Code</B> field.</LI>
<!sql endif>dnl
<!sql if (@q_var.1 == "" || @q_var.1 == " ")>dnl
<!sql set correct 0>dnl
	<LI>You must complete the <B>Name</B> field.</LI>
<!sql endif>dnl
<!sql if (@q_var.2 == "" || @q_var.2 == "0")>dnl
<!sql set correct 0>dnl
	<LI>You must select a language.</LI>
<!sql endif>dnl
<!sql if $correct>dnl
	<!sql set AFFECTED_ROWS 0>dnl
	<!sql query "INSERT IGNORE INTO Countries SET Code='?cCode', Name='?cName', IdLanguage=?cLanguage">dnl
	<!sql if $AFFECTED_ROWS>dnl
		<!sql set created 1>dnl
	<!sql endif>
<!sql endif>dnl
<!sql if $correct>dnl
<!sql if $created>dnl
	<LI>The country <B><!sql print ~cName></B> has been created</LI>
X_AUDIT({131}, {Country ?cName added})
<!sql else>dnl
	<LI>The country <B><!sql print ~cName></B> could not be created</LI>
<!sql endif>dnl
<!sql endif>dnl
	})
	B_MSGBOX_BUTTONS
<!sql setdefault Back "">dnl
<!sql if $created>dnl
		<A HREF="X_ROOT/country/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>dnl
		<A HREF="X_ROOT/country/add.xql<!sql if $Back != "">?Back=<!sql print #Back><!sql endif>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
