B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Edit user account permissions*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change user account permissions.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Edit user account permissions*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todefnum('User');
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
B_DIALOG(<*Edit user account permissions*>, <*POST*>, <*do_access.php*>)
	B_DIALOG_PACKEDINPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManagePub"<? ifYthenCHECKED($uperm,'ManagePub'); ?>>*>)
		<? putGS('User may add/change publications'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeletePub"<? ifYthenCHECKED($uperm,'DeletePub'); ?>>*>)
		<? putGS('User may delete publications'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageIssue"<? ifYthenCHECKED($uperm,'ManageIssue'); ?>>*>)
		<? putGS('User may add/change issues'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteIssue"<? ifYthenCHECKED($uperm,'DeleteIssue'); ?>>*>)
		<? putGS('User may delete issues'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageSection"<? ifYthenCHECKED($uperm,'ManageSection'); ?>>*>)
		<? putGS('User may add/change sections'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteSection"<? ifYthenCHECKED($uperm,'DeleteSection'); ?>>*>)
		<? putGS('User may delete sections'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cAddArticle"<? ifYthenCHECKED($uperm,'AddArticle'); ?>>*>)
		<? putGS('User may add articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cChangeArticle"<? ifYthenCHECKED($uperm,'ChangeArticle'); ?>>*>)
		<? putGS('User may change articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteArticle"<? ifYthenCHECKED($uperm,'DeleteArticle'); ?>>*>)
		<? putGS('User may delete articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cAddImage"<? ifYthenCHECKED($uperm,'AddImage'); ?>>*>)
		<? putGS('User may add images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cChangeImage"<? ifYthenCHECKED($uperm,'ChangeImage'); ?>>*>)
		<? putGS('User may change images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteImage"<? ifYthenCHECKED($uperm,'DeleteImage'); ?>>*>)
		<? putGS('User may delete images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageTempl"<? ifYthenCHECKED($uperm,'ManageTempl'); ?>>*>)
		<? putGS('User may manage templates'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteTempl"<? ifYthenCHECKED($uperm,'DeleteTempl'); ?>>*>)
		<? putGS('User may delete templates'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageUsers"<? ifYthenCHECKED($uperm,'ManageUsers'); ?>>*>)
		<? putGS('User may add/change user accounts and passwords'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteUsers"<? ifYthenCHECKED($uperm,'DeleteUsers'); ?>>*>)
		<? putGS('User may delete user accounts'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageSubscriptions"<? ifYthenCHECKED($uperm,'ManageSubscriptions'); ?>>*>)
		<? putGS('User may manage user subscriptions'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageUserTypes"<? ifYthenCHECKED($uperm,'ManageUserTypes'); ?>>*>)
		<? putGS('User may manage account types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageArticleTypes"<? ifYthenCHECKED($uperm,'ManageArticleTypes'); ?>>*>)
		<? putGS('User may add/change article types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteArticleTypes"<? ifYthenCHECKED($uperm,'DeleteArticleTypes'); ?>>*>)
		<? putGS('User may delete article types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageLanguages"<? ifYthenCHECKED($uperm,'ManageLanguages'); ?>>*>)
		<? putGS('User may add languages and manage language information'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteLanguages"<? ifYthenCHECKED($uperm,'DeleteLanguages'); ?>>*>)
		<? putGS('User may delete languages'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageClasses"<? ifYthenCHECKED($uperm,'ManageClasses'); ?>>*>)
		<? putGS('User may manage glossary infotypes'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageDictionary"<? ifYthenCHECKED($uperm,'ManageDictionary'); ?>>*>)
		<? putGS('User may add/change glossary entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteDictionary"<? ifYthenCHECKED($uperm,'DeleteDictionary'); ?>>*>)
		<? putGS('User may delete glossary entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageCountries"<? ifYthenCHECKED($uperm,'ManageCountries'); ?>>*>)
		<? putGS('User may add/change country entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteCountries"<? ifYthenCHECKED($uperm,'DeleteCountries'); ?>>*>)
		<? putGS('User may delete country entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cViewLogs"<? ifYthenCHECKED($uperm,'ViewLogs'); ?>>*>)
		<? putGS('User may view audit logs'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cMailNotify"<? ifYthenCHECKED($uperm,'MailNotify'); ?>>*>)
		<? putGS('User will be notified on several events'); ?>
	E_DIALOG_INPUT
	
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageLocalizer"<? ifYthenCHECKED($uperm,'ManageLocalizer'); ?>>*>)
		<? putGS('User may manage localizer'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cPublish"<? ifYthenCHECKED($uperm,'Publish'); ?>>*>)
		<? putGS('User may publish articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageTopics"<? ifYthenCHECKED($uperm,'ManageTopics'); ?>>*>)
		<? putGS('User may manage topics'); ?>
	E_DIALOG_INPUT

<SCRIPT>
	function do_submit()
	{
		document.dialog.submit();
		parent.fmenu.history.go(0);
	}
</SCRIPT>	
	
	E_DIALOG_PACKEDINPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<? pencHTML($User); ?>">
		<A HREF="javascript:void(do_submit())"><IMG SRC="X_ROOT/img/button/save.gif" BORDER="0" ALT="OK"></A>
		<A HREF="X_ROOT/users/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0"></A>
	E_DIALOG_BUTTONS
E_DIALOG
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

