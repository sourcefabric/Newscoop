B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Countries})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
<!sql query "SELECT * FROM Countries WHERE 1=0" q_countries>dnl
<!sql query "SELECT Id, Name FROM Languages WHERE 1=0" ls>dnl
E_HEAD

<!sql if $access>dnl
SET_ACCESS({mca}, {ManageCountries})
SET_ACCESS({dca}, {DeleteCountries})

B_STYLE
E_STYLE

B_BODY

<!sql setdefault sLanguage 0>dnl
B_HEADER({Countries})
B_HEADER_BUTTONS
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<P><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<!sql if ?mca != 0>
	<TD>X_NEW_BUTTON({Add new country}, {add.xql?Back=<!sql print #REQUEST_URI>})</TD>
	<!sql endif>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG({GET}, {index.xql})
		<TD>Language:</TD>
		<TD><SELECT NAME="sLanguage"><OPTION><!sql query "SELECT Id, Name FROM Languages ORDER BY Name" ls><!sql print_loop ls><OPTION VALUE="<!sql print ~ls.Id>"<!sql if @ls.Id == $sLanguage> SELECTED<!sql endif>><!sql print ~ls.Name><!sql done><!sql free ls></SELECT></TD>
		<TD><INPUT TYPE="IMAGE" SRC="X_ROOT/img/button/search.gif" BORDER="0"></TD>
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<!sql if $sLanguage>dnl
<!sql set ll " AND IdLanguage=?sLanguage">dnl
<!sql set oo ", IdLanguage">dnl
<!sql else>dnl
<!sql set ll "">dnl
<!sql set oo "">dnl
<!sql endif>dnl

<!sql set kwdid "ssssssssss">dnl
<!sql setdefault CtrOffs 0><!sql if $CtrOffs < 0><!sql set CtrOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Countries WHERE Code != \"\"$ll ORDER BY Code$oo LIMIT $CtrOffs, 11" q_countries>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
<!sql if ?mca != 0>dnl
		X_LIST_TH({Name<BR><SMALL>(click to edit)</SMALL>})
<!sql else>dnl
		X_LIST_TH({Name})
<!sql endif>dnl
		X_LIST_TH({Language}, {1%})
		X_LIST_TH({Code}, {1%})
<!sql if ?mca != 0>dnl
		X_LIST_TH({Translate}, {1%})
<!sql endif>dnl
<!sql if ?dca != 0>
		X_LIST_TH({Delete}, {1%})
<!sql endif>dnl
	E_LIST_HEADER
<!sql print_loop q_countries>dnl
<!sql if $i>dnl
	B_LIST_TR
<!sql if ?mca != 0>dnl
		B_LIST_ITEM
			<!sql if @q_countries.Code == $kwdid>&nbsp;<!sql endif><A HREF="X_ROOT/country/edit.xql?Code=<!sql print #q_countries.Code>&Language=<!sql print #q_countries.IdLanguage>"><!sql print ~q_countries.Name>&nbsp;</A>
		E_LIST_ITEM
<!sql else>dnl
		B_LIST_ITEM
			<!sql if @q_countries.Code == $kwdid>&nbsp;<!sql endif><!sql print ~q_countries.Name>&nbsp;
		E_LIST_ITEM
<!sql endif>dnl
		B_LIST_ITEM
<!sql query "SELECT Name FROM Languages WHERE Id=?q_countries.IdLanguage" q_ail>dnl
			<!sql print ~q_ail.Name>
<!sql free q_ail>dnl
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
<!sql if (@q_countries.Code != $kwdid)>dnl
			<!sql print ~q_countries.Code>
<!sql else>dnl
&nbsp;
<!sql endif>dnl	
		E_LIST_ITEM
	<!sql if ?mca != 0> 
		B_LIST_ITEM({CENTER})
<!sql if (@q_countries.Code != $kwdid)>dnl
			<A HREF="X_ROOT/country/translate.xql?Code=<!sql print #q_countries.Code>&Language=<!sql print #q_countries.IdLanguage>">Translate</A>
<!sql else>dnl
&nbsp;
<!sql endif>dnl	
		E_LIST_ITEM
	<!sql endif>
	<!sql if ?dca != 0> 
		B_LIST_ITEM({CENTER})
			X_BUTTON({Delete country <!sql print ~q_countries.Name>}, {icon/x.gif}, {country/del.xql?Code=<!sql print #q_countries.Code>&Language=<!sql print #q_countries.IdLanguage>})
		E_LIST_ITEM
	<!sql endif>
	E_LIST_TR
<!sql set kwdid $q_countries.Code>
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($CtrOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({index.xql?sLanguage=<!sql print #sLanguage>&CtrOffs=<!sql eval ($CtrOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({index.xql?sLanguage=<!sql print #sLanguage>&CtrOffs=<!sql eval ($CtrOffs + 10)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No countries.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
