B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageUserTypes})

B_HEAD
	X_EXPIRES
	X_TITLE({Updating User Types Permissions})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to change user type permissions.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Updating User Types Permissions})
B_HEADER_BUTTONS
X_HBUTTON({User Types}, {u_types/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault UType 0>dnl
<!sql setdefault cName "">dnl
<!sql setdefault cReader "">dnl
<!sql setdefault cManagePub "">dnl
<!sql setdefault cDeletePub "">dnl
<!sql setdefault cManageIssue "">dnl
<!sql setdefault cDeleteIssue "">dnl
<!sql setdefault cManageSection "">dnl
<!sql setdefault cDeleteSection "">dnl
<!sql setdefault cAddArticle "">dnl
<!sql setdefault cChangeArticle "">dnl
<!sql setdefault cDeleteArticle "">dnl
<!sql setdefault cAddImage "">dnl
<!sql setdefault cChangeImage "">dnl
<!sql setdefault cDeleteImage "">dnl
<!sql setdefault cManageTempl "">dnl
<!sql setdefault cDeleteTempl "">dnl
<!sql setdefault cManageUsers "">dnl
<!sql setdefault cManageSubscriptions "">dnl
<!sql setdefault cDeleteUsers "">dnl
<!sql setdefault cManageUserTypes "">dnl
<!sql setdefault cManageArticleTypes "">dnl
<!sql setdefault cDeleteArticleTypes "">dnl
<!sql setdefault cManageLanguages "">dnl
<!sql setdefault cDeleteLanguages "">dnl
<!sql setdefault cManageClasses "">dnl
<!sql setdefault cMailNotify "">dnl
<!sql setdefault cManageDictionary "">dnl
<!sql setdefault cDeleteDictionary "">dnl
<!sql setdefault cManageCountries "">dnl
<!sql setdefault cDeleteCountries "">dnl
<!sql setdefault cViewLogs "">dnl
<!sql if $cReader == "on"><!sql set cReader "Y"><!sql else><!sql set cReader "N"><!sql endif>dnl
<!sql if $cManagePub == "on"><!sql set cManagePub "Y"><!sql else><!sql set cManagePub "N"><!sql endif>dnl
<!sql if $cDeletePub == "on"><!sql set cDeletePub "Y"><!sql else><!sql set cDeletePub "N"><!sql endif>dnl
<!sql if $cManageIssue == "on"><!sql set cManageIssue "Y"><!sql else><!sql set cManageIssue "N"><!sql endif>dnl
<!sql if $cDeleteIssue == "on"><!sql set cDeleteIssue "Y"><!sql else><!sql set cDeleteIssue "N"><!sql endif>dnl
<!sql if $cManageSection == "on"><!sql set cManageSection "Y"><!sql else><!sql set cManageSection "N"><!sql endif>dnl
<!sql if $cDeleteSection == "on"><!sql set cDeleteSection "Y"><!sql else><!sql set cDeleteSection "N"><!sql endif>dnl
<!sql if $cAddArticle == "on"><!sql set cAddArticle "Y"><!sql else><!sql set cAddArticle "N"><!sql endif>dnl
<!sql if $cChangeArticle == "on"><!sql set cChangeArticle "Y"><!sql else><!sql set cChangeArticle "N"><!sql endif>dnl
<!sql if $cDeleteArticle == "on"><!sql set cDeleteArticle "Y"><!sql else><!sql set cDeleteArticle "N"><!sql endif>dnl
<!sql if $cAddImage == "on"><!sql set cAddImage "Y"><!sql else><!sql set cAddImage "N"><!sql endif>dnl
<!sql if $cChangeImage == "on"><!sql set cChangeImage "Y"><!sql else><!sql set cChangeImage "N"><!sql endif>dnl
<!sql if $cDeleteImage == "on"><!sql set cDeleteImage "Y"><!sql else><!sql set cDeleteImage "N"><!sql endif>dnl
<!sql if $cManageTempl == "on"><!sql set cManageTempl "Y"><!sql else><!sql set cManageTempl "N"><!sql endif>dnl
<!sql if $cDeleteTempl == "on"><!sql set cDeleteTempl "Y"><!sql else><!sql set cDeleteTempl "N"><!sql endif>dnl
<!sql if $cManageUsers == "on"><!sql set cManageUsers "Y"><!sql else><!sql set cManageUsers "N"><!sql endif>dnl
<!sql if $cManageSubscriptions == "on"><!sql set cManageSubscriptions "Y"><!sql else><!sql set cManageSubscriptions "N"><!sql endif>dnl
<!sql if $cDeleteUsers == "on"><!sql set cDeleteUsers "Y"><!sql else><!sql set cDeleteUsers "N"><!sql endif>dnl
<!sql if $cManageUserTypes == "on"><!sql set cManageUserTypes "Y"><!sql else><!sql set cManageUserTypes "N"><!sql endif>dnl
<!sql if $cManageArticleTypes == "on"><!sql set cManageArticleTypes "Y"><!sql else><!sql set cManageArticleTypes "N"><!sql endif>dnl
<!sql if $cDeleteArticleTypes == "on"><!sql set cDeleteArticleTypes "Y"><!sql else><!sql set cDeleteArticleTypes "N"><!sql endif>dnl
<!sql if $cManageLanguages == "on"><!sql set cManageLanguages "Y"><!sql else><!sql set cManageLanguages "N"><!sql endif>dnl
<!sql if $cDeleteLanguages == "on"><!sql set cDeleteLanguages "Y"><!sql else><!sql set cDeleteLanguages "N"><!sql endif>dnl
<!sql if $cMailNotify == "on"><!sql set cMailNotify "Y"><!sql else><!sql set cMailNotify "N"><!sql endif>dnl
<!sql if $cManageClasses == "on"><!sql set cManageClasses "Y"><!sql else><!sql set cManageClasses "N"><!sql endif>dnl
<!sql if $cManageDictionary == "on"><!sql set cManageDictionary "Y"><!sql else><!sql set cManageDictionary "N"><!sql endif>dnl
<!sql if $cDeleteDictionary == "on"><!sql set cDeleteDictionary "Y"><!sql else><!sql set cDeleteDictionary "N"><!sql endif>dnl
<!sql if $cManageCountries == "on"><!sql set cManageCountries "Y"><!sql else><!sql set cManageCountries "N"><!sql endif>dnl
<!sql if $cDeleteCountries == "on"><!sql set cDeleteCountries "Y"><!sql else><!sql set cDeleteCountries "N"><!sql endif>dnl
<!sql if $cViewLogs == "on"><!sql set cViewLogs "Y"><!sql else><!sql set cViewLogs "N"><!sql endif>dnl

<P>
B_MSGBOX({Updating user type permissions})
<!sql if $cName != "">dnl
	<!sql if $UType != $cName>dnl
		<!sql query "SELECT COUNT(*) FROM UserTypes WHERE Name='?cName'" c>dnl
		<!sql setexpr ok (@c.0 == 0)>dnl
		<!sql free c>dnl
	<!sql else>dnl
		<!sql set ok 1>dnl
	<!sql endif>dnl
	<!sql if $ok>dnl
		<!sql set AFFECTED_ROWS 0>dnl
		<!sql query "UPDATE UserTypes SET Name='?cName', Reader='?cReader', ManagePub='?cManagePub', DeletePub='?cDeletePub', ManageIssue='?cManageIssue', DeleteIssue='?cDeleteIssue', ManageSection='?cManageSection', DeleteSection='?cDeleteSection', AddArticle='?cAddArticle', ChangeArticle='?cChangeArticle', DeleteArticle='?cDeleteArticle', AddImage='?cAddImage', ChangeImage='?cChangeImage', DeleteImage='?cDeleteImage', ManageTempl='?cManageTempl', DeleteTempl='?cDeleteTempl', ManageUsers='?cManageUsers', ManageSubscriptions='?cManageSubscriptions', DeleteUsers='?cDeleteUsers', ManageUserTypes='?cManageUserTypes', ManageArticleTypes='?cManageArticleTypes', DeleteArticleTypes='?cDeleteArticleTypes', ManageLanguages='?cManageLanguages', DeleteLanguages='?cDeleteLanguages', MailNotify='?cMailNotify', ManageClasses='?cManageClasses', ManageDictionary='?cManageDictionary', DeleteDictionary='?cDeleteDictionary', ManageCountries='?cManageCountries', DeleteCountries='?cDeleteCountries', ViewLogs='?cViewLogs' WHERE Name='?UType'">dnl
		<!sql setexpr ok $AFFECTED_ROWS>dnl
	<!sql endif>dnl
<!sql else>dnl
	<!sql set ok 0>dnl
<!sql endif>dnl

<!sql if $ok>dnl
	X_MSGBOX_TEXT({<LI>User type permissions have been successfuly updated.</LI>})
X_AUDIT({123}, {User type ~cName changed permissions})
<!sql else>dnl
	X_MSGBOX_TEXT({<LI>User type permissions could not be updated.</LI>
<!sql if $cName == "">dnl
	<LI>You must complete the <B>Name</B> field.<LI>
<!sql endif>dnl
	})
<!sql endif>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/u_types/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
