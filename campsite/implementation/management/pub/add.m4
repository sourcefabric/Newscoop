B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManagePub})

B_HEAD
	X_TITLE({Add New Publication})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add publications.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Add New Publication})
B_HEADER_BUTTONS
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<P>
B_DIALOG({Add new publication}, {POST}, {do_add.xql})
	B_DIALOG_INPUT({Name:})
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Site:})
		<INPUT TYPE="TEXT" NAME="cSite" VALUE="<!sql print ~HTTP_HOST>" SIZE="32" MAXLENGTH="128">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Default language:})
	    <SELECT NAME="cLanguage">
<!sql query "SELECT Id, Name FROM Languages" q_lang>dnl
<!sql print_rows q_lang "<OPTION VALUE=\"~q_lang.Id\">~q_lang.Name">dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Pay time:})
		<INPUT TYPE="TEXT" NAME="cPayTime" VALUE="1" SIZE="5" MAXLENGTH="5"> days
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
<!sql setdefault Back "">dnl
<!sql if $Back != "">dnl
		<A HREF="<!sql print $Back>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<!sql else>dnl
		<A HREF="X_ROOT/pub/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
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
