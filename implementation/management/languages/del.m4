B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({DeleteLanguages})

B_HEAD
	X_EXPIRES
	X_TITLE({Delete Language})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to delete languages.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Delete Language})
B_HEADER_BUTTONS
X_HBUTTON({Languages}, {languages/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault Language 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Languages WHERE Id=?Language" q_lang>dnl
<!sql if $NUM_ROWS>dnl
<P>
B_MSGBOX({Delete language})
	X_MSGBOX_TEXT({<LI>Are you sure you want to delete the language <B><!sql print ~q_lang.Name></B>?</LI>})
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.xql">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<!sql print ~Language>">
		<INPUT TYPE="IMAGE" NAME="Yes" SRC="X_ROOT/img/button/yes.gif" BORDER="0">
		<A HREF="X_ROOT/languages/"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
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
