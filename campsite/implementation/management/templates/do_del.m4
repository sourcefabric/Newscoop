B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({DeleteTempl})

B_HEAD
	X_EXPIRES
	X_TITLE({Deleting Template})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to delete templates.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Path "">dnl
<!sql setdefault Name "">dnl
<!sql setdefault What 0>dnl
B_HEADER({Deleting Template})
B_HEADER_BUTTONS
X_HBUTTON({Templates}, {templates/?Path=<!sql print #Path>})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<P>
B_MSGBOX({Deleting template})
	X_MSGBOX_TEXT({<LI>
<!sql exec "X_SCRIPT_BIN/delete" "$Path" "$Name" "$What">
X_AUDIT({112}, {Templates deleted from ~Path~Name})
	</LI>})
	B_MSGBOX_BUTTONS
		<A HREF="<!sql print ~Path>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
