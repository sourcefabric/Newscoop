B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUserTypes*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Change user type permissions*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change user type permissions.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
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
<?
    todefnum('UType');
    query ("SELECT * FROM UserTypes WHERE Name='$UType'", 'uperm');
    fetchRow($uperm);
    if ($NUM_ROWS) { ?>
B_DIALOG(<*Change user type permissions*>, <*POST*>, <*do_access.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" VALUE="<? encHTML(pgetHVar($uperm,'Name')); ?>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_PACKEDINPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cReader"<? checkedIfY($uperm,'Reader'); ?>>*>)
		<? putGS('User is a reader'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManagePub"<? checkedIfY($uperm,'ManagePub'); ?>>*>)
		<? putGS('User may add/change publications'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeletePub"<? checkedIfY($uperm,'DeletePub'); ?>>*>)
		<? putGS('User may delete publications'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageIssue"<? checkedIfY($uperm,'ManageIssue'); ?>>*>)
		<? putGS('User may add/change issues'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteIssue"<? checkedIfY($uperm,'DeleteIssue'); ?>>*>)
		<? putGS('User may delete issues'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageSection"<? checkedIfY($uperm,'ManageSection'); ?>>*>)
		<? putGS('User may add/change sections'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteSection"<? checkedIfY($uperm,'DeleteSection'); ?>>*>)
		<? putGS('User may delete sections'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cAddArticle"<? checkedIfY($uperm,'AddArticle'); ?>>*>)
		<? putGS('User may add articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cChangeArticle"<? checkedIfY($uperm,'ChangeArticle'); ?>>*>)
		<? putGS('User may change articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteArticle"<? checkedIfY($uperm,'DeleteArticle'); ?>>*>)
		<? putGS('User may delete articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cAddImage"<? checkedIfY($uperm,'AddImage'); ?>>*>)
		<? putGS('User may add images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cChangeImage"<? checkedIfY($uperm,'ChangeImage'); ?>>*>)
		<? putGS('User may change images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteImage"<? checkedIfY($uperm,'DeleteImage'); ?>>*>)
		<? putGS('User may delete images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageTempl"<? checkedIfY($uperm,'ManageTempl'); ?>>*>)
		<? putGS('User may add templates'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteTempl"<? checkedIfY($uperm,'DeleteTempl'); ?>>*>)
		<? putGS('User may delete templates'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageUsers"<? checkedIfY($uperm,'ManageUsers'); ?>>*>)
		<? putGS('User may add/change user accounts and passwords'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteUsers"<? checkedIfY($uperm,'DeleteUsers'); ?>>*>)
		<? putGS('User may delete user accounts'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageSubscriptions"<? checkedIfY($uperm,'ManageSubscriptions'); ?>>*>)
		<? putGS('User may manage user subscriptions'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageUserTypes"<? checkedIfY($uperm,'ManageUserTypes'); ?>>*>)
		<? putGS('User may manage account types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageArticleTypes"<? checkedIfY($uperm,'ManageArticleTypes'); ?>>*>)
		<? putGS('User may add/change article types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteArticleTypes"<? checkedIfY($uperm,'DeleteArticleTypes'); ?>>*>)
		<? putGS('User may delete article types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageLanguages"<? checkedIfY($uperm,'ManageLanguages'); ?>>*>)
		<? putGS('User may add languages and manage language information'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteLanguages"<? checkedIfY($uperm,'DeleteLanguages'); ?>>*>)
		<? putGS('User may delete languages'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageClasses"<? checkedIfY($uperm,'ManageClasses'); ?>>*>)
		<? putGS('User may manage glossary infotypes'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageDictionary"<? checkedIfY($uperm,'ManageDictionary'); ?>>*>)
		<? putGS('User may add/change glossary entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteDictionary"<? checkedIfY($uperm,'DeleteDictionary'); ?>>*>)
		<? putGS('User may delete glossary entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageCountries"<? checkedIfY($uperm,'ManageCountries'); ?>>*>)
		<? putGS('User may add/change country names'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteCountries"<? checkedIfY($uperm,'DeleteCountries'); ?>>*>)
		<? putGS('User may delete country entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cMailNotify"<? checkedIfY($uperm,'MailNotify'); ?>>*>)
		<? putGS('User will be notified on several events'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cViewLogs"<? checkedIfY($uperm,'ViewLogs'); ?>>*>)
		<? putGS('User may view audit logs'); ?>
	
	E_DIALOG_INPUT
		B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageLocalizer"<? checkedIfY($uperm,'ManageLocalizer'); ?>>*>)
		<? putGS('User may manage localizer'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cPublish"<? checkedIfY($uperm,'Publish'); ?>>*>)
		<? putGS('User may publish articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageTopics"<? checkedIfY($uperm,'ManageTopics'); ?>>*>)
		<? putGS('User may manage topics'); ?>
	E_DIALOG_INPUT

	E_DIALOG_PACKEDINPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="UType" VALUE="<? print encHTML(decS($UType)); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/u_types/*>)
	E_DIALOG_BUTTONS
E_DIALOG
<? } else { ?>dnl
	<LI><? putGS('No such user type.'); ?></LI>
<? } ?>dnl
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML


