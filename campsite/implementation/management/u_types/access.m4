B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/u_types*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUserTypes*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Change user type permissions*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change user type permissions.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Change user type permissions*>)
B_HEADER_BUTTONS
X_HBUTTON(<*User Types*>, <*u_types/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<P>
<?php 
    todefnum('UType');
    query ("SELECT * FROM UserTypes WHERE Name='$UType'", 'uperm');
    fetchRow($uperm);
    if ($NUM_ROWS) { ?>
B_DIALOG(<*Change user type permissions*>, <*POST*>, <*do_access.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" VALUE="<?php  encHTML(pgetHVar($uperm,'Name')); ?>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_PACKEDINPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cReader"<?php  checkedIfY($uperm,'Reader'); ?>>*>)
		<?php  putGS('User is a reader'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManagePub"<?php  checkedIfY($uperm,'ManagePub'); ?>>*>)
		<?php  putGS('User may add/change publications'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeletePub"<?php  checkedIfY($uperm,'DeletePub'); ?>>*>)
		<?php  putGS('User may delete publications'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageIssue"<?php  checkedIfY($uperm,'ManageIssue'); ?>>*>)
		<?php  putGS('User may add/change issues'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteIssue"<?php  checkedIfY($uperm,'DeleteIssue'); ?>>*>)
		<?php  putGS('User may delete issues'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageSection"<?php  checkedIfY($uperm,'ManageSection'); ?>>*>)
		<?php  putGS('User may add/change sections'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteSection"<?php  checkedIfY($uperm,'DeleteSection'); ?>>*>)
		<?php  putGS('User may delete sections'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cAddArticle"<?php  checkedIfY($uperm,'AddArticle'); ?>>*>)
		<?php  putGS('User may add articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cChangeArticle"<?php  checkedIfY($uperm,'ChangeArticle'); ?>>*>)
		<?php  putGS('User may change articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteArticle"<?php  checkedIfY($uperm,'DeleteArticle'); ?>>*>)
		<?php  putGS('User may delete articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cAddImage"<?php  checkedIfY($uperm,'AddImage'); ?>>*>)
		<?php  putGS('User may add images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cChangeImage"<?php  checkedIfY($uperm,'ChangeImage'); ?>>*>)
		<?php  putGS('User may change images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteImage"<?php  checkedIfY($uperm,'DeleteImage'); ?>>*>)
		<?php  putGS('User may delete images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageTempl"<?php  checkedIfY($uperm,'ManageTempl'); ?>>*>)
		<?php  putGS('User may add templates'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteTempl"<?php  checkedIfY($uperm,'DeleteTempl'); ?>>*>)
		<?php  putGS('User may delete templates'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageUsers"<?php  checkedIfY($uperm,'ManageUsers'); ?>>*>)
		<?php  putGS('User may add/change user accounts and passwords'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteUsers"<?php  checkedIfY($uperm,'DeleteUsers'); ?>>*>)
		<?php  putGS('User may delete user accounts'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageSubscriptions"<?php  checkedIfY($uperm,'ManageSubscriptions'); ?>>*>)
		<?php  putGS('User may manage user subscriptions'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageUserTypes"<?php  checkedIfY($uperm,'ManageUserTypes'); ?>>*>)
		<?php  putGS('User may manage account types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageArticleTypes"<?php  checkedIfY($uperm,'ManageArticleTypes'); ?>>*>)
		<?php  putGS('User may add/change article types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteArticleTypes"<?php  checkedIfY($uperm,'DeleteArticleTypes'); ?>>*>)
		<?php  putGS('User may delete article types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageLanguages"<?php  checkedIfY($uperm,'ManageLanguages'); ?>>*>)
		<?php  putGS('User may add languages and manage language information'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteLanguages"<?php  checkedIfY($uperm,'DeleteLanguages'); ?>>*>)
		<?php  putGS('User may delete languages'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageClasses"<?php  checkedIfY($uperm,'ManageClasses'); ?>>*>)
		<?php  putGS('User may manage glossary infotypes'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageDictionary"<?php  checkedIfY($uperm,'ManageDictionary'); ?>>*>)
		<?php  putGS('User may add/change glossary entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteDictionary"<?php  checkedIfY($uperm,'DeleteDictionary'); ?>>*>)
		<?php  putGS('User may delete glossary entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageCountries"<?php  checkedIfY($uperm,'ManageCountries'); ?>>*>)
		<?php  putGS('User may add/change country names'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteCountries"<?php  checkedIfY($uperm,'DeleteCountries'); ?>>*>)
		<?php  putGS('User may delete country entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cMailNotify"<?php  checkedIfY($uperm,'MailNotify'); ?>>*>)
		<?php  putGS('User will be notified on several events'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cViewLogs"<?php  checkedIfY($uperm,'ViewLogs'); ?>>*>)
		<?php  putGS('User may view audit logs'); ?>
	
	E_DIALOG_INPUT
		B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageLocalizer"<?php  checkedIfY($uperm,'ManageLocalizer'); ?>>*>)
		<?php  putGS('User may manage localizer'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cPublish"<?php  checkedIfY($uperm,'Publish'); ?>>*>)
		<?php  putGS('User may publish articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageTopics"<?php  checkedIfY($uperm,'ManageTopics'); ?>>*>)
		<?php  putGS('User may manage topics'); ?>
	E_DIALOG_INPUT

	E_DIALOG_PACKEDINPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="UType" VALUE="<?php  print encHTML(decS($UType)); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/u_types/*>)
	E_DIALOG_BUTTONS
E_DIALOG
<?php  } else { ?>dnl
	<LI><?php  putGS('No such user type.'); ?></LI>
<?php  } ?>dnl
<P>

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML


