B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({User Types})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql query "SELECT * FROM UserTypes WHERE 1=0" UTypes>dnl
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
SET_ACCESS({muta}, {ManageUserTypes})

B_STYLE
E_STYLE

B_BODY

B_HEADER({User Types})
B_HEADER_BUTTONS
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql if ?muta != 0>
<P>X_NEW_BUTTON({Add new user type}, {add.xql?Back=<!sql print #REQUEST_URI>})
<!sql endif>

<P><!sql setdefault UTOffs 0><!sql if $UTOffs < 0><!sql set UTOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM UserTypes ORDER BY Name LIMIT $UTOffs, 11" UTypes>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Type})
		X_LIST_TH({Reader}, {1%})
	<!sql if ?muta != 0> 
		X_LIST_TH({Access}, {1%})
		X_LIST_TH({Delete}, {1%})
	<!sql endif>
	E_LIST_HEADER
	<!sql print_loop UTypes>dnl
	<!sql if $i>dnl
	B_LIST_TR
		B_LIST_ITEM
			<!sql print ~UTypes.Name>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			<!sql if (@UTypes.Reader == "Y")>Yes<!sql else>No<!sql endif>
		E_LIST_ITEM
	<!sql if ?muta != 0> 
		B_LIST_ITEM({CENTER})
			<A HREF="access.xql?UType=<!sql print #UTypes.Name>">Change</A>
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			X_BUTTON({Delete user type <!sql print ~UTypes.Name>}, {icon/x.gif}, {u_types/del.xql?UType=<!sql print #UTypes.Name>})
		E_LIST_ITEM
	<!sql endif>
	E_LIST_TR
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($UTOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({index.xql?UTOffs=<!sql eval ($UTOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({index.xql?UTOffs=<!sql eval ($UTOffs + 10)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No user types.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
