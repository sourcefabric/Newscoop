B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Subscription Default Time})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
<!sql query "SELECT * FROM SubsDefTime WHERE 1=0" q_deft>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>dnl
<!sql setdefault Language 1>dnl
B_HEADER({Subscription Default Time})
B_HEADER_BUTTONS
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl
B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
E_CURRENT

<P>X_NEW_BUTTON({Add new country}, {countryadd.xql?Pub=<!sql print #Pub>&Language=<!sql print #Language>})

<P><!sql setdefault ListOffs 0><!sql if $ListOffs < 0><!sql set ListOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM SubsDefTime WHERE IdPublication=?Pub ORDER BY CountryCode LIMIT $ListOffs, 11" q_deft>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Country<BR><SMALL>(click to edit)</SMALL>})
		X_LIST_TH({Trial Time}, {1%}, {nowrap})
		X_LIST_TH({Paid Time}, {1%}, {nowrap})
		X_LIST_TH({Delete}, {1%})
	E_LIST_HEADER
<!sql print_loop q_deft>dnl
<!sql if $i>dnl
	B_LIST_TR
		B_LIST_ITEM
<!sql query "SELECT * FROM Countries WHERE Code = '?q_deft.CountryCode' AND IdLanguage = ?Language" q_ctr>dnl
			<A HREF="X_ROOT/pub/editdeftime.xql?Pub=<!sql print #Pub>&CountryCode=<!sql print ~q_deft.CountryCode>&Language=<!sql print #Language>"><!sql print ~q_ctr.Name> (<!sql print ~q_ctr.Code>)</A>
		E_LIST_ITEM
		B_LIST_ITEM({RIGHT})
			<!sql print ~q_deft.TrialTime>
		E_LIST_ITEM
		B_LIST_ITEM({RIGHT})
			<!sql print ~q_deft.PaidTime>
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			X_BUTTON({Delete entry <!sql print ~q_deft.CountryCode>}, {icon/x.gif}, {pub/deldeftime.xql?Pub=<!sql print #Pub>&CountryCode=<!sql print #q_deft.CountryCode>&Language=<!sql print #Language>})
		E_LIST_ITEM
	E_LIST_TR
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER(9)
<!sql if ($ListOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({index.xql?Pub=<!sql print #Pub>&ListOffs=<!sql eval ($ListOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({index.xql?Pub=<!sql print #Pub>&ListOffs=<!sql eval ($ListOffs + 10)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No entries defined.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such publication.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
