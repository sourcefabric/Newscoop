B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManagePub})

B_HEAD
	X_EXPIRES
	X_TITLE({Adding New Publication})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add publications.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Adding New Publication})
B_HEADER_BUTTONS
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault cName "">dnl
<!sql setdefault cSite "">dnl
<!sql setdefault cLanguage 0>dnl
<!sql setdefault cPayTime 0>dnl
<!sql set correct 1><!sql set created 0>dnl
<P>
B_MSGBOX({Adding new publication})
	X_MSGBOX_TEXT({
<!sql query "SELECT TRIM('?cName'), TRIM('?cSite')" q_tr>dnl
<!sql if (@q_tr.0 == "" || @q_tr.0 == " ")>dnl
<!sql set correct 0>dnl
		<LI>You must complete the <B>Name</B> field.</LI>
<!sql endif>dnl
<!sql if (@q_tr.1 == "" || @q_tr.1 == " ")>dnl
<!sql set correct 0>dnl
		<LI>You must complete the <B>Site</B> field.</LI>
<!sql endif>dnl
<!sql if $correct>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "INSERT IGNORE INTO Publications SET Name='?q_tr.0', Site='?q_tr.1', IdDefaultLanguage=?cLanguage, PayTime='?cPayTime'">dnl
<!sql setexpr created ($AFFECTED_ROWS != 0)>dnl
<!sql endif>dnl
<!sql if $created>dnl
		<LI>The publication <B><!sql print ~q_tr.0></B> has been successfuly added.</LI>
X_AUDIT({1}, {Publication ?q_tr.0 added})
<!sql else>dnl
<!sql if ($correct != 0)>dnl
		<LI>The publication could not be added.</LI><LI>Please check if another publication with the same or the same site name does not already exist.</LI>
<!sql endif>dnl
<!sql endif>dnl
		})
<!sql if $correct && $created>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/add.xql"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another publication"></A>
		<A HREF="X_ROOT/pub/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<!sql else>
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/add.xql"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
	E_MSGBOX_BUTTONS
<!sql endif>dnl
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
