B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageUsers})

B_HEAD
	X_EXPIRES
	X_TITLE({Adding New IP Group})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add IP Groups.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Adding New User Account})
B_HEADER_BUTTONS
X_HBUTTON({IP Access List}, {users/ipaccesslist.xql?User=<!sql print #User>})
X_HBUTTON({Users}, {users/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault User "">dnl
<!sql setdefault cStartIP1 "">dnl
<!sql setdefault cStartIP2 "">dnl
<!sql setdefault cStartIP3 "">dnl
<!sql setdefault cStartIP4 "">dnl
<!sql setdefault cAddresses "">dnl
<!sql setdefault UName "">dnl
<!sql query "SELECT Name FROM Users WHERE Id = ?User" uname>

<!sql set correct 1><!sql set created 0>dnl
<P>
B_MSGBOX({Adding new IP Group})
	X_MSGBOX_TEXT({
<!sql if ($cStartIP1 == "") || ($cStartIP2 == "") || ($cStartIP3 == "") || ($cStartIP4 == "")>
<!sql set correct 0>
		<LI>You must complete the <B>Start IP</B> fields.</LI>
<!sql endif>dnl

<!sql if ($cAddresses == "")>dnl
<!sql set correct 0>dnl
		<LI>You must complete the <B>Number of addresses</B> field.</LI>
<!sql endif>dnl

<!sql set NUM_ROWS 0>dnl
<!sql if $correct>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "INSERT IGNORE INTO SubsByIP SET IdUser=?User, StartIP="?cStartIP1*256*256*256+?cStartIP2*256*256+?cStartIP3*256+?cStartIP4", Addresses=?cAddresses">dnl
<!sql setexpr created ($AFFECTED_ROWS != 0)>dnl
<!sql endif>dnl

<!sql if $created>dnl
		<LI>The IP Group <B><!sql print ~cStartIP1.~cStartIP2.~cStartIP3.~cStartIP4:~cAddresses></B> has been created.</LI>
X_AUDIT({57}, {IP Group ~cStartIP1.~cStartIP2.~cStartIP3.~cStartIP4:~cAddresses added for user ~uname.0})
<!sql else>dnl
<!sql if ($correct != 0)>dnl
		<LI>The IP Group could not be created.<LI></LI>Please check if an account with the same IP Group does not already exist.</LI>
<!sql endif>
<!sql endif>
        })

<!sql if $correct && $created>dnl
	B_MSGBOX_BUTTONS
<!sql query "SELECT LAST_INSERT_ID()" lid>dnl
		<A HREF="X_ROOT/users/ipadd.xql?User=<!sql print #User>"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another IP Group"></A>
		<A HREF="X_ROOT/users/ipaccesslist.xql?User=<!sql print #User>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<!sql else>
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/users/ipaccesslist.xql?User=<!sql print #User>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
	E_MSGBOX_BUTTONS
<!sql endif>dnl
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
