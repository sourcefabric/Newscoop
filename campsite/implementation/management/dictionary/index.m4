B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Dictionary})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
<!sql query "SELECT Id, Name FROM Languages WHERE 1=0" ls>dnl
<!sql query "SELECT Id, IdLanguage, Keyword FROM Dictionary WHERE 1=0" Dict>dnl
E_HEAD

<!sql if $access>dnl
SET_ACCESS({mda}, {ManageDictionary})
SET_ACCESS({dda}, {DeleteDictionary})

B_STYLE
E_STYLE

B_BODY

B_HEADER({Dictionary})
B_HEADER_BUTTONS
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault sKeyword ""><!sql setdefault sLang "">dnl
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<!sql if ?mda != 0>
	<TD>X_NEW_BUTTON({Add new keyword}, {add.xql})</TD>
	<!sql endif>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG({GET}, {index.xql})
		<TD>Keyword:</TD>
		<TD><INPUT TYPE="TEXT" NAME="sKeyword" VALUE="<!sql print ~sKeyword>" SIZE="16" MAXLENGTH="32"></TD>
		<TD><SELECT NAME="sLang"><OPTION><!sql query "SELECT Id, Name FROM Languages ORDER BY Name" ls><!sql print_loop ls><OPTION VALUE="<!sql print ~ls.Id>"<!sql if @ls.Id == $sLang> SELECTED<!sql endif>><!sql print ~ls.Name><!sql done><!sql free ls></SELECT></TD>
		<TD><INPUT TYPE="IMAGE" SRC="X_ROOT/img/button/search.gif" BORDER="0"></TD>
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<!sql if $sKeyword != ""><!sql set kk "Keyword LIKE '?sKeyword%'"><!sql else><!sql set kk ""><!sql endif>dnl
<!sql if $sLang != ""><!sql set ll "IdLanguage = ?sLang"><!sql else><!sql set ll ""><!sql endif>dnl
<!sql set ww "">dnl
<!sql set aa "">dnl
<!sql if $sLang != "">dnl
<!sql set ww "WHERE ">dnl
<!sql endif>dnl
<!sql if $sKeyword != "">dnl
<!sql if $ww != "">dnl
<!sql set aa " AND ">dnl
<!sql endif>dnl
<!sql set ww "WHERE ">dnl
<!sql endif>dnl

<!sql set kwdid "xxxxxx">dnl

<P><!sql setdefault DictOffs 0><!sql if $DictOffs < 0><!sql set DictOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Id, IdLanguage, Keyword FROM Dictionary $ww$ll$aa$kk ORDER BY Id, IdLanguage LIMIT $DictOffs, 11" Dict>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Keyword})
		X_LIST_TH({Language})
	<!sql if ?mda != 0>
		X_LIST_TH({Translate}, {1%})
	<!sql endif>
		X_LIST_TH({Classes}, {1%})
	<!sql if ?dda != 0>
		X_LIST_TH({Delete}, {1%})
	<!sql endif>
	E_LIST_HEADER
	<!sql print_loop Dict>dnl
	<!sql if $i>dnl
	B_LIST_TR
		B_LIST_ITEM
			<!sql if (@Dict.Id == $kwdid)>&nbsp; <!sql endif><!sql print ~Dict.Keyword>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
<!sql query "SELECT Name FROM Languages WHERE Id=@Dict.IdLanguage" l>dnl
			<!sql print_rows l "~l.0">&nbsp;
<!sql free l>dnl
		E_LIST_ITEM
	<!sql if ?mda != 0>
		B_LIST_ITEM({CENTER})
<!sql if (@Dict.Id != $kwdid)>dnl
			<A HREF="X_ROOT/dictionary/translate.xql?Keyword=<!sql print #Dict.Id>">Translate</A>
<!sql endif>&nbsp;
		E_LIST_ITEM
	<!sql endif>
		B_LIST_ITEM({CENTER})
			<A HREF="X_ROOT/dictionary/keyword/?Keyword=<!sql print ~Dict.Id>&Language=<!sql print ~Dict.IdLanguage>">Classes</A>
		E_LIST_ITEM

	<!sql if ?dda != 0> 
		B_LIST_ITEM({CENTER})
			X_BUTTON({Delete keyword <!sql print ~Dict.Keyword>}, {icon/x.gif}, {dictionary/del.xql?Keyword=<!sql print @Dict.Id>&Language=<!sql print @Dict.IdLanguage>})
		E_LIST_ITEM
	<!sql endif>
<!sql if (@Dict.Id != $kwdid)>dnl
<!sql setexpr kwdid @Dict.Id>dnl
<!sql endif>dnl
	E_LIST_TR
	<!sql setexpr i ($i - 1)>dnl
	<!sql endif>
	<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($DictOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({index.xql?sKeyword=<!sql print #sKeyword>&sLang=<!sql print #sLang>&DictOffs=<!sql eval ($DictOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({index.xql?sKeyword=<!sql print #sKeyword>&sLang=<!sql print #sLang>&DictOffs=<!sql eval ($DictOffs + 10)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No keywords.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
