B_HTML
B_DATABASE
<!sql setdefault cName "">dnl
<!sql set correct 1><!sql set created 0><!sql set j 0>dnl

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageArticleTypes})

B_HEAD
	X_EXPIRES
	X_TITLE({Adding New Article Type})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add new article types.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Adding New Article Type})
B_HEADER_BUTTONS
X_HBUTTON({Article Types}, {a_types/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<P>
B_MSGBOX({Adding new article type})
	X_MSGBOX_TEXT({
<!sql if ($cName == "")>dnl
<!sql set correct 0>dnl
	<LI>You must complete the <B>Name</B> field.</LI>
<!sql else>dnl
<!sql query "SELECT LENGTH('?cName')" l>dnl
<!sql setexpr j @l.0>dnl
<!sql set ok 1>dnl
<!sql while $j>dnl
<!sql query "SELECT ASCII(LCASE(SUBSTRING('?cName', ?j))) BETWEEN 97 AND 122" s>dnl
<!sql if (@s.0 == 0)>dnl
<!sql set ok 0>dnl
<!sql endif>dnl
<!sql free s>dnl
<!sql setexpr j ($j - 1)>dnl
<!sql done>dnl
<!sql free l>dnl
<!sql if ($ok == 0)>dnl
<!sql set correct 0>dnl
	<LI>The <B>Name</B> field may only contain letters.</LI>
<!sql endif>dnl
<!sql if $correct>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SHOW TABLES LIKE 'X?cName'" t>dnl
<!sql if $NUM_ROWS>dnl
<!sql set correct 0>dnl
	<LI>The article type <B><!sql print ~cName></B> already exists.</LI>
<!sql endif>dnl
<!sql free t>dnl
<!sql endif>dnl
<!sql if $correct>dnl
<!sql query "CREATE TABLE X?cName (NrArticle INT UNSIGNED NOT NULL, IdLanguage INT UNSIGNED NOT NULL, PRIMARY KEY(NrArticle, IdLanguage))">
<!sql set created 1>
	<LI>The article type <B><!sql print ~cName></B> has been added.</LI>
X_AUDIT({61}, {Article type ~cName added})
<!sql endif>dnl
<!sql endif>dnl
	})
<!sql setdefault Back "">dnl
<!sql if $correct && $created>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/a_types/fields/add.xql?AType=<!sql print #cName>"><IMG SRC="X_ROOT/img/button/new_field.gif" BORDER="0" ALT="Add field"></A>
		<A HREF="X_ROOT/a_types/add.xql"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another article type"></A>
		<A HREF="X_ROOT/a_types/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<!sql else>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/a_types/add.xql<!sql if $Back != "">?Back=<!sql print #Back><!sql endif>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
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
