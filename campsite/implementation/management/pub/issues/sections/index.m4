B_HTML
B_DATABASE

<!sql query "SELECT * FROM Sections WHERE 1=0" q_sect>dnl
CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Sections})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

SET_ACCESS({msa}, {ManageSection})
SET_ACCESS({dsa}, {DeleteSection})

B_BODY

<!sql setdefault Pub 0>dnl
<!sql setdefault Issue 0>dnl
<!sql setdefault Language 0>dnl

B_HEADER({Sections})
B_HEADER_BUTTONS
X_HBUTTON({Issues}, {pub/issues/?Pub=<!sql print #Pub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Issues WHERE IdPublication=?Pub AND Number=?Issue AND IdLanguage=?Language" q_iss>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Publications WHERE Id=?q_iss.IdPublication" q_pub>dnl
<!sql if $NUM_ROWS>dnl

<!sql query "SELECT Name FROM Languages WHERE Id=?Language" q_language>dnl
B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
X_CURRENT({Issue:}, {<B><!sql print ~q_iss.Number>. <!sql print ~q_iss.Name> (<!sql print ~q_language.Name>)</B>})
E_CURRENT
<!sql free q_language>dnl

<!sql if $msa != 0>
<P>X_NEW_BUTTON({Add new section}, {add.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>})
<!sql endif>

<P><!sql setdefault SectOffs 0><!sql if $SectOffs < 0><!sql set SectOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Sections WHERE IdPublication=?Pub AND NrIssue=?Issue AND IdLanguage=?Language ORDER BY Number LIMIT $SectOffs, 21" q_sect>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 20>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Nr}, {1%})
		X_LIST_TH({Name<BR><SMALL>(click to see articles)</SMALL>})
	<!sql if $msa != 0>
		X_LIST_TH({Change}, {1%})
	<!sql endif>
	<!sql if $dsa != 0>
		X_LIST_TH({Delete}, {1%})
	<!sql endif>
	E_LIST_HEADER
<!sql print_loop q_sect>dnl
<!sql if $i>dnl
	B_LIST_TR
		B_LIST_ITEM({RIGHT})
			<!sql print ~q_sect.Number>
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/articles/?Pub=<!sql print #Pub>&Issue=<!sql print #q_sect.NrIssue>&Section=<!sql print #q_sect.Number>&Language=<!sql print #q_sect.IdLanguage>"><!sql print ~q_sect.Name></A>
		E_LIST_ITEM
	<!sql if $msa != 0>
		B_LIST_ITEM({CENTER})
			<A HREF="X_ROOT/pub/issues/sections/edit.xql?Pub=<!sql print #Pub>&Issue=<!sql print #q_sect.NrIssue>&Section=<!sql print #q_sect.Number>&Language=<!sql print #q_sect.IdLanguage>">Change</A>
		E_LIST_ITEM
	<!sql endif>

	<!sql if $dsa != 0>
		B_LIST_ITEM({CENTER})
			X_BUTTON({Delete section <!sql print ~q_sect.Name>}, {icon/x.gif}, {pub/issues/sections/del.xql?Pub=<!sql print #Pub>&Issue=<!sql print #q_sect.NrIssue>&Section=<!sql print #q_sect.Number>&Language=<!sql print #q_sect.IdLanguage>})
		E_LIST_ITEM
	<!sql endif>
	E_LIST_TR
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($SectOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({index.xql?Pub=<!sql print #Pub>&Language=<!sql print #Language>&Issue=<!sql print #Issue>&SectOffs=<!sql eval ($SectOffs - 20)>})
<!sql endif>dnl
<!sql if $nr < 21>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({index.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>&SectOffs=<!sql eval ($SectOffs + 20)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No sections.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such publication.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such issue.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
