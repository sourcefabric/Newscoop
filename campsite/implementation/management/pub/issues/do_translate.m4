B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageIssue})

B_HEAD
	X_EXPIRES
	X_TITLE({Adding New Translation})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add issues.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault cName "">dnl
<!sql setdefault cNumber 0>dnl
<!sql setdefault cLang 0>dnl
<!sql setdefault cPub 0>dnl
B_HEADER({Adding New Translation})
B_HEADER_BUTTONS
X_HBUTTON({Issues}, {pub/issues/?Pub=<!sql print #cPub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set correct 1><!sql set created 0>dnl
<P>
B_MSGBOX({Adding new translation})
	X_MSGBOX_TEXT({
<!sql query "SELECT TRIM('?cName'), TRIM('?cNumber')" q_x>dnl
<!sql if ($cLang = 0)>dnl
<!sql set correct 0>dnl
		<LI>You must select a language.</LI>
<!sql endif>dnl
<!sql if (@q_x.0 == "" || @q_x.0 == " ")>dnl
<!sql set correct 0>dnl
		<LI>You must complete the <B>Name</B> field.</LI>
<!sql endif>dnl
<!sql if (@q_x.1 == "" || @q_x.1 == " ")>dnl
<!sql set correct 0>dnl
		<LI>You must complete the <B>Number</B> field.</LI>
<!sql endif>dnl
<!sql if $correct>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "INSERT IGNORE INTO Issues SET Name='?q_x.0', IdPublication=?cPub, IdLanguage=?cLang, Number=?q_x.1">dnl
<!sql setexpr created ($AFFECTED_ROWS != 0)>dnl
<!sql endif>dnl
<!sql if $created>dnl
		<LI>The issue <B><!sql print ~cName></B> has been successfuly added.</LI>
X_AUDIT({11}, {Issue ?cName added})
<!sql else>dnl
<!sql if ($correct != 0)>dnl
		<LI>The issue could not be added.</LI><LI>Please check if another issue with the same number/language does not already exist.</LI>
<!sql endif>dnl
<!sql endif>dnl
		})
<!sql if $correct && $created>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/issues/translate.xql?Pub=<!sql print #cPub>&Issue=<!sql print #cNumber>"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another issue"></A>
		<A HREF="X_ROOT/pub/issues/?Pub=<!sql print #cPub>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<!sql else>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/issues/translate.xql?Pub=<!sql print #cPub>&Issue=<!sql print #cNumber>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
	E_MSGBOX_BUTTONS
<!sql endif>dnl
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
