B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageClasses})

B_HEAD
	X_EXPIRES
	X_TITLE({Delete Class})
<!sql if $access == 0>dnl
		X_AD({You do not have the right to delete dictionary classes.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Delete Class})
B_HEADER_BUTTONS
X_HBUTTON({Dictionary Classes}, {classes/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault Class 0>dnl
<!sql setdefault Lang 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Classes WHERE Id=?Class AND IdLanguage=?Lang" c>dnl
<P>
<!sql if $NUM_ROWS>dnl
B_MSGBOX({Delete class})
	X_MSGBOX_TEXT({<LI>Are you sure you want to delete the class <B><!sql print ~c.Name></B>?</LI>})
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.xql">
		<INPUT TYPE="HIDDEN" NAME="Class" VALUE="<!sql print ~Class>">
		<INPUT TYPE="HIDDEN" NAME="Lang" VALUE="<!sql print ~Lang>">
		<INPUT TYPE="IMAGE" NAME="Yes" SRC="X_ROOT/img/button/yes.gif" BORDER="0">
		<A HREF="X_ROOT/classes/"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<!sql else>dnl
	<LI>No such class.</LI>
<!sql endif>dnl
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
