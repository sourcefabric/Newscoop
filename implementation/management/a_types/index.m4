B_HTML
B_DATABASE

<!sql query "SHOW TABLES LIKE 'XXYYZZ'" ATypes>dnl
CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Article Types})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl

SET_ACCESS({mata}, {ManageArticleTypes})
SET_ACCESS({data}, {DeleteArticleTypes})

B_STYLE
E_STYLE

B_BODY

B_HEADER({Article Types})
B_HEADER_BUTTONS
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql if $mata != 0>dnl
<P>X_NEW_BUTTON({Add new article type}, {add.xql?Back=<!sql print #REQUEST_URI>})
<!sql endif>dnl

<P>
<!sql set NUM_ROWS 0>dnl
<!sql query "SHOW TABLES LIKE 'X%'" ATypes>dnl
<!sql if $NUM_ROWS>dnl
<!sql setdefault ATOffs 0><!sql if ($ATOffs <= 0)><!sql set ATOffs 0><!sql endif>dnl
<!sql setexpr be $ATOffs><!sql set en 0>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Type})
		X_LIST_TH({Fields}, {1%})
<!sql if $data != 0>dnl
		X_LIST_TH({Delete}, {1%})
<!sql endif>dnl
	E_LIST_HEADER
<!sql print_loop ATypes>dnl
<!sql if (0 < $be)>dnl
<!sql setexpr be ($be - 1)>dnl
<!sql else>dnl
<!sql if ($en < 10)>dnl
<!sql query "SELECT SUBSTRING('?ATypes.0', 2)" s>dnl
	B_LIST_TR
		B_LIST_ITEM
			<!sql print ~s.0>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			<A HREF="X_ROOT/a_types/fields/?AType=<!sql print #s.0>">Fields</A>
		E_LIST_ITEM
<!sql if $data != 0>dnl
		B_LIST_ITEM({CENTER})
			X_BUTTON({Delete article type <!sql print ~s.0>}, {icon/x.gif}, {a_types/del.xql?AType=<!sql print #s.0>})
		E_LIST_ITEM
<!sql endif>dnl
	E_LIST_TR
<!sql free s>dnl
<!sql endif>dnl
<!sql setexpr en ($en + 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($ATOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({X_ROOT/a_types/?ATOffs=<!sql eval ($ATOffs - 10)>">})
<!sql endif>dnl
<!sql if (10 < $en)>dnl
		X_NEXT_A({X_ROOT/a_types/?ATOffs=<!sql eval ($ATOffs + 10)>">})
<!sql else>dnl
		X_NEXT_I
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No article types.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
