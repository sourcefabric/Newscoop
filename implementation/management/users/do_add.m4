B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/users*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new user account*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to create user accounts.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Adding new user account*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Users*>, <*users/*>)
E_HEADER_BUTTONS
E_HEADER
<?php 
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
<?php 
    if ($cName == "") {
	$correct= 0; ?>
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Full Name').'</B>'); ?></LI>
<?php 
    }
    if ($cCountryCode == "") {
	$correct= 0; ?>
		<LI><?php  putGS('You must select a $1','<B>'.getGS('Country').'</B>'); ?>.</LI>
<?php  
    }
    if ($cUName == "") {
	$correct= 0; ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('User Name').'</B>'); ?></LI>
<?php 
    }
    
    if ($correct)
	$correct=(($cPass1==$cPass2)&&(strlen(decS($cPass1))>=6));

    if ($correct == 0) { ?>dnl
	<LI><?php  putGS('The password must be at least 6 characters long and both passwords should match.'); ?></LI>
<?php 
    }
    
    query ("SELECT * FROM UserTypes where Name='$cType'", 'utype');
    if ($NUM_ROWS == 0) { 
    ?>dnl
	<LI><?php  putGS('You must select an user type.'); ?></LI>
<?php 
    $correct= 0;
    }

    if ($correct) {
    fetchRow($utype);
	query ("INSERT IGNORE INTO Users SET Name='$cName', Title='$cTitle', Gender='$cGender', Age='$cAge', UName='$cUName', Password=password('$cPass1'), EMail='$cEMail', City='$cCity', StrAddress='$cStrAddress', State='$cState', CountryCode='$cCountryCode', Phone='$cPhone', Fax='$cFax', Contact='$cContact', Phone2='$cPhone2', PostalCode='$cPostalCode', Employer='$cEmployer', EmployerType='$cEmployerType', Position='$cPosition', Reader='".getSVar($utype,'Reader')."'");
	$created= ($AFFECTED_ROWS > 0);
	if ($created) {
	    if (getVar($utype,'Reader') == "N") {
		query ("INSERT INTO UserPerm SET IdUser=LAST_INSERT_ID(), ManagePub='".getSVar($utype,'ManagePub')."', DeletePub='".getSVar($utype,'DeletePub')."', ManageIssue='".getSVar($utype,'ManageIssue')."', DeleteIssue='".getSVar($utype,'DeleteIssue')."', ManageSection='".getSVar($utype,'ManageSection')."', DeleteSection='".getSVar($utype,'DeleteSection')."', AddArticle='".getSVar($utype,'AddArticle')."', ChangeArticle='".getSVar($utype,'ChangeArticle')."', DeleteArticle='".getSVar($utype,'DeleteArticle')."', AddImage='".getSVar($utype,'AddImage')."', ChangeImage='".getSVar($utype,'ChangeImage')."', DeleteImage='".getSVar($utype,'DeleteImage')."', ManageTempl='".getSVar($utype,'ManageTempl')."', DeleteTempl='".getSVar($utype,'DeleteTempl')."', ManageUsers='".getSVar($utype,'ManageUsers')."', ManageSubscriptions='".getSVar($utype,'ManageSubscriptions')."', DeleteUsers='".getSVar($utype,'DeleteUsers')."', ManageUserTypes='".getSVar($utype,'ManageUserTypes')."', ManageArticleTypes='".getSVar($utype,'ManageArticleTypes')."', DeleteArticleTypes='".getSVar($utype,'DeleteArticleTypes')."', ManageLanguages='".getSVar($utype,'ManageLanguages')."', DeleteLanguages='".getSVar($utype,'DeleteLanguages')."', ManageDictionary='".getSVar($utype,'ManageDictionary')."', DeleteDictionary='".getSVar($utype,'DeleteDictionary')."', ViewLogs='".getSVar($utype,'ViewLogs')."', ManageCountries='".getSVar($utype,'ManageCountries')."', DeleteCountries='".getSVar($utype,'DeleteCountries')."', ManageClasses='".getSVar($utype,'ManageClasses')."', MailNotify='".getSVar($utype,'MailNotify')."', ManageLocalizer='".getSVar($utype,'ManageLocalizer')."', ManageIndexer='".getSVar($utype,'ManageIndexer')."', Publish='".getSVar($utype,'Publish')."', ManageTopics='".getSVar($utype,'ManageTopics')."'"); ?>dnl
	    <?php  } ?>dnl
X_AUDIT(<*51*>, <*getGS('User account $1 created', encHTML(decS($cName)))*>)
<?php  }
    }

    if ($created) { ?>dnl
		<LI><?php  putGS('The user account $1 has been created.','<B>'.encHTML(decS($cUName)).'</B>'); ?></LI>
X_AUDIT(<*51*>, <*getGS('User account $1 created', encHTML(decS($cUName)))*>)
<?php  } else {

    if ($correct != 0) { ?>dnl
		<LI><?php  putGS('The user account could not be created.'); ?><LI></LI><?php  putGS('Please check if an account with the same user name does not already exist.'); ?></LI>
<?php }
}
?>dnl
		*>)
<?php 
    if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
<?php 
    query ("SELECT LAST_INSERT_ID()", 'lid');
    fetchRowNum($lid);
    todef('Back');
    ?>dnl
		REDIRECT(<*New*>, <*Add another*>, <*X_ROOT/users/add.php<?php  if ($Back != "") { ?>?Back=<?php  pencURL($Back); ?><?php  } ?>*>)
<?php  if (getVar($utype,'Reader') == "Y") { ?>dnl
		REDIRECT(<*Edit subscr*>, <*Subscriptions*>, <*X_ROOT/users/subscriptions/?User=<?php  pencURL(getNumVar($lid,0)); ?>*>)
<?php  } ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/users/*>)
	E_MSGBOX_BUTTONS
<?php  } else { ?>
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/users/add.php?cName=<?php  pencURL($cName); ?>&cTitle=<?php  pencURL($cTitle); ?>&cGender=<?php  pencURL($cGender); ?>&cAge=<?php  pencURL($cAge); ?>&cUName=<?php  pencURL($cUName); ?>&cPass1=<?php  pencURL($cPass1); ?>&cPass2=<?php  pencURL($cPass2); ?>&cEMail=<?php  pencURL($cEMail); ?>&cCity=<?php  pencURL($cCity); ?>&cStrAddress=<?php  pencURL($cStrAddress); ?>&cState=<?php  pencURL($cState); ?>&cCountryCode=<?php  pencURL($cCountryCode); ?>&cPhone=<?php  pencURL($cPhone); ?>&cFax=<?php  pencURL($cFax); ?>&cContact=<?php  pencURL($cContact); ?>&cPhone2=<?php  pencURL($cPhone2); ?>&cPostalCode=<?php  pencURL($cPostalCode); ?>&cEmployer=<?php  pencURL($cEmployer); ?>&cEmployerType=<?php  pencURL($cEmployerType); ?>&cPosition=<?php  pencURL($cPosition); ?>&cType=<?php  pencURL($cType); ?>*>)
	E_MSGBOX_BUTTONS
<?php  } ?>dnl
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
