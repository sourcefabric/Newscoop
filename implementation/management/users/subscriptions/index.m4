B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Subscriptions})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
<!sql query "SELECT * FROM Subscriptions WHERE 1=0" q_subs>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault User 0>dnl
B_HEADER({Subscriptions})
B_HEADER_BUTTONS
X_HBUTTON({Users}, {users/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT UName FROM Users WHERE Id=?User" q_usr>dnl
<!sql if $NUM_ROWS>dnl

B_CURRENT
X_CURRENT({User account:}, {<B><!sql print ~q_usr.UName></B>})
E_CURRENT

<P>X_NEW_BUTTON({Add new subscription}, {add.xql?User=<!sql print #User>})
<P><!sql setdefault SubsOffs 0><!sql if $SubsOffs < 0><!sql set SubsOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Subscriptions WHERE IdUser=?User ORDER BY Id DESC LIMIT $SubsOffs, 11" q_subs>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Publication}<BR><SMALL>(click to see sections)</SMALL>)
		X_LIST_TH({Left to pay})
		X_LIST_TH({Active}, {1%})
		X_LIST_TH({Delete}, {1%})
	E_LIST_HEADER
<!sql print_loop q_subs>dnl
<!sql if $i>dnl
	B_LIST_TR
		B_LIST_ITEM
<!sql query "SELECT Name FROM Publications WHERE Id=?q_subs.IdPublication" q_pub>dnl
			<A HREF="X_ROOT/users/subscriptions/sections/?Subs=<!sql print #q_subs.Id>&Pub=<!sql print #q_subs.IdPublication>&User=<!sql print #User>"><!sql print ~q_pub.Name></A>dnl
<!sql free q_pub>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="X_ROOT/users/subscriptions/topay.xql?User=<!sql print #User>&Subs=<!sql print #q_subs.Id>">dnl
			<!sql print ~q_subs.ToPay> <!sql print ~q_subs.Currency>
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			<A HREF="X_ROOT/users/subscriptions/status.xql?User=<!sql print #User>&Subs=<!sql print #q_subs.Id>">dnl
<!sql if @q_subs.Active == "Y">Yes<!sql else>No<!sql endif></A>
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			X_BUTTON({Delete subscriptions to <!sql print ~q_pub.Name>}, {icon/x.gif}, {users/subscriptions/del.xql?User=<!sql print #User>&Subs=<!sql print #q_subs.Id>})
		E_LIST_ITEM
	E_LIST_TR
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($SubsOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({index.xql?User=<!sql print #User>&SubsOffs=<!sql eval ($SubsOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({index.xql?User=<!sql print #User>&SubsOffs=<!sql eval ($SubsOffs + 10)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No subscriptions.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such user account.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
