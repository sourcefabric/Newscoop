B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageLanguages})

B_HEAD
	X_EXPIRES
	X_TITLE({Updating Language Information})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to edit languages.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Updating Language Information})
B_HEADER_BUTTONS
X_HBUTTON({Languages}, {languages/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault Lang 0>dnl
<!sql setdefault cName "">dnl
<!sql setdefault cCodePage "">dnl
<!sql setdefault cCode "">dnl
<!sql setdefault cOrigName "">dnl
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
<!sql setdefault updated 0>dnl

<P>
B_MSGBOX({Updating language information})
<!sql query "SELECT COUNT(*) FROM Languages WHERE Id != ?Lang AND Name='?cName'" c>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql if @c.0 == 0>dnl
<!sql if $cName != "">dnl
<!sql if $cCodePage != "">dnl
<!sql query "UPDATE Languages SET Name='?cName', CodePage='?cCodePage', Code='?cCode', OrigName='?cOrigName', Month1='?cMonth1', Month2='?cMonth2', Month3='?cMonth3', Month4='?cMonth4', Month5='?cMonth5', Month6='?cMonth6', Month7='?cMonth7', Month8='?cMonth8', Month9='?cMonth9', Month10='?cMonth10', Month11='?cMonth11', Month12='?cMonth12', WDay1='?cWDay1', WDay2='?cWDay2', WDay3='?cWDay3', WDay4='?cWDay4', WDay5='?cWDay5', WDay6='?cWDay6', WDay7='?cWDay7' WHERE Id=?Lang">dnl
<!sql endif>dnl
<!sql endif>dnl
<!sql endif>dnl
<!sql if $AFFECTED_ROWS><!sql set updated 1><!sql endif>dnl

<!sql query "UPDATE TimeUnits SET Name='?cDay' WHERE Unit='D' AND IdLanguage=?Lang">
<!sql if $AFFECTED_ROWS><!sql set updated 1><!sql endif>dnl 
<!sql query "UPDATE TimeUnits SET Name='?cWeek' WHERE Unit='W' AND IdLanguage=?Lang">
<!sql if $AFFECTED_ROWS><!sql set updated 1><!sql endif>dnl 
<!sql query "UPDATE TimeUnits SET Name='?cMonth' WHERE Unit='M' AND IdLanguage=?Lang">
<!sql if $AFFECTED_ROWS><!sql set updated 1><!sql endif>dnl 
<!sql query "UPDATE TimeUnits SET Name='?cYear' WHERE Unit='Y' AND IdLanguage=?Lang">
<!sql if $AFFECTED_ROWS><!sql set updated 1><!sql endif>dnl 

<!sql if ?updated>
	X_MSGBOX_TEXT({<LI>Language information has been successfuly updated.</LI>})
X_AUDIT({103}, {Language ~cName modified})
<!sql else>dnl
	X_MSGBOX_TEXT({<LI>Language information could not be updated.</LI>
<!sql if ($cName == "")>dnl
	<LI>You must complete the <B>Name</B> field.</LI>
<!sql endif>dnl
<!sql if ($cCodePage == "")>dnl
	<LI>You must complete the <B>Code page</B> field.</LI>
<!sql endif>dnl
<!sql if @c.0>dnl
	<LI>A language with the same name already exists.</LI>
<!sql endif>dnl
	})
<!sql endif>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/languages/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
