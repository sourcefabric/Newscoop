B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Publications})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
<!sql query "SELECT * FROM Publications WHERE 1=0" publ>dnl
E_HEAD

<!sql if $access>dnl
SET_ACCESS({mpa}, {ManagePub})
SET_ACCESS({dpa}, {DeletePub})

B_STYLE
E_STYLE

B_BODY

B_HEADER({Publications})
B_HEADER_BUTTONS
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql if $mpa != 0>dnl
	<P>X_NEW_BUTTON({Add new publication}, {add.xql?Back=<!sql print #REQUEST_URI>})
<!sql endif>dnl 

<P><!sql setdefault PubOffs 0><!sql if $PubOffs < 0><!sql set PubOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Publications ORDER BY Name LIMIT $PubOffs, 11" publ>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Name<BR><SMALL>(click to see issues)</SMALL>})
		X_LIST_TH({Site}, {20%})
		X_LIST_TH({Default Language}, {20%})
	<!sql if $mpa != 0>dnl 
		X_LIST_TH({Subscription Default Time}, {10%})
		X_LIST_TH({Pay Time}, {10%})
		X_LIST_TH({Time Unit}, {10%})
		X_LIST_TH({Unit Cost}, {10%})
		X_LIST_TH({Info}, {1%})
	<!sql endif>dnl 
	<!sql if $dpa != 0>dnl
		X_LIST_TH({Delete}, {1%})
	<!sql endif>dnl 
	E_LIST_HEADER
<!sql print_loop publ>dnl
<!sql if $i>dnl
	B_LIST_TR
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/?Pub=<!sql print #publ.Id>"><!sql print ~publ.Name></A>
		E_LIST_ITEM
		B_LIST_ITEM
			<!sql print ~publ.Site>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
			<!sql query "SELECT Name FROM Languages WHERE Id=?publ.IdDefaultLanguage" q_dlng><!sql print ~q_dlng.Name>&nbsp;
		E_LIST_ITEM
<!sql if $mpa != 0>dnl
		B_LIST_ITEM
			<a href="deftime.xql?Pub=<!sql print #publ.Id>">Change</A>
		E_LIST_ITEM
		B_LIST_ITEM
			<!sql print ~publ.PayTime> days
		E_LIST_ITEM
			<!--sql query "SELECT Name FROM TimeUnits where Unit = '~publ.TimeUnit' and IdLanguage = 1" tu-->
			<!sql query "SELECT Name FROM TimeUnits where Unit = '~publ.TimeUnit' and IdLanguage = ?publ.IdDefaultLanguage" tu>
		B_LIST_ITEM
			<!sql print ~tu.Name>
		E_LIST_ITEM
		B_LIST_ITEM
			<!sql print ~publ.UnitCost> <!sql print ~publ.Currency>
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			<A HREF="X_ROOT/pub/edit.xql?Pub=<!sql print #publ.Id>">Change</A>
		E_LIST_ITEM
<!sql endif>dnl
<!sql if $dpa != 0>dnl 
		B_LIST_ITEM({CENTER})
			X_BUTTON({Delete publication <!sql print ~publ.Name>}, {icon/x.gif}, {pub/del.xql?Pub=<!sql print @publ.Id>})
		E_LIST_ITEM
<!sql endif>dnl
    E_LIST_TR
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($PubOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({index.xql?PubOffs=<!sql eval ($PubOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({index.xql?PubOffs=<!sql eval ($PubOffs + 10)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No publications.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
