B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new user account*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to create user accounts.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Adding new user account*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER
<?
todef('cName');
todef('cTitle');
todef('cGender');
todef('cAge');
todef('cUName');
todef('cPass1');
todef('cPass2');
todef('cEMail');
todef('cCity');
todef('cStrAddress');
todef('cState');
todef('cCountryCode');
todef('cPhone');
todef('cFax');
todef('cContact');
todef('cPhone2');
todef('cPostalCode');
todef('cEmployer');
todef('cEmployerType');
todef('cPosition');
todef('cType');

$correct= 1;
$created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new user account*>)
	X_MSGBOX_TEXT(<*
<?
    if ($cName == "") {
	$correct= 0; ?>
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Full Name').'</B>'); ?></LI>
<?
    }
    if ($cCountryCode == "") {
	$correct= 0; ?>
		<LI><? putGS('You must select a $1','<B>'.getGS('Country').'</B>'); ?>.</LI>
<? 
    }
    if ($cUName == "") {
	$correct= 0; ?>dnl
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('User Name').'</B>'); ?></LI>
<?
    }
    
    if ($correct)
	$correct=(($cPass1==$cPass2)&&(strlen(decS($cPass1))>=6));

    if ($correct == 0) { ?>dnl
	<LI><? putGS('The password must be at least 6 characters long and both passwords should match.'); ?></LI>
<?
    }
    
    query ("SELECT * FROM UserTypes where Name='$cType'", 'utype');
    if ($NUM_ROWS == 0) { 
    ?>dnl
	<LI><? putGS('You must select an user type.'); ?></LI>
<?
    $correct= 0;
    }

    if ($correct) {
    fetchRow($utype);
	query ("INSERT IGNORE INTO Users SET Name='$cName', Title='$cTitle', Gender='$cGender', Age='$cAge', UName='$cUName', Password=password('$cPass1'), EMail='$cEMail', City='$cCity', StrAddress='$cStrAddress', State='$cState', CountryCode='$cCountryCode', Phone='$cPhone', Fax='$cFax', Contact='$cContact', Phone2='$cPhone2', PostalCode='$cPostalCode', Employer='$cEmployer', EmployerType='$cEmployerType', Position='$cPosition', Reader='".getSVar($utype,'Reader')."'");
	$created= ($AFFECTED_ROWS > 0);
	if ($created) {
	    if (getVar($utype,'Reader') == "N") {
		query ("INSERT INTO UserPerm SET IdUser=LAST_INSERT_ID(), ManagePub='".getSVar($utype,'ManagePub')."', DeletePub='".getSVar($utype,'DeletePub')."', ManageIssue='".getSVar($utype,'ManageIssue')."', DeleteIssue='".getSVar($utype,'DeleteIssue')."', ManageSection='".getSVar($utype,'ManageSection')."', DeleteSection='".getSVar($utype,'DeleteSection')."', AddArticle='".getSVar($utype,'AddArticle')."', ChangeArticle='".getSVar($utype,'ChangeArticle')."', DeleteArticle='".getSVar($utype,'DeleteArticle')."', AddImage='".getSVar($utype,'AddImage')."', ChangeImage='".getSVar($utype,'ChangeImage')."', DeleteImage='".getSVar($utype,'DeleteImage')."', ManageTempl='".getSVar($utype,'ManageTempl')."', DeleteTempl='".getSVar($utype,'DeleteTempl')."', ManageUsers='".getSVar($utype,'ManageUsers')."', ManageSubscriptions='".getSVar($utype,'ManageSubscriptions')."', DeleteUsers='".getSVar($utype,'DeleteUsers')."', ManageUserTypes='".getSVar($utype,'ManageUserTypes')."', ManageArticleTypes='".getSVar($utype,'ManageArticleTypes')."', DeleteArticleTypes='".getSVar($utype,'DeleteArticleTypes')."', ManageLanguages='".getSVar($utype,'ManageLanguages')."', DeleteLanguages='".getSVar($utype,'DeleteLanguages')."', ManageDictionary='".getSVar($utype,'ManageDictionary')."', DeleteDictionary='".getSVar($utype,'DeleteDictionary')."', ViewLogs='".getSVar($utype,'ViewLogs')."'"); ?>dnl
	    <? } ?>dnl
X_AUDIT(<*51*>, <*getGS('User account $1 created', encHTML(decS($cName)))*>)
<? }
    }

    if ($created) { ?>dnl
		<LI><? putGS('The user account $1 has been created.','<B>'.encHTML(decS($cUName)).'</B>'); ?></LI>
X_AUDIT(<*51*>, <*getGS('User account $1 created', encHTML(decS($cUName)))*>)
<? } else {

    if ($correct != 0) { ?>dnl
		<LI><? putGS('The user account could not be created.'); ?><LI></LI><? putGS('Please check if an account with the same user name does not already exist.'); ?></LI>
<?}
}
?>dnl
		*>)
<?
    if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
<?
    query ("SELECT LAST_INSERT_ID()", 'lid');
    fetchRowNum($lid);
    todef('Back');
    ?>dnl
		<A HREF="X_ROOT/users/add.php<? if ($Back != "") { ?>?Back=<? pencURL($Back); ?><? } ?>"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another user account"></A>
<? if (getVar($utype,'Reader') == "Y") { ?>dnl
		<A HREF="X_ROOT/users/subscriptions/?User=<? pencURL(getNumVar($lid,0)); ?>"><IMG SRC="X_ROOT/img/button/subscriptions.gif" BORDER="0" ALT="Edit user's subscriptions"></A>
<? } ?>dnl
		<A HREF="X_ROOT/users/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<? } else { ?>
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/users/add.php?cName=<? pencURL($cName); ?>&cTitle=<? pencURL($cTitle); ?>&cGender=<? pencURL($cGender); ?>&cAge=<? pencURL($cAge); ?>&cUName=<? pencURL($cUName); ?>&cPass1=<? pencURL($cPass1); ?>&cPass2=<? pencURL($cPass2); ?>&cEMail=<? pencURL($cEMail); ?>&cCity=<? pencURL($cCity); ?>&cStrAddress=<? pencURL($cStrAddress); ?>&cState=<? pencURL($cState); ?>&cCountryCode=<? pencURL($cCountryCode); ?>&cPhone=<? pencURL($cPhone); ?>&cFax=<? pencURL($cFax); ?>&cContact=<? pencURL($cContact); ?>&cPhone2=<? pencURL($cPhone2); ?>&cPostalCode=<? pencURL($cPostalCode); ?>&cEmployer=<? pencURL($cEmployer); ?>&cEmployerType=<? pencURL($cEmployerType); ?>&cPosition=<? pencURL($cPosition); ?>&cType=<? pencURL($cType); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
	E_MSGBOX_BUTTONS
<? } ?>dnl
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
