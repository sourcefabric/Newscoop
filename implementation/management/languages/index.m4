B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Languages})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
<!sql query "SELECT Id, Name, OrigName, CodePage, Code FROM Languages WHERE 1=0" Languages>dnl
E_HEAD

<!sql if $access>dnl

SET_ACCESS({mla}, {ManageLanguages})
SET_ACCESS({dla}, {DeleteLanguages})

B_STYLE
E_STYLE

B_BODY

B_HEADER({Languages})
B_HEADER_BUTTONS
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql if ?mla != 0>
<P>X_NEW_BUTTON({Add new language}, {add.xql?Back=<!sql print #REQUEST_URI>})
<!sql endif>

<P><!sql setdefault LangOffs 0><!sql if $LangOffs < 0><!sql set LangOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Id, Name, OrigName, CodePage, Code FROM Languages ORDER BY Name LIMIT $LangOffs, 11" Languages>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Language})
		X_LIST_TH({Native name})
		X_LIST_TH({Code})
		X_LIST_TH({Code page})
	<!sql if ?mla != 0>
		X_LIST_TH({Edit}, {1%})
	<!sql endif>
	<!sql if ?dla != 0>
		X_LIST_TH({Delete}, {1%})
	<!sql endif>
	E_LIST_HEADER
<!sql print_loop Languages>dnl
<!sql if $i>dnl
	B_LIST_TR
		B_LIST_ITEM
			<!sql print ~Languages.Name>
		E_LIST_ITEM
		B_LIST_ITEM
			<!sql print ~Languages.OrigName>
		E_LIST_ITEM
		B_LIST_ITEM
			<!sql print ~Languages.Code>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
			<!sql print ~Languages.CodePage>&nbsp;
		E_LIST_ITEM
	<!sql if ?mla != 0> 
		B_LIST_ITEM({CENTER})
			<A HREF="modify.xql?Lang=<!sql print @Languages.Id>">Edit</A>
		E_LIST_ITEM
	<!sql endif>
	<!sql if ?dla != 0>
		B_LIST_ITEM({CENTER})
			X_BUTTON({Delete language <!sql print ~Languages.Name>}, {icon/x.gif}, {languages/del.xql?Language=<!sql print @Languages.Id>})
		E_LIST_ITEM
	<!sql endif>
	E_LIST_TR
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($LangOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({index.xql?LangOffs=<!sql eval ($LangOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({index.xql?LangOffs=<!sql eval ($LangOffs + 10)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No language.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
