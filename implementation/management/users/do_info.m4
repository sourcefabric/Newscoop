B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageUsers})

B_HEAD
	X_EXPIRES
	X_TITLE({Changing User Account Information})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to change user account information.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Changing User Account Information})
B_HEADER_BUTTONS
X_HBUTTON({Users}, {users/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault Name "">dnl
<!sql setdefault Title "">dnl
<!sql setdefault Gender "">dnl
<!sql setdefault Age "">dnl
<!sql setdefault EMail "">dnl
<!sql setdefault City "">dnl
<!sql setdefault StrAddress "">dnl
<!sql setdefault State "">dnl
<!sql setdefault CountryCode "">dnl
<!sql setdefault Phone "">dnl
<!sql setdefault Fax "">dnl
<!sql setdefault Contact "">dnl
<!sql setdefault Phone2 "">dnl
<!sql setdefault PostalCode "">dnl
<!sql setdefault Employer "">dnl
<!sql setdefault EmployerType "">dnl
<!sql setdefault Position "">dnl
<!sql setdefault User 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Users WHERE Id=?User" users>dnl
<!sql if $NUM_ROWS>dnl

B_CURRENT
X_CURRENT({User account:}, {<B><!sql print ~users.UName></B>})
E_CURRENT

<!sql set correct 1><!sql set changed 0>dnl
<P>
B_MSGBOX({Changing user account information})
	X_MSGBOX_TEXT({
<!sql if $Name == "">dnl
<!sql set correct 0>dnl
		<LI>The <B>Name</B> field must not be void.</LI>
<!sql endif>dnl
<!sql if $correct>dnl
	<!sql set AFFECTED_ROWS 0>dnl
	<!sql query "UPDATE Users SET Name='?Name', Title='?Title', Gender='?Gender', Age='?Age', EMail='?EMail', City='?City', StrAddress='?StrAddress', State='?State', CountryCode='?CountryCode', Phone='?Phone', Fax='?Fax', Contact='?Contact', Phone2='?Phone2', PostalCode='?PostalCode', Employer='?Employer', EmployerType='?EmployerType', Position='?Position' WHERE Id=?User">
	<!sql setexpr changed $AFFECTED_ROWS>dnl
	<!sql if $changed>dnl
		<LI>User account information has been changed.</LI>
X_AUDIT({56}, {User account iformation changed for ~users.UName})
	<!sql else>dnl
		<LI>User account information could not be changed.</LI>
	<!sql endif>dnl
<!sql endif>dnl
	})
	B_MSGBOX_BUTTONS
<!sql if $changed>dnl
		<A HREF="X_ROOT/users/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>dnl
		<A HREF="X_ROOT/users/"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such user.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
