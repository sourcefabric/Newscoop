B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Article Type Fields})
	<!sql if $access == 0>
		X_LOGOUT
	<!sql endif>
<!sql query "SHOW COLUMNS FROM Articles LIKE 'XXYYZZ'" q_col>dnl
E_HEAD

<!sql if $access>dnl

SET_ACCESS({mata}, {ManageArticleTypes})

B_STYLE
E_STYLE

B_BODY

B_HEADER({Article Type Fields})
B_HEADER_BUTTONS
X_HBUTTON({Article Types}, {a_types/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT({Article type:}, {<B><!sql print ~AType></B>})
E_CURRENT

<!sql if ?mata != 0>
<P>X_NEW_BUTTON({Add new field}, {add.xql?AType=<!sql print ~AType>})
<!sql endif>


<P>
<!sql set NUM_ROWS 0>dnl
<!sql query "SHOW COLUMNS FROM X?AType LIKE 'F%'" q_col>
<!sql if $NUM_ROWS>dnl
<!sql setdefault AFOffs 0><!sql if ($AFOffs <= 0)><!sql set AFOffs 0><!sql endif>dnl
<!sql setexpr be $AFOffs><!sql set en 0>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Name})
		X_LIST_TH({Type})
	<!sql if ?mata != 0>
		X_LIST_TH({Delete}, {1%})
	<!sql endif>
	E_LIST_HEADER
<!sql print_loop q_col>dnl
<!sql if (0 < $be)>dnl
<!sql setexpr be ($be - 1)>dnl
<!sql else>dnl
<!sql if ($en < 10)>dnl
<!sql query "SELECT SUBSTRING('?q_col.0', 2)" q_ss>dnl
	B_LIST_TR
		B_LIST_ITEM
			<!sql print ~q_ss.0>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
<!sql query "SELECT REPLACE(REPLACE(REPLACE('?q_col.1', 'mediumblob', 'Article body'), 'varchar(100)', 'Text '), 'date', 'Date')" q_rr>dnl
			<!sql print ~q_rr.0>&nbsp;
<!sql free q_rr>dnl
		E_LIST_ITEM
	<!sql if ?mata != 0>
		B_LIST_ITEM({CENTER})
			X_BUTTON({Delete field <!sql print ~q_ss.0>}, {icon/x.gif}, {a_types/fields/del.xql?AType=<!sql print #AType>&Field=<!sql print #q_ss.0>})
		E_LIST_ITEM
	<!sql endif>
	E_LIST_TR
<!sql free q_ss>dnl
<!sql endif>dnl
<!sql setexpr en ($en + 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($AFOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({X_ROOT/a_types/fields/?AType=<!sql print #AType>&AFOffs=<!sql eval ($AFOffs - 10)>})
<!sql endif>dnl
<!sql if (10 < $en)>dnl
		X_NEXT_A({X_ROOT/a_types/fields/?AType=<!sql print #AType>&AFOffs=<!sql eval ($AFOffs + 10)>})
<!sql else>dnl
		X_NEXT_I
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No fields.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
