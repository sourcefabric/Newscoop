INCLUDE_PHP_LIB(<*$ADMIN_DIR/u_types*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUserTypes*>)

B_HEAD
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
E_HEADER_BUTTONS
E_HEADER

<P>
<?php 
    todef('UType');
    query ("SELECT * FROM UserTypes WHERE Name='$UType'", 'uperm');
    fetchRow($uperm);
    if ($NUM_ROWS) { ?>
B_DIALOG(<*Change user type permissions*>, <*POST*>, <*do_access.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" VALUE="<?php  encHTML(pgetHVar($uperm,'Name')); ?>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_PACKEDINPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cReader"<?php  checkedIfY($uperm,'Reader'); ?> class="input_checkbox">*>)
		<?php  putGS('User is a reader'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManagePub"<?php  checkedIfY($uperm,'ManagePub'); ?> class="input_checkbox">*>)
		<?php  putGS('User may add/change publications'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeletePub"<?php  checkedIfY($uperm,'DeletePub'); ?> class="input_checkbox">*>)
		<?php  putGS('User may delete publications'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageIssue"<?php  checkedIfY($uperm,'ManageIssue'); ?> class="input_checkbox">*>)
		<?php  putGS('User may add/change issues'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteIssue"<?php  checkedIfY($uperm,'DeleteIssue'); ?> class="input_checkbox">*>)
		<?php  putGS('User may delete issues'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageSection"<?php  checkedIfY($uperm,'ManageSection'); ?> class="input_checkbox">*>)
		<?php  putGS('User may add/change sections'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteSection"<?php  checkedIfY($uperm,'DeleteSection'); ?> class="input_checkbox">*>)
		<?php  putGS('User may delete sections'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cAddArticle"<?php  checkedIfY($uperm,'AddArticle'); ?> class="input_checkbox">*>)
		<?php  putGS('User may add articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cChangeArticle"<?php  checkedIfY($uperm,'ChangeArticle'); ?> class="input_checkbox">*>)
		<?php  putGS('User may change articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteArticle"<?php  checkedIfY($uperm,'DeleteArticle'); ?> class="input_checkbox">*>)
		<?php  putGS('User may delete articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cAddImage"<?php  checkedIfY($uperm,'AddImage'); ?> class="input_checkbox">*>)
		<?php  putGS('User may add images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cChangeImage"<?php  checkedIfY($uperm,'ChangeImage'); ?> class="input_checkbox">*>)
		<?php  putGS('User may change images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteImage"<?php  checkedIfY($uperm,'DeleteImage'); ?> class="input_checkbox">*>)
		<?php  putGS('User may delete images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageTempl"<?php  checkedIfY($uperm,'ManageTempl'); ?> class="input_checkbox">*>)
		<?php  putGS('User may add templates'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteTempl"<?php  checkedIfY($uperm,'DeleteTempl'); ?> class="input_checkbox">*>)
		<?php  putGS('User may delete templates'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageUsers"<?php  checkedIfY($uperm,'ManageUsers'); ?> class="input_checkbox">*>)
		<?php  putGS('User may add/change user accounts and passwords'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageReaders"<?php  checkedIfY($uperm,'ManageReaders'); ?> class="input_checkbox">*>)
		<?php  putGS('User may add/change subscribers accounts and passwords'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteUsers"<?php  checkedIfY($uperm,'DeleteUsers'); ?> class="input_checkbox">*>)
		<?php  putGS('User may delete user accounts'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageSubscriptions"<?php  checkedIfY($uperm,'ManageSubscriptions'); ?> class="input_checkbox">*>)
		<?php  putGS('User may manage user subscriptions'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageUserTypes"<?php  checkedIfY($uperm,'ManageUserTypes'); ?> class="input_checkbox">*>)
		<?php  putGS('User may manage account types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageArticleTypes"<?php  checkedIfY($uperm,'ManageArticleTypes'); ?> class="input_checkbox">*>)
		<?php  putGS('User may add/change article types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteArticleTypes"<?php  checkedIfY($uperm,'DeleteArticleTypes'); ?> class="input_checkbox">*>)
		<?php  putGS('User may delete article types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageLanguages"<?php  checkedIfY($uperm,'ManageLanguages'); ?> class="input_checkbox">*>)
		<?php  putGS('User may add languages and manage language information'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteLanguages"<?php  checkedIfY($uperm,'DeleteLanguages'); ?> class="input_checkbox">*>)
		<?php  putGS('User may delete languages'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageCountries"<?php  checkedIfY($uperm,'ManageCountries'); ?> class="input_checkbox">*>)
		<?php  putGS('User may add/change country names'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteCountries"<?php  checkedIfY($uperm,'DeleteCountries'); ?> class="input_checkbox">*>)
		<?php  putGS('User may delete country entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cMailNotify"<?php  checkedIfY($uperm,'MailNotify'); ?> class="input_checkbox">*>)
		<?php  putGS('User will be notified on several events'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cViewLogs"<?php  checkedIfY($uperm,'ViewLogs'); ?> class="input_checkbox">*>)
		<?php  putGS('User may view audit logs'); ?>
	
	E_DIALOG_INPUT
		B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageLocalizer"<?php  checkedIfY($uperm,'ManageLocalizer'); ?> class="input_checkbox">*>)
		<?php  putGS('User may manage localizer'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cPublish"<?php  checkedIfY($uperm,'Publish'); ?> class="input_checkbox">*>)
		<?php  putGS('User may publish articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageTopics"<?php  checkedIfY($uperm,'ManageTopics'); ?> class="input_checkbox">*>)
		<?php  putGS('User may manage topics'); ?>
	E_DIALOG_INPUT

	<tr>
		<td colspan="2" align="left" style="padding-top: 10px;">
			<b>WYSIWYG Editor Permissions</b>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="left" style="padding-top: 5px;">
			--- Line 1 ---
		</td>
	</tr>
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorBold"<?php  ifYthenCHECKED($uperm,'EditorBold'); ?> class="input_checkbox">*>)
		<?php  putGS('User may use bold'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorItalic"<?php  ifYthenCHECKED($uperm,'EditorItalic'); ?> class="input_checkbox">*>)
		<?php  putGS('User may use italic'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorUnderline"<?php  ifYthenCHECKED($uperm,'EditorUnderline'); ?> class="input_checkbox">*>)
		<?php  putGS('User may use underline'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorStrikethrough"<?php  ifYthenCHECKED($uperm,'EditorStrikethrough'); ?> class="input_checkbox">*>)
		<?php  putGS('User may use strikethrough'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorTextAlignment"<?php  ifYthenCHECKED($uperm,'EditorTextAlignment'); ?> class="input_checkbox">*>)
		<?php  putGS('User may change text alignment'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorCopyCutPaste"<?php  ifYthenCHECKED($uperm,'EditorCopyCutPaste'); ?> class="input_checkbox">*>)
		<?php  putGS('User may copy, cut, and paste'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorUndoRedo"<?php  ifYthenCHECKED($uperm,'EditorUndoRedo'); ?> class="input_checkbox">*>)
		<?php  putGS('User may undo/redo'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorTextDirection"<?php  ifYthenCHECKED($uperm,'EditorTextDirection'); ?> class="input_checkbox">*>)
		<?php  putGS('User may change text direction'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorIndent"<?php  ifYthenCHECKED($uperm,'EditorIndent'); ?> class="input_checkbox">*>)
		<?php  putGS('User may set indents'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorLink"<?php  ifYthenCHECKED($uperm,'EditorLink'); ?> class="input_checkbox">*>)
		<?php  putGS('User may add links'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorSubhead"<?php  ifYthenCHECKED($uperm,'EditorSubhead'); ?> class="input_checkbox">*>)
		<?php  putGS('User may add subheads'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorImage"<?php  ifYthenCHECKED($uperm,'EditorImage'); ?> class="input_checkbox">*>)
		<?php  putGS('User may insert images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorSourceView"<?php  ifYthenCHECKED($uperm,'EditorSourceView'); ?> class="input_checkbox">*>)
		<?php  putGS('User may view the HTML source'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorEnlarge"<?php  ifYthenCHECKED($uperm,'EditorEnlarge'); ?> class="input_checkbox">*>)
		<?php  putGS('User may enlarge the editor'); ?>
	E_DIALOG_INPUT
	
	<tr>
		<td colspan="2" align="left" style="padding-top: 5px;">
			--- Line 2 ---
		</td>
	</tr>
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorFontFace"<?php  ifYthenCHECKED($uperm,'EditorFontFace'); ?> class="input_checkbox">*>)
		<?php  putGS('User may change the font face'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorFontSize"<?php  ifYthenCHECKED($uperm,'EditorFontSize'); ?> class="input_checkbox">*>)
		<?php  putGS('User may change the font size'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorListBullet"<?php  ifYthenCHECKED($uperm,'EditorListBullet'); ?> class="input_checkbox">*>)
		<?php  putGS('User may create bulleted lists'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorListNumber"<?php  ifYthenCHECKED($uperm,'EditorListNumber'); ?> class="input_checkbox">*>)
		<?php  putGS('User may create numbered lists'); ?>
	E_DIALOG_INPUT
	
	<tr>
		<td colspan="2" align="left" style="padding-top: 5px;">
			--- Line 3 ---
		</td>
	</tr>
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorTable"<?php  ifYthenCHECKED($uperm,'EditorTable'); ?> class="input_checkbox">*>)
		<?php  putGS('User may insert tables'); ?>
	E_DIALOG_INPUT

	<tr>
		<td colspan="2" align="left" style="padding-top: 5px;">
			--- Line 4 ---
		</td>
	</tr>
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorHorizontalRule"<?php  ifYthenCHECKED($uperm,'EditorHorizontalRule'); ?> class="input_checkbox">*>)
		<?php  putGS('User may insert horizontal rules'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorFontColor"<?php  ifYthenCHECKED($uperm,'EditorFontColor'); ?> class="input_checkbox">*>)
		<?php  putGS('User may change the font color'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorSuperscript"<?php  ifYthenCHECKED($uperm,'EditorSuperscript'); ?> class="input_checkbox">*>)
		<?php  putGS('User may use superscripts'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorSubscript"<?php  ifYthenCHECKED($uperm,'EditorSubscript'); ?> class="input_checkbox">*>)
		<?php  putGS('User may use subscripts'); ?>
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

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML


