B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageUsers})

B_HEAD
	X_EXPIRES
	X_TITLE({Adding New User Account})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to create user accounts.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Adding New User Account})
B_HEADER_BUTTONS
X_HBUTTON({Users}, {users/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault cName "">dnl
<!sql setdefault cTitle "">dnl
<!sql setdefault cGender "">dnl
<!sql setdefault cAge "">dnl
<!sql setdefault cUName "">dnl
<!sql setdefault cPass1 "">dnl
<!sql setdefault cPass2 "">dnl
<!sql setdefault cEMail "">dnl
<!sql setdefault cCity "">dnl
<!sql setdefault cStrAddress "">dnl
<!sql setdefault cState "">dnl
<!sql setdefault cCountryCode "">dnl
<!sql setdefault cPhone "">dnl
<!sql setdefault cFax "">dnl
<!sql setdefault cContact "">dnl
<!sql setdefault cPhone2 "">dnl
<!sql setdefault cPostalCode "">dnl
<!sql setdefault cEmployer "">dnl
<!sql setdefault cEmployerType "">dnl
<!sql setdefault cPosition "">dnl
<!sql setdefault cType "">dnl

<!sql set correct 1><!sql set created 0>dnl
<P>
B_MSGBOX({Adding new user account})
	X_MSGBOX_TEXT({
<!sql if ($cName == "")>
<!sql set correct 0>
		<LI>You must complete the <B>Full Name</B> field.</LI>
<!sql endif>dnl
<!sql if ($cCountryCode == "")>
<!sql set correct 0>
		<LI>You must select a <B>Country</B>.</LI>
<!sql endif>dnl
<!sql if ($cUName == "")>dnl
<!sql set correct 0>dnl
		<LI>You must complete the <B>User Name</B> field.</LI>
<!sql endif>dnl
<!sql query "SELECT (STRCMP('?cPass1', '?cPass2') = 0 && LENGTH('?cPass1') >= 6)" pass_ok>dnl
<!sql if $correct>dnl
<!sql setexpr correct @pass_ok.0>dnl
<!sql endif>dnl
<!sql free pass_ok>dnl
<!sql if ($correct == 0)>dnl
	<LI>The password must be at least 6 characters long and both passwords should match.</LI>
<!sql endif>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM UserTypes where Name='?cType'" utype>dnl
<!sql if $NUM_ROWS == 0>dnl
	<LI>You must select an user type.</LI>
<!sql set correct 0>dnl
<!sql endif>dnl
<!sql if $correct>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "INSERT IGNORE INTO Users SET Name='?cName', Title='?cTitle', Gender='?cGender', Age='?cAge', UName='?cUName', Password=password('?cPass1'), EMail='?cEMail', City='?cCity', StrAddress='?cStrAddress', State='?cState', CountryCode='?cCountryCode', Phone='?cPhone', Fax='?cFax', Contact='?cContact', Phone2='?cPhone2', PostalCode='?cPostalCode', Employer='?cEmployer', EmployerType='?cEmployerType', Position='?cPosition', Reader='?utype.Reader'">dnl
<!sql setexpr created ($AFFECTED_ROWS != 0)>dnl
<!sql if $created>dnl
<!sql if @utype.Reader == "N">dnl
<!sql query "INSERT INTO UserPerm SET IdUser=LAST_INSERT_ID(), ManagePub='?utype.ManagePub', DeletePub='?utype.DeletePub', ManageIssue='?utype.ManageIssue', DeleteIssue='?utype.DeleteIssue', ManageSection='?utype.ManageSection', DeleteSection='?utype.DeleteSection', AddArticle='?utype.AddArticle', ChangeArticle='?utype.ChangeArticle', DeleteArticle='?utype.DeleteArticle', AddImage='?utype.AddImage', ChangeImage='?utype.ChangeImage', DeleteImage='?utype.DeleteImage', ManageTempl='?utype.ManageTempl', DeleteTempl='?utype.DeleteTempl', ManageUsers='?utype.ManageUsers', ManageSubscriptions='?utype.ManageSubscriptions', DeleteUsers='?utype.DeleteUsers', ManageUserTypes='?utype.ManageUserTypes', ManageArticleTypes='?utype.ManageArticleTypes', DeleteArticleTypes='?utype.DeleteArticleTypes', ManageLanguages='?utype.ManageLanguages', DeleteLanguages='?utype.DeleteLanguages', ManageDictionary='?utype.ManageDictionary', DeleteDictionary='?utype.DeleteDictionary', ViewLogs='?utype.ViewLogs'">dnl
<!sql endif>dnl
X_AUDIT({51}, {User account ?cName created})
<!sql endif>dnl
<!sql endif>dnl
<!sql if $created>dnl
		<LI>The user account <B><!sql print ~cUName></B> has been created.</LI>
X_AUDIT({51}, {User account ~cUName added})
<!sql else>dnl
<!sql if ($correct != 0)>dnl
		<LI>The user account could not be created.<LI></LI>Please check if an account with the same user name does not already exist.</LI>
<!sql endif>dnl
<!sql endif>dnl
		})
<!sql if $correct && $created>dnl
	B_MSGBOX_BUTTONS
<!sql query "SELECT LAST_INSERT_ID()" lid>dnl
<!sql setdefault Back "">dnl
		<A HREF="X_ROOT/users/add.xql<!sql if $Back != "">?Back=<!sql print #Back><!sql endif>"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another user account"></A>
<!sql if @utype.Reader == "Y">dnl
		<A HREF="X_ROOT/users/subscriptions/?User=<!sql print #lid.0>"><IMG SRC="X_ROOT/img/button/subscriptions.gif" BORDER="0" ALT="Edit user's subscriptions"></A>
<!sql endif>dnl
		<A HREF="X_ROOT/users/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<!sql else>
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/users/add.xql?cName=<!sql print #cName>&cTitle=<!sql print #cTitle>&cGender=<!sql print #cGender>&cAge=<!sql print #cAge>&cUName=<!sql print #cUName>&cPass1=<!sql print #cPass1>&cPass2=<!sql print #cPass2>&cEMail=<!sql print #cEMail>&cCity=<!sql print #cCity>&cStrAddress=<!sql print #cStrAddress>&cState=<!sql print #cState>&cCountryCode=<!sql print #cCountryCode>&cPhone=<!sql print #cPhone>&cFax=<!sql print #cFax>&cContact=<!sql print #cContact>&cPhone2=<!sql print #cPhone2>&cPostalCode=<!sql print #cPostalCode>&cEmployer=<!sql print #cEmployer>&cEmployerType=<!sql print #cEmployerType>&cPosition=<!sql print #cPosition>&cType=<!sql print #cType>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
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
