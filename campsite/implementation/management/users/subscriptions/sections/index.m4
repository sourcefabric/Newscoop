B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Subscribed Sections})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
<!sql query "SELECT * FROM SubsSections WHERE 1=0" q_ssect>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>dnl
<!sql setdefault User 0>dnl
<!sql setdefault Subs 0>dnl
B_HEADER({Subscribed Sections})
B_HEADER_BUTTONS
X_HBUTTON({Subscriptions}, {users/subscriptions/?User=<!sql print #User>})
X_HBUTTON({Users}, {users/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT UName FROM Users WHERE Id=?User" q_usr>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl

B_CURRENT
X_CURRENT({User account:}, {<B><!sql print ~q_usr.UName></B>})
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
E_CURRENT

<P>X_NEW_BUTTON({Add new section to subscription}, {add.xql?Subs=<!sql print #Subs>&Pub=<!sql print #Pub>&User=<!sql print #User>})

<P><!sql setdefault SSectOffs 0><!sql if $SSectOffs < 0><!sql set SSectOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl

<!sql query "SELECT DISTINCT Sub.*, Sec.Name FROM SubsSections as Sub, Sections as Sec WHERE IdSubscription=?Subs AND Sub.SectionNumber = Sec.Number ORDER BY SectionNumber LIMIT $SSectOffs, 11" q_ssect>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Section (Number.Name)})
		X_LIST_TH({Start Date}<BR><SMALL>(yyyy-mm-dd)</SMALL>)
		X_LIST_TH({Days})
		X_LIST_TH({Paid Days})
		X_LIST_TH({Change}, {1%})
		X_LIST_TH({Delete}, {1%})
	E_LIST_HEADER
<!sql print_loop q_ssect>dnl
<!sql if $i>dnl
	B_LIST_TR
		B_LIST_ITEM
			<!sql print ~q_ssect.SectionNumber.~q_ssect.Name>
		E_LIST_ITEM
		B_LIST_ITEM
			<!sql print ~q_ssect.StartDate>
		E_LIST_ITEM
		B_LIST_ITEM
			<!sql print ~q_ssect.Days>
		E_LIST_ITEM
		B_LIST_ITEM
			<!sql print ~q_ssect.PaidDays>
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			<A HREF="X_ROOT/users/subscriptions/sections/change.xql?User=<!sql print #User>&Pub=<!sql print #Pub>&Subs=<!sql print #Subs>&Sect=<!sql print #q_ssect.SectionNumber>">Change</A>
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			X_BUTTON({Delete subscription to section <!sql print ~q_ssect.SectionNumber>}, {icon/x.gif}, {users/subscriptions/sections/del.xql?User=<!sql print #User>&Pub=<!sql print #Pub>&Subs=<!sql print #Subs>&Sect=<!sql print #q_ssect.SectionNumber>})
		E_LIST_ITEM
	E_LIST_TR
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($SSectOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({index.xql?Subs=<!sql print #Subs>&Pub=<!sql print #Pub>&User=<!sql print #User>&SSectOffs=<!sql eval ($SSectOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({index.xql?Subs=<!sql print #Subs>&Pub=<!sql print #Pub>&User=<!sql print #User>&SSectOffs=<!sql eval ($SSectOffs + 10)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No sections in current subscriptions.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such publication.</LI>
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
