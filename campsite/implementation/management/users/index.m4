B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({User Management})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql query "SELECT * FROM Users WHERE 1=0" Users>dnl
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
SET_ACCESS({mua}, {ManageUsers})
SET_ACCESS({dua}, {DeleteUsers})
SET_ACCESS({msa}, {ManageSubscriptions})

B_STYLE
E_STYLE

B_BODY

B_HEADER({User Management})
B_HEADER_BUTTONS
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault sUname ""><!sql setdefault sType "">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<!sql if ?mua != 0>
	<TD>X_NEW_BUTTON({Add new user account}, {add.xql?Back=<!sql print #REQUEST_URI>})</TD>
	<!sql endif>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG({GET}, {index.xql})
		<TD>User name:</TD>
		<TD><INPUT TYPE="TEXT" NAME="sUname" VALUE="<!sql print ~sUname>" SIZE="16" MAXLENGTH="32"></TD>
		<TD><SELECT NAME="sType"><OPTION><OPTION VALUE="Y" <!sql if ($sType == "Y")>SELECTED<!sql endif>>Reader<OPTION VALUE="N" <!sql if ($sType == "N")>SELECTED<!sql endif>>Staff</SELECT></TD>
		<TD><INPUT TYPE="IMAGE" SRC="X_ROOT/img/button/search.gif" BORDER="0"></TD>
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<P><!sql setdefault UserOffs 0><!sql if $UserOffs < 0><!sql set UserOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Users WHERE Name LIKE '%?sUname%' AND Reader LIKE '?sType%' LIMIT $UserOffs, 11" Users>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Full Name})
		X_LIST_TH({User Name})
	<!sql if ?mua != 0>
		X_LIST_TH({IP Access}, {1%}, {nowrap})
		X_LIST_TH({Password}, {1%})
		X_LIST_TH({Reader}, {1%})
		X_LIST_TH({Info}, {1%})
		X_LIST_TH({Rights}, {1%})
	<!sql else>
		X_LIST_TH({Reader}, {1%})
	<!sql endif>
	<!sql if ?dua != 0>
		X_LIST_TH({Delete}, {1%})
	<!sql endif>
	E_LIST_HEADER
<!sql print_loop Users>dnl
<!sql if $i>dnl
	B_LIST_TR
		B_LIST_ITEM
			<!sql print ~Users.Name>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
			<!sql print ~Users.UName>&nbsp;
		E_LIST_ITEM
	<!sql if ?mua != 0>
<!sql query "SELECT COUNT(*) FROM SubsByIP WHERE IdUser=?Users.Id" bip>
		B_LIST_ITEM({CENTER})
                        <A HREF="X_ROOT/users/ipaccesslist.xql?User=<!sql print #Users.Id>"><!sql if ~bip.0>Update<!sql else>Set<!sql endif></A>
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
        		<A HREF="X_ROOT/users/passwd.xql?User=<!sql print #Users.Id>">Password</A>
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			<!sql if @Users.Reader == "Y">Yes<!sql else>No<!sql endif>
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			<A HREF="X_ROOT/users/info.xql?User=<!sql print #Users.Id>">Change</A>
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
<!sql if @Users.Reader == "Y">dnl
		<!sql if ?msa != 0>
			<A HREF="X_ROOT/users/subscriptions/?User=<!sql print #Users.Id>">Subscriptions</A>
		<!sql else>
			&nbsp;
		<!sql endif>
<!sql else>dnl
			<A HREF="X_ROOT/users/access.xql?User=<!sql print #Users.Id>">Rights</A>
<!sql endif>dnl
		E_LIST_ITEM
	<!sql else>
		B_LIST_ITEM({CENTER})
			<!sql if @Users.Reader == "Y">Yes<!sql else>No<!sql endif>
                E_LIST_ITEM  
	<!sql endif>
	<!sql if ?dua != 0>
		B_LIST_ITEM({CENTER})
			X_BUTTON({Delete user <!sql print ~Users.Name>}, {icon/x.gif}, {users/del.xql?User=<!sql print @Users.Id>})
		E_LIST_ITEM
	<!sql endif>
	E_LIST_TR
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($UserOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({index.xql?sUname=<!sql print #sUname>&sType=<!sql print #sType>&UserOffs=<!sql eval ($UserOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({index.xql?sUname=<!sql print #sUname>&sType=<!sql print #sType>&UserOffs=<!sql eval ($UserOffs + 10)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such user account.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
