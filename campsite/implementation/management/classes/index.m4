B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
B_HEAD
	X_EXPIRES
	X_TITLE({Dictionary Classes})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
<!sql query "SELECT Id, Name FROM Languages WHERE 1=0" ls>dnl
<!sql query "SELECT Id, IdLanguage, Name FROM Classes WHERE 1=0" q_cls>dnl
E_HEAD

<!sql if $access>dnl
SET_ACCESS({mca}, {ManageClasses})

B_STYLE
E_STYLE

B_BODY

B_HEADER({Dictionary Classes})
B_HEADER_BUTTONS
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault sLang ""><!sql setdefault sName "">dnl
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<!sql if ?mca != 0>
	<TD>X_NEW_BUTTON({Add new dictionary class}, {add.xql})</TD>
	<!sql endif>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG({GET}, {index.xql})
		<TD>Class:</TD>
		<TD><INPUT TYPE="TEXT" NAME="sName" VALUE="<!sql print ~sName>"></TD>
		<TD><SELECT NAME="sLang"><OPTION><!sql query "SELECT Id, Name FROM Languages ORDER BY Name" ls><!sql print_loop ls><OPTION VALUE="<!sql print ~ls.Id>"<!sql if @ls.Id == $sLang> SELECTED<!sql endif>><!sql print ~ls.Name><!sql done><!sql free ls></SELECT></TD>
		<TD><INPUT TYPE="IMAGE" SRC="X_ROOT/img/button/search.gif" BORDER="0"></TD>
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<!sql if $sName != ""><!sql set kk "Name LIKE '?sName%'"><!sql else><!sql set kk ""><!sql endif>dnl
<!sql if $sLang != ""><!sql set ll "IdLanguage = ?sLang"><!sql else><!sql set ll ""><!sql endif>dnl
<!sql set ww "">dnl
<!sql set aa "">dnl
<!sql if $sLang != "">dnl
<!sql set ww "WHERE ">dnl
<!sql endif>dnl
<!sql if $sName != "">dnl
<!sql if $ww != "">dnl
<!sql set aa " AND ">dnl
<!sql endif>dnl
<!sql set ww "WHERE ">dnl
<!sql endif>dnl
<!sql set kwdid "xxxxxx">dnl

<P><!sql setdefault ClsOffs 0><!sql if $ClsOffs < 0><!sql set ClsOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Id, IdLanguage, Name FROM Classes $ww$ll$aa$kk ORDER BY Id, IdLanguage LIMIT $ClsOffs, 11" q_cls>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Class})
		X_LIST_TH({Language})
	<!sql if ?mca != 0> 
		X_LIST_TH({Translate}, {1%})
		X_LIST_TH({Delete}, {1%})
	<!sql endif>
	E_LIST_HEADER
<!sql print_loop q_cls>dnl
<!sql if $i>dnl
	B_LIST_TR
		B_LIST_ITEM
			<!sql if (@q_cls.Id == $kwdid)>&nbsp; <!sql endif><!sql print ~q_cls.Name>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
<!sql query "SELECT Name FROM Languages WHERE Id=@q_cls.IdLanguage" l>dnl
			<!sql print_rows l "~l.0">&nbsp;
<!sql free l>dnl
		E_LIST_ITEM
	<!sql if ?mca != 0> 
		B_LIST_ITEM({CENTER})
<!sql if (@q_cls.Id != $kwdid)>dnl
			<A HREF="X_ROOT/classes/translate.xql?Class=<!sql print #q_cls.Id>">Translate</A>
<!sql endif>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			X_BUTTON({Delete dictionary class <!sql print ~q_cls.Name>}, {icon/x.gif}, {classes/del.xql?Class=<!sql print #q_cls.Id>&Lang=<!sql print #q_cls.IdLanguage>})
		E_LIST_ITEM
	<!sql endif>
<!sql if (@q_cls.Id != $kwdid)>dnl
<!sql setexpr kwdid @q_cls.Id>dnl
<!sql endif>dnl
	E_LIST_TR
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($ClsOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({index.xql?sName=<!sql print #sName>&sLang=<!sql print #sLang>&ClsOffs=<!sql eval ($ClsOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({index.xql?sName=<!sql print #sName>&sLang=<!sql print #sLang>&ClsOffs=<!sql eval ($ClsOffs + 10)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No dictionary classes.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
