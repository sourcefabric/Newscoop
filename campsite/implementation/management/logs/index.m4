B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ViewLogs})

B_HEAD
	X_EXPIRES
	X_TITLE({Logs})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to view logs.})
<!sql endif>dnl
<!sql query "SELECT Id, Name FROM Events WHERE 1=0" ee>dnl
<!sql query "SELECT TStamp, IdEvent, User, Text FROM Log WHERE 1=0" log>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Logs})
B_HEADER_BUTTONS
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault sEvent 0>dnl
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG({GET}, {index.xql})
		<TD>Event:</TD>
		<TD><SELECT NAME="sEvent"><OPTION VALUE="0"><!sql query "SELECT Id, Name FROM Events ORDER BY Id" ee><!sql print_loop ee><OPTION VALUE="<!sql print ~ee.Id>"<!sql if @ee.Id = $sEvent> SELECTED<!sql endif>><!sql print ~ee.Name><!sql done><!sql free ee></SELECT></TD>
		<TD><INPUT TYPE="IMAGE" SRC="X_ROOT/img/button/search.gif" BORDER="0"></TD>
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<P><!sql setdefault LogOffs 0><!sql if $LogOffs < 0><!sql set LogOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql if $sEvent != 0>dnl
<!sql set ww "WHERE IdEvent = ?sEvent">dnl
<!sql else>dnl
<!sql set ww "">dnl
<!sql endif>dnl
<!sql query "SELECT TStamp, IdEvent, User, Text FROM Log $ww ORDER BY TStamp DESC LIMIT $LogOffs, 11" log>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Date/Time}, {15%})
		X_LIST_TH({User}, {1%})
<!sql if $sEvent == 0>dnl
		X_LIST_TH({Event}, {1%})
<!sql endif>dnl
		X_LIST_TH({Description})
	E_LIST_HEADER
<!sql print_loop log>dnl
<!sql if $i>dnl
	B_LIST_TR
		B_LIST_ITEM(CENTER)
			<!sql print ~log.TStamp>
		E_LIST_ITEM
		B_LIST_ITEM
			<!sql print ~log.User>&nbsp;
		E_LIST_ITEM
<!sql if $sEvent == 0>dnl
		B_LIST_ITEM
			<!sql query "SELECT Name FROM Events WHERE Id=?log.IdEvent" ev><!sql print_rows ev "~ev.0"><!sql free ev>&nbsp;
		E_LIST_ITEM
<!sql endif>dnl
		B_LIST_ITEM
			<!sql print ~log.Text>
		E_LIST_ITEM
	E_LIST_TR
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($LogOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({index.xql?sEvent=<!sql print #sEvent>&LogOffs=<!sql eval ($LogOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({index.xql?sEvent=<!sql print #sEvent>&LogOffs=<!sql eval ($LogOffs + 10)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No events.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
