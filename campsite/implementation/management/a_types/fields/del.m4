B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({DeleteArticleTypes})

B_HEAD
	X_EXPIRES
	X_TITLE({Delete Field})
	<!sql if $access == 0>
		X_AD({You do not have the right to delete article type fields.})
	<!sql endif>
E_HEAD

<!sql if $access>
B_STYLE
E_STYLE

B_BODY

<!sql setdefault AType "">dnl
<!sql setdefault Field "">dnl

B_HEADER({Delete Field})
B_HEADER_BUTTONS
X_HBUTTON({Fields}, {a_types/fields/?AType=<!sql print #AType>})
X_HBUTTON({Article Types}, {a_types/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT({Article type:}, {<B><!sql print ~AType></B>})
E_CURRENT

<P>
B_MSGBOX({Delete field})
	X_MSGBOX_TEXT({<LI>Are you sure you want to delete the field <B><!sql print ~Field></B>?</LI>
		<LI>You will also delete all fields with this name from all articles of this type from all publications.</LI>})
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.xql">
		<INPUT TYPE="HIDDEN" NAME="AType" VALUE="<!sql print ~AType>">
		<INPUT TYPE="HIDDEN" NAME="Field" VALUE="<!sql print ~Field>">
		<INPUT TYPE="IMAGE" NAME="Yes" SRC="X_ROOT/img/button/yes.gif" BORDER="0"></A>
		<A HREF="X_ROOT/a_types/fields/?AType=<!sql print #AType>"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<!sql free s>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>

E_DATABASE
E_HTML
