B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageArticleTypes})

B_HEAD
	X_EXPIRES
	X_TITLE({Add New Field})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add article type fields.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault AType "">dnl
B_HEADER({Add New Field})
B_HEADER_BUTTONS
X_HBUTTON({Fields}, {a_types/fields/?AType=<!sql print ~AType>})
X_HBUTTON({Article Types}, {a_types/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT({Article type:}, {<B><!sql print ~AType></B>})
E_CURRENT

<P>
B_DIALOG({Add new field}, {POST}, {do_add.xql})
	B_DIALOG_INPUT({Name:})
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Type:})
		<SELECT NAME="cType">
			<OPTION VALUE="1">Text
			<OPTION VALUE="2">Date
			<OPTION VALUE="3">Body
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="AType" VALUE="<!sql print ~AType>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/a_types/fields/?AType=<!sql print #AType>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
