B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageTempl})

B_HEAD
	X_EXPIRES
	X_TITLE({Upload Template})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to upload templates.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Upload Template})
B_HEADER_BUTTONS
X_HBUTTON({Templates}, {templates/?Path=<!sql print #Path>})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault Path "">dnl
<P>
B_DIALOG({Upload template}, {POST}, {/cgi-bin/upload_t}, {multipart/form-data})
	B_DIALOG_INPUT({File:})
		<INPUT TYPE="HIDDEN" NAME="Path" VALUE="<!sql print ~Path>">
		<INPUT TYPE="FILE" NAME="File" SIZE="32" MAXLENGTH="128">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
<!sql setdefault Back "">dnl
<!sql if $Back != "">dnl
		<A HREF="<!sql print $Back>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<!sql else>dnl
		<A HREF="X_ROOT/templates/?Path=<!sql print #Path>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<!sql endif>dnl
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
