B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUserTypes*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Updating user types permissions*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change user type permissions.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Updating user type permissions*>)
B_HEADER_BUTTONS
X_HBUTTON(<*User Types*>, <*u_types/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER
<?
todef('UType');

todef('cName');

todefradio('cReader');
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
todefradio('cManageClasses');
todefradio('cMailNotify');
todefradio('cManageDictionary');
todefradio('cDeleteDictionary');
todefradio('cManageCountries');
todefradio('cDeleteCountries');
todefradio('cViewLogs');
todefradio('cManageLocalizer');
todefradio('cManageIndexer');
todefradio('cPublish');
todefradio('cManageTopics');

?>


<P>
B_MSGBOX(<*Updating user type permissions*>)
<? if ($cName != "") {
	if ($UType != $cName) {
		query ("SELECT COUNT(*) FROM UserTypes WHERE Name='$cName'", 'c');
		fetchRowNum($c);
		$ok = (getNumVar($c,0) == 0);
	}
	else
		$ok= 1;
	if ($ok) {
		query ("UPDATE UserTypes SET Name='$cName', Reader='$cReader', ManagePub='$cManagePub', DeletePub='$cDeletePub', ManageIssue='$cManageIssue', DeleteIssue='$cDeleteIssue', ManageSection='$cManageSection', DeleteSection='$cDeleteSection', AddArticle='$cAddArticle', ChangeArticle='$cChangeArticle', DeleteArticle='$cDeleteArticle', AddImage='$cAddImage', ChangeImage='$cChangeImage', DeleteImage='$cDeleteImage', ManageTempl='$cManageTempl', DeleteTempl='$cDeleteTempl', ManageUsers='$cManageUsers', ManageSubscriptions='$cManageSubscriptions', DeleteUsers='$cDeleteUsers', ManageUserTypes='$cManageUserTypes', ManageArticleTypes='$cManageArticleTypes', DeleteArticleTypes='$cDeleteArticleTypes', ManageLanguages='$cManageLanguages', DeleteLanguages='$cDeleteLanguages', MailNotify='$cMailNotify', ManageClasses='$cManageClasses', ManageDictionary='$cManageDictionary', DeleteDictionary='$cDeleteDictionary', ManageCountries='$cManageCountries', DeleteCountries='$cDeleteCountries', ViewLogs='$cViewLogs', ManageLocalizer = '$cManageLocalizer', ManageIndexer = '$cManageIndexer', Publish = '$cPublish', ManageTopics= '$cManageTopics' WHERE Name='$UType'");
		$ok= $AFFECTED_ROWS > 0;
	}
} else
	$ok= 0;
if ($ok) { ?>
	X_MSGBOX_TEXT(<*<LI><? putGS('User type permissions have been successfuly updated.'); ?></LI>*>)
X_AUDIT(<*123*>, <*getGS('User type $1 changed permissions',$cName)*>)
<? } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('User type permissions could not be updated.'); ?></LI>
<? if ($cName == "") { ?>dnl
	<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?><LI>
<? } ?>dnl
	*>)
<? } ?>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/u_types/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

