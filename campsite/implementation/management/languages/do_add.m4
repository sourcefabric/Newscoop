B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageLanguages})

B_HEAD
	X_EXPIRES
	X_TITLE({Adding New Language})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add new languages.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Adding New Language})
B_HEADER_BUTTONS
X_HBUTTON({Languages}, {languages/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault cName "">dnl
<!sql setdefault cCodePage "">dnl
<!sql setdefault cOrigName "">dnl
<!sql setdefault cCode "">dnl
<!sql setdefault cMonth1 "">dnl
<!sql setdefault cMonth2 "">dnl
<!sql setdefault cMonth3 "">dnl
<!sql setdefault cMonth4 "">dnl
<!sql setdefault cMonth5 "">dnl
<!sql setdefault cMonth6 "">dnl
<!sql setdefault cMonth7 "">dnl
<!sql setdefault cMonth8 "">dnl
<!sql setdefault cMonth9 "">dnl
<!sql setdefault cMonth10 "">dnl
<!sql setdefault cMonth11 "">dnl
<!sql setdefault cMonth12 "">dnl
<!sql setdefault cWDay1 "">dnl
<!sql setdefault cWDay2 "">dnl
<!sql setdefault cWDay3 "">dnl
<!sql setdefault cWDay4 "">dnl
<!sql setdefault cWDay5 "">dnl
<!sql setdefault cWDay6 "">dnl
<!sql setdefault cWDay7 "">dnl
<!sql setdefault cDay "">dnl
<!sql setdefault cWeek "">dnl
<!sql setdefault cMonth "">dnl
<!sql setdefault cYear "">dnl


<!sql set correct 1><!sql set created 0>dnl
<P>
B_MSGBOX({Adding new language})
	X_MSGBOX_TEXT({
<!sql if ($cName == "")>dnl
<!sql set correct 0>dnl
		<LI>You must complete the <B>Name</B> field.</LI>
<!sql endif>dnl
<!sql if ($cOrigName == "")>dnl
<!sql set correct 0>dnl
		<LI>You must complete the <B>Native name</B> field.</LI>
<!sql endif>dnl
<!sql if ($cCodePage == "")>dnl
<!sql set correct 0>dnl
		<LI>You must complete the <B>Code page</B> field.</LI>
<!sql endif>dnl
<!sql if $correct>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "INSERT IGNORE INTO Languages SET Name='?cName', CodePage='?cCodePage', Code='?cCode', OrigName='?cOrigName', Month1='?cMonth1', Month2='?cMonth2', Month3='?cMonth3', Month4='?cMonth4', Month5='?cMonth5', Month6='?cMonth6', Month7='?cMonth7', Month8='?cMonth8', Month9='?cMonth9', Month10='?cMonth10', Month11='?cMonth11', Month12='?cMonth12', WDay1='?cWDay1', WDay2='?cWDay2', WDay3='?cWDay3', WDay4='?cWDay4', WDay5='?cWDay5', WDay6='?cWDay6', WDay7='?cWDay7'">dnl
<!sql setexpr created ($AFFECTED_ROWS != 0)>dnl
<!sql endif>dnl

<!sql if $created>
	<!sql query "SELECT LAST_INSERT_ID()" lgid>
	<!sql query "INSERT IGNORE INTO TimeUnits VALUES('D', ?lgid.0,'?cDay')">
			<!sql setexpr created ($AFFECTED_ROWS != 0)>dnl
	<!sql query "INSERT IGNORE INTO TimeUnits VALUES('W', ?lgid.0, '?cWeek')">
		<!sql setexpr created ($AFFECTED_ROWS != 0)>dnl
	<!sql query "INSERT IGNORE INTO TimeUnits VALUES('M', ?lgid.0, '?cMonth')">
		<!sql setexpr created ($AFFECTED_ROWS != 0)>dnl
	<!sql query "INSERT IGNORE INTO TimeUnits VALUES('Y', ?lgid.0, '?cYear')">
		<!sql setexpr created ($AFFECTED_ROWS != 0)>dnl
<!sql endif>

<!sql if $created>dnl
		<LI>The language <B><!sql print ~cName></B> has been successfuly added.</LI>
X_AUDIT({101}, {Language ~cName added})
<!sql else>dnl
<!sql if ($correct != 0)>dnl
		<LI>The language could not be added.</LI><LI>Please check if a language with the same name does not already exist.</LI>
<!sql endif>dnl
<!sql endif>dnl
		})
	B_MSGBOX_BUTTONS
<!sql setdefault Back "">dnl
<!sql if $correct && $created>dnl
		<A HREF="X_ROOT/languages/add.xql<!sql if $Back>?Back=<!sql print #Back><!sql endif>"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another language"></A>
		<A HREF="X_ROOT/languages/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>
		<A HREF="X_ROOT/languages/add.xql<!sql if $Back>?Back=<!sql print #Back><!sql endif>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
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
