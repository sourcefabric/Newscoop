B_HTML
B_DATABASE
<!sql setdefault cName "">dnl
<!sql setdefault cType "">dnl
<!sql setdefault AType "">dnl

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageArticleTypes})

B_HEAD
	X_EXPIRES
	X_TITLE({Adding New Field})
	<!sql if $access == 0>
		X_AD({You do not have the right to add article type fields.})
	<!sql endif>
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY
<!sql setdefault cName "">dnl

B_HEADER({Adding New Field})
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

<!sql set created 0>dnl
<P>
B_MSGBOX({Adding new field})
	X_MSGBOX_TEXT({
<!sql query "SELECT LENGTH('?cName')" l>dnl
<!sql setexpr j @l.0>dnl
<!sql set correct 1>dnl
<!sql while $j>dnl
<!sql query "SELECT ASCII(LCASE(SUBSTRING('?cName', ?j))) BETWEEN 97 AND 122" s>dnl
<!sql if (@s.0 == 0)>dnl
<!sql set correct 0>dnl
<!sql endif>dnl
<!sql free s>dnl
<!sql setexpr j ($j - 1)>dnl
<!sql done>dnl
<!sql if @l.0 == 0>dnl
<!sql set correct 0>dnl
<!sql endif>dnl
<!sql free l>dnl
<!sql if $correct>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SHOW FIELDS FROM X?AType LIKE 'F?cName'" f>dnl
<!sql if $NUM_ROWS>dnl
	<LI>The field <B><!sql print ~cName></B> already exists.</LI>
	<!sql set correct 0>dnl
<!sql endif>dnl
<!sql else>dnl
	<LI>The <B>Name</B> must not be void and may only contain letters.</LI>
<!sql endif>dnl
<!sql if $correct>dnl
<!sql if ($cType == 1)>dnl
	<!sql query "ALTER TABLE X?AType ADD COLUMN F?cName VARCHAR(100) NOT NULL">
	<!sql set created 1>dnl
<!sql elsif ($cType == 2)>dnl
	<!sql query "ALTER TABLE X?AType ADD COLUMN F?cName DATE NOT NULL">
	<!sql set created 1>dnl
<!sql elsif ($cType == 3)>dnl
	<!sql query "ALTER TABLE X?AType ADD COLUMN F?cName MEDIUMBLOB NOT NULL">
	<!sql set created 1>dnl
<!sql else>dnl
	<LI>Invalid field type.</LI>
	<!sql set correct 0>dnl
<!sql endif>dnl
<!sql endif>dnl
<!sql if $created>dnl
	<LI>The field <B><!sql print ~cName></B> has been created.</LI>
X_AUDIT({71}, {Article type field ~cName created})
<!sql endif>dnl
	})
<!sql if $created>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/a_types/fields/add.xql?AType=<!sql print #AType>"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another field"></A>
		<A HREF="X_ROOT/a_types/fields/?AType=<!sql print #AType>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<!sql else>
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/a_types/fields/add.xql?AType=<!sql print #AType>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
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
