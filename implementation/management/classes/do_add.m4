B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageClasses})

B_HEAD
	X_EXPIRES
	X_TITLE({Adding New Class})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add dictionary classes.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Adding New Class})
B_HEADER_BUTTONS
X_HBUTTON({Dictionary Classes}, {classes/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault cName "">dnl
<!sql setdefault cLang 0>dnl

<!sql set correct 1><!sql set created 0>dnl
<P>
B_MSGBOX({Adding new keyword class})
	X_MSGBOX_TEXT({
<!sql if ($cName == "")>
<!sql set correct 0>
		<LI>You must complete the <B>Name</B> field.</LI>
<!sql endif>dnl
<!sql if ($cLang == 0)>
<!sql set correct 0>
		<LI>You must select a language.</LI>
<!sql endif>dnl
<!sql set NUM_ROWS 0>dnl
<!sql if $correct>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "UPDATE AutoId SET ClassId=LAST_INSERT_ID(ClassId + 1)">dnl
<!sqi if $AFFECTED_ROWS>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "INSERT IGNORE INTO Classes SET Id=LAST_INSERT_ID(), IdLanguage='?cLang', Name='?cName'">dnl
<!sql setexpr created ($AFFECTED_ROWS != 0)>dnl
<!sql endif>dnl
<!sql endif>dnl
<!sql if $created>dnl
		<LI>The class <B><!sql print ~cName></B> has been added.</LI>
X_AUDIT({81}, {Class ~cName added})
<!sql else>dnl
<!sql if ($correct != 0)>dnl
		<LI>The class could not be added.<LI></LI>Please check if the class does not already exist.</LI>
<!sql endif>dnl
<!sql endif>dnl
		})
<!sql if $correct && $created>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/classes/add.xql"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another class"></A>
		<A HREF="X_ROOT/classes/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<!sql else>
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/classes/add.xql"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
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
