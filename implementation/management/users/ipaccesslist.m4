B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({User Management})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
<!sql query "SELECT (StartIP & 0xff000000) >> 24, (StartIP & 0x00ff0000) >> 16, (StartIP & 0x0000ff00) >> 8, StartIP & 0x000000ff, StartIP, Addresses FROM SubsByIP WHERE 1=0" IPs>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({User IP Access List Management})
B_HEADER_BUTTONS
X_HBUTTON({Users}, {users/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault User 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Users WHERE Id=?User" users>dnl
<!sql if $NUM_ROWS>dnl
B_CURRENT
X_CURRENT({User account:}, {<B><!sql print ~users.Name></B>})
E_CURRENT
<P>
<!sql endif>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<TD>X_NEW_BUTTON({Add new IP address group}, {ipadd.xql?User=<!sql print #User>})</TD>
	<TD ALIGN="RIGHT">
	</TD>
</TABLE>

<P><!sql setdefault IPOffs 0><!sql if $IPOffs < 0><!sql set IPOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT (StartIP & 0xff000000) >> 24, (StartIP & 0x00ff0000) >> 16, (StartIP & 0x0000ff00) >> 8, StartIP & 0x000000ff, StartIP, Addresses FROM SubsByIP WHERE IdUser = ?User LIMIT $IPOffs, 11" IPs>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Start IP})
		X_LIST_TH({Number of addresses})
		X_LIST_TH({Delete}, {1%})
	E_LIST_HEADER
<!sql print_loop IPs>dnl
<!sql if $i>dnl
	B_LIST_TR
		B_LIST_ITEM
			<!sql print ~IPs.0.~IPs.1.~IPs.2.~IPs.3>
		E_LIST_ITEM
		B_LIST_ITEM
			<!sql print ~IPs.Addresses>
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			X_BUTTON({Delete IP Group <!sql print ~IPs.StartIP>}, {icon/x.gif}, {users/ipdel.xql?User=<!sql print @User>&StartIP=<!sql print @IPs.StartIP>})
		E_LIST_ITEM
	E_LIST_TR
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($IPOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({ipaccesslist.xql?User=<!sql print #User>&IPOffs=<!sql eval ($IPOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({ipaccesslist.xql?User=<!sql print #User>&IPOffs=<!sql eval ($UserOffs + 10)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No records.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
