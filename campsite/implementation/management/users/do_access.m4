B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Updating user account permissions*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change user account permissions.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Updating user account permissions*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todefnum('User');

todefradio('cManagePub');
todefradio('cDeletePub');
todefradio('cManageIssue');
todefradio('cDeleteIssue');
todefradio('cManageSection');
todefradio('cDeleteSection');
todefradio('cAddArticle');
todefradio('cChangeArticle');
todefradio('cDeleteArticle');
todefradio('cAddImage');
todefradio('cChangeImage');
todefradio('cDeleteImage');
todefradio('cManageTempl');
todefradio('cDeleteTempl');
todefradio('cManageUsers');
todefradio('cManageSubscriptions');
todefradio('cDeleteUsers');
todefradio('cManageUserTypes');
todefradio('cManageArticleTypes');
todefradio('cDeleteArticleTypes');
todefradio('cManageLanguages');
todefradio('cDeleteLanguages');
todefradio('cMailNotify');
todefradio('cManageClasses');
todefradio('cManageDictionary');
todefradio('cDeleteDictionary');
todefradio('cManageCountries');
todefradio('cDeleteCountries');
todefradio('cViewLogs');
todefradio('cManageLocalizer');
todefradio('cManageIndexer');
todefradio('cPublish');
todefradio('cManageTopics');



    query ("SELECT * FROM Users WHERE Id=$User", 'uacc');
    if ($NUM_ROWS) {
	query ("SELECT * FROM UserPerm WHERE IdUser=$User", 'uperm');
	if ($NUM_ROWS) { 
	    fetchRow($uacc);
	    fetchRow($uperm);
	
	?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<B><? pgetHVar($uacc,'UName'); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Updating user account permissions*>)
<?
    query ("UPDATE UserPerm SET ManagePub='$cManagePub', DeletePub='$cDeletePub', ManageIssue='$cManageIssue', DeleteIssue='$cDeleteIssue', ManageSection='$cManageSection', DeleteSection='$cDeleteSection', AddArticle='$cAddArticle', ChangeArticle='$cChangeArticle', DeleteArticle='$cDeleteArticle', AddImage='$cAddImage', ChangeImage='$cChangeImage', DeleteImage='$cDeleteImage', ManageTempl='$cManageTempl', DeleteTempl='$cDeleteTempl', ManageUsers='$cManageUsers', ManageSubscriptions='$cManageSubscriptions', DeleteUsers='$cDeleteUsers', ManageUserTypes='$cManageUserTypes', ManageArticleTypes='$cManageArticleTypes', DeleteArticleTypes='$cDeleteArticleTypes', ManageLanguages='$cManageLanguages', DeleteLanguages='$cDeleteLanguages', MailNotify='$cMailNotify', ManageClasses='$cManageClasses', ManageDictionary='$cManageDictionary', DeleteDictionary='$cDeleteDictionary', ManageCountries='$cManageCountries', DeleteCountries='$cDeleteCountries', ViewLogs='$cViewLogs' , ManageLocalizer = '$cManageLocalizer', ManageIndexer = '$cManageIndexer', Publish = '$cPublish', ManageTopics= '$cManageTopics' WHERE IdUser=$User");
    if ($AFFECTED_ROWS > 0) { ?>dnl
X_AUDIT(<*55*>, <*getGS('Permissions for $1 changed',getHVar($uacc,'UName'))*>)
	X_MSGBOX_TEXT(<*<LI><? putGS('User account permissions have been successfuly updated.'); ?></LI>*>)
<? } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('User account permissions could not be updated.'); ?></LI>
<? } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<?
    if ($AFFECTED_ROWS > 0) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/users/*>)
<? } else { ?>dnl
		REDIRECT(<OK**>, <*OK*>, <*X_ROOT/users/access.php?User=<? pencURL($User); ?>*>)
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('This user account does not have permissions information.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such user account.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
