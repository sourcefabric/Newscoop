B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageClasses})

B_HEAD
	X_EXPIRES
	X_TITLE({Deleting Class})
<!sql if $access == 0>dnl
		X_AD({You do not have the right to delete dictionary classes.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Deleting Class})
B_HEADER_BUTTONS
X_HBUTTON({Dictionary Classes}, {classes/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault Class 0>dnl
<!sql setdefault Lang 0>dnl
<P>
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "DELETE FROM Classes  WHERE Id=?Class AND IdLanguage=?Lang">dnl
<!sql query "SELECT COUNT(*) FROM Classes WHERE Id=?Class" q_cnt>dnl
<!sql if @q_cnt.0 == 0>dnl
<!sql query "DELETE FROM KeywordClasses WHERE IdClasses=?Class">dnl
<!sql endif>dnl
B_MSGBOX({Deleting class})
<!sql if $AFFECTED_ROWS>
	X_MSGBOX_TEXT({<LI>The class has been deleted.</LI>})
X_AUDIT({82}, {Class ~cName deleted})
<!sql else>
	X_MSGBOX_TEXT({<LI>The class could not be deleted.</LI>})
<!sql endif>
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/classes/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
