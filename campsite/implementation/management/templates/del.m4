B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({DeleteTempl})

B_HEAD
	X_EXPIRES
	X_TITLE({Delete Templates})
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
B_HEADER({Delete Templates})
B_HEADER_BUTTONS
X_HBUTTON({Templates}, {templates/?Path=<!sql print #Path>})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<P>
B_MSGBOX({Delete templates})
<!sql if $What == 0>dnl
	X_MSGBOX_TEXT({<LI>Are you sure you want to delete the folder <B><!sql print ~Name></B> from <B><!sql print ~Path></B>?</LI>})
<!sql else>dnl
	X_MSGBOX_TEXT({<LI>Are you sure you want to delete the template <B><!sql print ~Name></B> from folder <B><!sql print ~Path></B>?</LI>})
<!sql endif>dnl
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.xql">
		<INPUT TYPE="HIDDEN" NAME="Path" VALUE="<!sql print ~Path>">
		<INPUT TYPE="HIDDEN" NAME="Name" VALUE="<!sql print ~Name>">
		<INPUT TYPE="HIDDEN" NAME="What" VALUE="<!sql print ~What>">
		<INPUT TYPE="IMAGE" SRC="X_ROOT/img/button/yes.gif" BORDER="0" NAME="Yes"></A>
		<A HREF="<!sql print ~Path>"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
