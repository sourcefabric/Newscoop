B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({DeleteArticleTypes})

B_HEAD
	X_EXPIRES
	X_TITLE({Deleting Field})
	<!sql if $access == 0>
		X_AD({You do not have the right to delete article type fields.})
	<!sql endif>
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY
<!sql setdefault AType "">dnl
<!sql setdefault Field "">dnl

B_HEADER({Deleting Field})
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
<!sql set NUM_ROWS 0>dnl
<!sql query "SHOW COLUMNS FROM X?AType LIKE 'F?Field'" c>dnl
<!sql if $NUM_ROWS>dnl
<!sql query "ALTER TABLE X?AType DROP COLUMN F?Field">dnl
<!sql endif>dnl
B_MSGBOX({Deleting field})
	X_MSGBOX_TEXT({<LI>The field <B><!sql print ~Field></B> has been deleted.</LI>})
X_AUDIT({72}, {Article type field ~Field deleted})
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/a_types/fields/?AType=<!sql print #AType>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
