B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Keyword Classes})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
<!sql query "SELECT IdClasses FROM KeywordClasses WHERE 1=0" q_kwdcls>dnl
E_HEAD

<!sql if $access>dnl
SET_ACCESS({mda}, {ManageDictionary})

B_STYLE
E_STYLE

B_BODY

<!sql setdefault Keyword 0>dnl
<!sql setdefault Language 0>dnl
B_HEADER({Keyword Classes})
B_HEADER_BUTTONS
X_HBUTTON({Dictionary}, {dictionary/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Keyword FROM Dictionary WHERE Id=?Keyword AND IdLanguage=?Language" q_dict>dnl
<!sql if $NUM_ROWS>dnl
<!sql query "SELECT Name FROM Languages WHERE Id=?Language" q_lang>dnl
B_CURRENT
X_CURRENT({Keyword:}, {<B><!sql print ~q_dict.Keyword></B>})
X_CURRENT({Language:}, {<B><!sql print ~q_lang.Name></B>})
E_CURRENT

<!sql if ?mda != 0>
<P>X_NEW_BUTTON({Add new keyword class}, {add.xql?Keyword=<!sql print #Keyword>&Language=<!sql print #Language>})
<!sql endif>

<P><!sql setdefault KwdOffs 0><!sql if $KwdOffs < 0><!sql set KwdOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT IdClasses FROM KeywordClasses WHERE IdDictionary=?Keyword AND IdLanguage=?Language LIMIT $KwdOffs, 11" q_kwdcls>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Class})
	<!sql if ?mda != 0>
		X_LIST_TH({Edit}, {1%})
		X_LIST_TH({Delete}, {1%})
	<!sql endif>
	E_LIST_HEADER
<!sql print_loop q_kwdcls>dnl
<!sql if $i>dnl
	B_LIST_TR
		B_LIST_ITEM
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Classes WHERE Id=?q_kwdcls.IdClasses AND IdLanguage=?Language" q_cls>dnl
<!sql if $NUM_ROWS>dnl
			<!sql print ~q_cls.Name>dnl
<!sql else>dnl
			&nbsp;
<!sql endif>dnl
<!sql free q_cls>dnl
		E_LIST_ITEM
	<!sql if ?mda != 0>
		B_LIST_ITEM({CENTER})
			<A HREF="X_ROOT/dictionary/keyword/edit.xql?Keyword=<!sql print #Keyword>&Class=<!sql print #q_kwdcls.IdClasses>&Language=<!sql print #Language>">Edit</A>
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			X_BUTTON({Unlink class}, {icon/x.gif}, {dictionary/keyword/del.xql?Keyword=<!sql print #Keyword>&Class=<!sql print #q_kwdcls.IdClasses>&Language=<!sql print #Language>})
		E_LIST_ITEM
	<!sql endif>
	E_LIST_TR
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($KwdOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({index.xql?Keyword=<!sql print #Keyword>&Language=<!sql print #Language>&KwdOffs=<!sql eval ($KwdOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({index.xql?Keyword=<!sql print #Keyword>&Language=<!sql print #Language>&KwdOffs=<!sql eval ($KwdOffs + 10)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No classes for this keyword.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such keyword.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
