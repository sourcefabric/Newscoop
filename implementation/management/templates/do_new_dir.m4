B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageTempl})

B_HEAD
	X_EXPIRES
	X_TITLE({Creating New Folder})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to create new folders.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault cPath "">dnl
B_HEADER({Creating New Folder})
B_HEADER_BUTTONS
X_HBUTTON({Templates}, {templates/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault cName "">dnl
<!sql set correct 1><!sql set created 0>dnl
<P>
B_MSGBOX({Creating new folder})
	X_MSGBOX_TEXT({
<!sql if ($cName == "")>dnl
<!sql set correct 0>dnl
		<LI>You must complete the <B>Name</B> field.</LI>
<!sql endif>dnl
<!sql if $correct>dnl
<!sql exec "X_SCRIPT_BIN/make_dir" "$cPath" "$cName">dnl
<!sql endif>dnl
		})
	B_MSGBOX_BUTTONS
<!sql if $correct>dnl
		<A HREF="X_ROOT/templates/new_dir.xql?Path=<!sql print $cPath>"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Create another folder"></A>
		<A HREF="<!sql print $cPath>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>
		<A HREF="X_ROOT/templates/new_dir.xql?Path=<!sql print $cPath>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
