B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageClasses})

B_HEAD
	X_EXPIRES
	X_TITLE({Add New Class})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add dictionary classes.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Add New Class})
B_HEADER_BUTTONS
X_HBUTTON({Dictionary Classes}, {classes/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<P>
B_DIALOG({Add new class}, {POST}, {do_add.xql})
	B_DIALOG_INPUT({Name:})
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Language:})
		<!sql query "SELECT Id, Name FROM Languages ORDER BY Name" q>
		<SELECT NAME="cLang">
			<!sql print_rows q "<OPTION VALUE="~q.Id">~q.Name">
		</SELECT>
		<!sql free q>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/classes/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
