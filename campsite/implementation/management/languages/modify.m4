B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageLanguages})

B_HEAD
	X_EXPIRES
	X_TITLE({Edit Language})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to edit languages.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Edit Language})
B_HEADER_BUTTONS
X_HBUTTON({Languages}, {languages/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<P>
<!sql query "SELECT * FROM TimeUnits WHERE 1=0" q_tu>
<!sql setdefault Lang 0>dnl
<!sql set NUM_ROWS 0>
<!sql query "SELECT * FROM Languages WHERE Id=?Lang" l>dnl
<!sql if $NUM_ROWS>
B_DIALOG({Edit language}, {POST}, {do_modify.xql})
	B_DIALOG_INPUT({Name:})
		<INPUT TYPE="TEXT" NAME="cName" VALUE="<!sql print ~l.Name>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Native name:})
		<INPUT TYPE="TEXT" NAME="cOrigName" VALUE="<!sql print ~l.OrigName>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Code page:})
		<INPUT TYPE="TEXT" NAME="cCodePage" VALUE="<!sql print ~l.CodePage>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Code:})
		<INPUT TYPE="TEXT" NAME="cCode" VALUE="<!sql print ~l.Code>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	X_DIALOG_TEXT({Please enter the translation for month names.})
	B_DIALOG_INPUT({January:})
		<INPUT TYPE="TEXT" NAME="cMonth1" VALUE="<!sql print ~l.Month1>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({February:})
		<INPUT TYPE="TEXT" NAME="cMonth2" VALUE="<!sql print ~l.Month2>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({March:})
		<INPUT TYPE="TEXT" NAME="cMonth3" VALUE="<!sql print ~l.Month3>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({April:})
		<INPUT TYPE="TEXT" NAME="cMonth4" VALUE="<!sql print ~l.Month4>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({May:})
		<INPUT TYPE="TEXT" NAME="cMonth5" VALUE="<!sql print ~l.Month5>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({June:})
		<INPUT TYPE="TEXT" NAME="cMonth6" VALUE="<!sql print ~l.Month6>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({July:})
		<INPUT TYPE="TEXT" NAME="cMonth7" VALUE="<!sql print ~l.Month7>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({August:})
		<INPUT TYPE="TEXT" NAME="cMonth8" VALUE="<!sql print ~l.Month8>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({September:})
		<INPUT TYPE="TEXT" NAME="cMonth9" VALUE="<!sql print ~l.Month9>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({October:})
		<INPUT TYPE="TEXT" NAME="cMonth10" VALUE="<!sql print ~l.Month10>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({November:})
		<INPUT TYPE="TEXT" NAME="cMonth11" VALUE="<!sql print ~l.Month11>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({December:})
		<INPUT TYPE="TEXT" NAME="cMonth12" VALUE="<!sql print ~l.Month12>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	X_DIALOG_TEXT({Please enter the translation for week day names.})
	B_DIALOG_INPUT({Sunday:})
		<INPUT TYPE="TEXT" NAME="cWDay1" VALUE="<!sql print ~l.WDay1>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Monday:})
		<INPUT TYPE="TEXT" NAME="cWDay2" VALUE="<!sql print ~l.WDay2>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Tuesday:})
		<INPUT TYPE="TEXT" NAME="cWDay3" VALUE="<!sql print ~l.WDay3>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Wednesday:})
		<INPUT TYPE="TEXT" NAME="cWDay4" VALUE="<!sql print ~l.WDay4>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Thursday:})
		<INPUT TYPE="TEXT" NAME="cWDay5" VALUE="<!sql print ~l.WDay5>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Friday:})
		<INPUT TYPE="TEXT" NAME="cWDay6" VALUE="<!sql print ~l.WDay6>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Saturday:})
		<INPUT TYPE="TEXT" NAME="cWDay7" VALUE="<!sql print ~l.WDay7>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT

<!sql query "SELECT * FROM TimeUnits WHERE IdLanguage=?Lang" q_tu>
	X_DIALOG_TEXT({Please enter the translation for time units.})
<!sql if $NUM_ROWS == 0>
	<!sql query "SELECT * FROM TimeUnits where 1=0" q_tu>
	B_DIALOG_INPUT({Years:})
		<INPUT TYPE="TEXT" NAME="cYear" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Months:})
		<INPUT TYPE="TEXT" NAME="cMonth" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Weeks:})
		<INPUT TYPE="TEXT" NAME="cWeek" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Days:})
		<INPUT TYPE="TEXT" NAME="cDay" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
<!sql else>
	<!sql print_loop q_tu>
	<!sql if ~q_tu.0 == "D">
	B_DIALOG_INPUT({Days:})
		<INPUT TYPE="TEXT" NAME="cDay" SIZE="20" MAXLENGTH="20" VALUE="<!sql print ~q_tu.2>">
	E_DIALOG_INPUT
	<!sql endif>
	<!sql if ~q_tu.0 == "W">
	B_DIALOG_INPUT({Weeks:})
		<INPUT TYPE="TEXT" NAME="cWeek" SIZE="20" MAXLENGTH="20" VALUE="<!sql print ~q_tu.2>">
	E_DIALOG_INPUT
	<!sql endif>
	<!sql if ~q_tu.0 == "M">
	B_DIALOG_INPUT({Months:})
		<INPUT TYPE="TEXT" NAME="cMonth" SIZE="20" MAXLENGTH="20" VALUE="<!sql print ~q_tu.2>">
	E_DIALOG_INPUT
	<!sql endif>
	<!sql if ~q_tu.0 == "Y">
	B_DIALOG_INPUT({Years:})
		<INPUT TYPE="TEXT" NAME="cYear" SIZE="20" MAXLENGTH="20" VALUE="<!sql print ~q_tu.2>">
	E_DIALOG_INPUT
	<!sql endif>
	<!sql  done>
<!sql endif>
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Lang" VALUE="<!sql print ~Lang>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/languages/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such language.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
