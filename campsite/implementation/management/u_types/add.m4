INCLUDE_PHP_LIB(<*$ADMIN_DIR/u_types*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUserTypes*>)

B_HEAD
	X_TITLE(<*Add new user type*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add user types.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new user type*>)
B_HEADER_BUTTONS
X_HBUTTON(<*User Types*>, <*u_types/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<P>
B_DIALOG(<*Add new user type*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_PACKEDINPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cReader">*>)
		<?php  putGS('User is a reader'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManagePub">*>)
		<?php  putGS('User may add/change publications'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeletePub">*>)
		<?php  putGS('User may delete publications'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageIssue">*>)
		<?php  putGS('User may add/change issues'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteIssue">*>)
		<?php  putGS('User may delete issues'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageSection">*>)
		<?php  putGS('User may add/change sections'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteSection">*>)
		<?php  putGS('User may delete sections'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cAddArticle">*>)
		<?php  putGS('User may add articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cChangeArticle">*>)
		<?php  putGS('User may change articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteArticle">*>)
		<?php  putGS('User may delete articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cAddImage">*>)
		<?php  putGS('User may add images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cChangeImage">*>)
		<?php  putGS('User may change images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteImage">*>)
		<?php  putGS('User may delete images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageTempl">*>)
		<?php  putGS('User may add templates'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteTempl">*>)
		<?php  putGS('User may delete templates'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageUsers">*>)
		<?php  putGS('User may add/change user accounts and passwords'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteUsers">*>)
		<?php  putGS('User may delete user accounts'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageSubscriptions">*>)
		<?php  putGS('User may manage user subscriptions'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageUserTypes">*>)
		<?php  putGS('User may manage account types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageArticleTypes">*>)
		<?php  putGS('User may add/change article types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteArticleTypes">*>)
		<?php  putGS('User may delete article types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageLanguages">*>)
		<?php  putGS('User may add languages and manage language information'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteLanguages">*>)
		<?php  putGS('User may delete languages'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageClasses">*>)
		<?php  putGS('User may manage glossary infotypes'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageDictionary">*>)
		<?php  putGS('User may add/change glossary entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteDictionary">*>)
		<?php  putGS('User may delete glossary entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cMailNotify">*>)
		<?php  putGS('User will be notified on several events'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cViewLogs">*>)
		<?php  putGS('User may view audit logs'); ?>
	E_DIALOG_INPUT
	E_DIALOG_INPUT
		B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cViewLogs">*>)
		<?php  putGS('User may manage localizer'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cViewLogs">*>)
		<?php  putGS('User may publish articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cViewLogs">*>)
		<?php  putGS('User may manage topics'); ?>
	E_DIALOG_INPUT
	E_DIALOG_PACKEDINPUT
	B_DIALOG_BUTTONS
		SUBMIT(<*Save*>, <*Save changes*>)
<?php  todef('Back'); ?>dnl
		<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<?php  print encHTML($Back); ?>">
<?php  if ($Back != "") { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*<?php  print encHTML($Back); ?>*>)
<?php } else { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/u_types/*>)
<?php  } ?>dnl
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML


