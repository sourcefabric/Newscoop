B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/users*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Edit user account permissions*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change user account permissions.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
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

<?php 
    todefnum('User');
    query ("SELECT * FROM Users WHERE Id=$User", 'uacc');
    if ($NUM_ROWS) {
	query ("SELECT * FROM UserPerm WHERE IdUser=$User", 'uperm');
	    if ($NUM_ROWS) { 
		fetchRow($uacc);
		fetchRow($uperm);
	    
	    ?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<B><?php  pgetHVar($uacc,'UName'); ?></B>*>)
E_CURRENT

<P>
B_DIALOG(<*Edit user account permissions*>, <*POST*>, <*do_access.php*>)
	B_DIALOG_PACKEDINPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManagePub"<?php  ifYthenCHECKED($uperm,'ManagePub'); ?>>*>)
		<?php  putGS('User may add/change publications'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeletePub"<?php  ifYthenCHECKED($uperm,'DeletePub'); ?>>*>)
		<?php  putGS('User may delete publications'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageIssue"<?php  ifYthenCHECKED($uperm,'ManageIssue'); ?>>*>)
		<?php  putGS('User may add/change issues'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteIssue"<?php  ifYthenCHECKED($uperm,'DeleteIssue'); ?>>*>)
		<?php  putGS('User may delete issues'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageSection"<?php  ifYthenCHECKED($uperm,'ManageSection'); ?>>*>)
		<?php  putGS('User may add/change sections'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteSection"<?php  ifYthenCHECKED($uperm,'DeleteSection'); ?>>*>)
		<?php  putGS('User may delete sections'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cAddArticle"<?php  ifYthenCHECKED($uperm,'AddArticle'); ?>>*>)
		<?php  putGS('User may add articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cChangeArticle"<?php  ifYthenCHECKED($uperm,'ChangeArticle'); ?>>*>)
		<?php  putGS('User may change articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteArticle"<?php  ifYthenCHECKED($uperm,'DeleteArticle'); ?>>*>)
		<?php  putGS('User may delete articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cAddImage"<?php  ifYthenCHECKED($uperm,'AddImage'); ?>>*>)
		<?php  putGS('User may add images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cChangeImage"<?php  ifYthenCHECKED($uperm,'ChangeImage'); ?>>*>)
		<?php  putGS('User may change images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteImage"<?php  ifYthenCHECKED($uperm,'DeleteImage'); ?>>*>)
		<?php  putGS('User may delete images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageTempl"<?php  ifYthenCHECKED($uperm,'ManageTempl'); ?>>*>)
		<?php  putGS('User may manage templates'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteTempl"<?php  ifYthenCHECKED($uperm,'DeleteTempl'); ?>>*>)
		<?php  putGS('User may delete templates'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageUsers"<?php  ifYthenCHECKED($uperm,'ManageUsers'); ?>>*>)
		<?php  putGS('User may add/change user accounts and passwords'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteUsers"<?php  ifYthenCHECKED($uperm,'DeleteUsers'); ?>>*>)
		<?php  putGS('User may delete user accounts'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageSubscriptions"<?php  ifYthenCHECKED($uperm,'ManageSubscriptions'); ?>>*>)
		<?php  putGS('User may manage user subscriptions'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageUserTypes"<?php  ifYthenCHECKED($uperm,'ManageUserTypes'); ?>>*>)
		<?php  putGS('User may manage account types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageArticleTypes"<?php  ifYthenCHECKED($uperm,'ManageArticleTypes'); ?>>*>)
		<?php  putGS('User may add/change article types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteArticleTypes"<?php  ifYthenCHECKED($uperm,'DeleteArticleTypes'); ?>>*>)
		<?php  putGS('User may delete article types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageLanguages"<?php  ifYthenCHECKED($uperm,'ManageLanguages'); ?>>*>)
		<?php  putGS('User may add languages and manage language information'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteLanguages"<?php  ifYthenCHECKED($uperm,'DeleteLanguages'); ?>>*>)
		<?php  putGS('User may delete languages'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageClasses"<?php  ifYthenCHECKED($uperm,'ManageClasses'); ?>>*>)
		<?php  putGS('User may manage glossary infotypes'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageDictionary"<?php  ifYthenCHECKED($uperm,'ManageDictionary'); ?>>*>)
		<?php  putGS('User may add/change glossary entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteDictionary"<?php  ifYthenCHECKED($uperm,'DeleteDictionary'); ?>>*>)
		<?php  putGS('User may delete glossary entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageCountries"<?php  ifYthenCHECKED($uperm,'ManageCountries'); ?>>*>)
		<?php  putGS('User may add/change country entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteCountries"<?php  ifYthenCHECKED($uperm,'DeleteCountries'); ?>>*>)
		<?php  putGS('User may delete country entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cViewLogs"<?php  ifYthenCHECKED($uperm,'ViewLogs'); ?>>*>)
		<?php  putGS('User may view audit logs'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cMailNotify"<?php  ifYthenCHECKED($uperm,'MailNotify'); ?>>*>)
		<?php  putGS('User will be notified on several events'); ?>
	E_DIALOG_INPUT
	
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageLocalizer"<?php  ifYthenCHECKED($uperm,'ManageLocalizer'); ?>>*>)
		<?php  putGS('User may manage localizer'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cPublish"<?php  ifYthenCHECKED($uperm,'Publish'); ?>>*>)
		<?php  putGS('User may publish articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageTopics"<?php  ifYthenCHECKED($uperm,'ManageTopics'); ?>>*>)
		<?php  putGS('User may manage topics'); ?>
	E_DIALOG_INPUT

	<?php 
	## added by sebastian
	if (function_exists ("incModFile"))
		incModFile ($User);
	?>

<SCRIPT>
	function do_submit()
	{
		document.dialog.submit();
		parent.fmenu.history.go(0);
	}
</SCRIPT>	
	
	E_DIALOG_PACKEDINPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<?php  pencHTML($User); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/users/*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('This user account does not have permissions information.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such user account.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

