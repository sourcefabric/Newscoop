B_HTML
B_DATABASE

<!sql set ok 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Id FROM Users WHERE UName='?UserName' AND Password=PASSWORD('?UserPassword') AND Reader='N'" q>dnl
<!sql if $NUM_ROWS>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "UPDATE Users SET KeyId=RAND()*1000000000+RAND()*1000000+RAND()*1000 WHERE Id=?q.Id">dnl
<!sql setexpr ok $AFFECTED_ROWS>dnl
<!sql if $ok>dnl
<!sql query "SELECT Id, KeyId FROM Users WHERE Id=?q.Id" usrs>dnl
<!sql endif>dnl
<!sql endif>dnl
<!sql free q>dnl

B_HEAD
	X_EXPIRES
<!sql if $ok == 0>dnl
	X_TITLE({Login Failed})
<!sql else>dnl
	X_TITLE({Login})
	X_REFRESH({0; URL=X_ROOT/})
	X_COOKIE({TOL_UserId=<!sql print $usrs.Id>})
	X_COOKIE({TOL_UserKey=<!sql print $usrs.KeyId>})
	<!sql endif>
E_HEAD

<!sql if $ok == 0>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Login Failed})
B_HEADER_BUTTONS
X_HBUTTON({Login}, {login.xql})
E_HEADER_BUTTONS
E_HEADER

<BLOCKQUOTE>
	<LI>Login failed.</LI>
	<LI>Pease make sure that you typed the correct user name and password.</LI>
	<LI>If your problem persists please contact the site administrator <A HREF="mailto:<!sql print #SERVER_ADMIN>"><!sql print ~SERVER_ADMIN></A></LI>
</BLOCKQUOTE>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
