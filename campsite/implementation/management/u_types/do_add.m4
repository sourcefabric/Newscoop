B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUserTypes*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new user type*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add user types.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Adding new user type*>)
B_HEADER_BUTTONS
X_HBUTTON(<*User Types*>, <*u_types/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER
<?

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
todefradio('cViewLogs');

$correct=1;
$created=0;
?>dnl
<P>
B_MSGBOX(<*Adding new user type*>)
	X_MSGBOX_TEXT(<*
<? if ($cName == "") {
    $correct=0; ?>
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<? } 
    if ($correct) {
	query ("INSERT IGNORE INTO UserTypes SET Name='$cName', Reader='$cReader', ManagePub='$cManagePub', DeletePub='$cDeletePub', ManageIssue='$cManageIssue', DeleteIssue='$cDeleteIssue', ManageSection='$cManageSection', DeleteSection='$cDeleteSection', AddArticle='$cAddArticle', ChangeArticle='$cChangeArticle', DeleteArticle='$cDeleteArticle', AddImage='$cAddImage', ChangeImage='$cChangeImage', DeleteImage='$cDeleteImage', ManageTempl='$cManageTempl', DeleteTempl='$cDeleteTempl', ManageUsers='$cManageUsers', ManageSubscriptions='$cManageSubscriptions', DeleteUsers='$cDeleteUsers', ManageUserTypes='$cManageUserTypes', ManageArticleTypes='$cManageArticleTypes', DeleteArticleTypes='$cDeleteArticleTypes', ManageLanguages='$cManageLanguages', DeleteLanguages='$cDeleteLanguages', ManageClasses='$cManageClasses', MailNotify='$cMailNotify', ManageDictionary='$cManageDictionary', DeleteDictionary='$cDeleteDictionary', ViewLogs='$cViewLogs' ");
	$created= ($AFFECTED_ROWS > 0);
    }
    if ($created) { ?>dnl
		<LI><? putGS('The user type $1 has been added.','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
X_AUDIT(<*121*>, <*getGS('User type $1 added',encHTML(decS($cName)));*>)
<? } else {
    if ($correct != 0) { ?>dnl
		<LI><? putGS('The user type could not be added.'); ?></LI><LI><? putGS('Please check if an user type with the same name does not already exist.'); ?></LI>
<? }
} ?>dnl
		*>)
	B_MSGBOX_BUTTONS
<? todef('Back');
    if (($correct) && ($created)) {?>dnl
		<A HREF="X_ROOT/u_types/add.php<? if ($Back != "") print '?Back='.encURL($Back); ?>"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another user type"></A>
		<A HREF="X_ROOT/u_types/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } else { ?>
		<A HREF="X_ROOT/u_types/add.php<? if ($Back != "") print '?Back='.encURL($Back); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

