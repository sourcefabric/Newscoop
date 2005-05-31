INCLUDE_PHP_LIB(<*$ADMIN_DIR/users*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
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
X_HBUTTON(<*Staff*>, <*users/?uType=Staff*>)
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
X_CURRENT(<*User account*>, <*<?php  pgetHVar($uacc,'UName'); ?>*>)
E_CURRENT

<P>
B_DIALOG(<*Edit user account permissions*>, <*POST*>, <*do_access.php*>)
	B_DIALOG_PACKEDINPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManagePub" class="input_checkbox" <?php  ifYthenCHECKED($uperm,'ManagePub'); ?>>*>)
		<?php  putGS('User may add/change publications'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeletePub" class="input_checkbox" <?php  ifYthenCHECKED($uperm,'DeletePub'); ?>>*>)
		<?php  putGS('User may delete publications'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageIssue" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'ManageIssue'); ?>>*>)
		<?php  putGS('User may add/change issues'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteIssue" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'DeleteIssue'); ?>>*>)
		<?php  putGS('User may delete issues'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageSection" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'ManageSection'); ?>>*>)
		<?php  putGS('User may add/change sections'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteSection" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'DeleteSection'); ?>>*>)
		<?php  putGS('User may delete sections'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cAddArticle" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'AddArticle'); ?>>*>)
		<?php  putGS('User may add articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cChangeArticle" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'ChangeArticle'); ?>>*>)
		<?php  putGS('User may change articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteArticle" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'DeleteArticle'); ?>>*>)
		<?php  putGS('User may delete articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cAddImage" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'AddImage'); ?>>*>)
		<?php  putGS('User may add images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cChangeImage" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'ChangeImage'); ?>>*>)
		<?php  putGS('User may change images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteImage" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'DeleteImage'); ?>>*>)
		<?php  putGS('User may delete images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageTempl" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'ManageTempl'); ?>>*>)
		<?php  putGS('User may manage templates'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteTempl" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'DeleteTempl'); ?>>*>)
		<?php  putGS('User may delete templates'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageUsers" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'ManageUsers'); ?>>*>)
		<?php  putGS('User may add/change user accounts and passwords'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteUsers" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'DeleteUsers'); ?>>*>)
		<?php  putGS('User may delete user accounts'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageReaders" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'ManageReaders'); ?>>*>)
		<?php  putGS('User may add/change subscribers accounts and passwords'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageSubscriptions" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'ManageSubscriptions'); ?>>*>)
		<?php  putGS('User may manage user subscriptions'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageUserTypes" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'ManageUserTypes'); ?>>*>)
		<?php  putGS('User may manage account types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageArticleTypes" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'ManageArticleTypes'); ?>>*>)
		<?php  putGS('User may add/change article types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteArticleTypes" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'DeleteArticleTypes'); ?>>*>)
		<?php  putGS('User may delete article types'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageLanguages" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'ManageLanguages'); ?>>*>)
		<?php  putGS('User may add languages and manage language information'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteLanguages" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'DeleteLanguages'); ?>>*>)
		<?php  putGS('User may delete languages'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageCountries" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'ManageCountries'); ?>>*>)
		<?php  putGS('User may add/change country entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cDeleteCountries" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'DeleteCountries'); ?>>*>)
		<?php  putGS('User may delete country entries'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cViewLogs" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'ViewLogs'); ?>>*>)
		<?php  putGS('User may view audit logs'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cMailNotify" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'MailNotify'); ?>>*>)
		<?php  putGS('User will be notified on several events'); ?>
	E_DIALOG_INPUT
	
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageLocalizer" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'ManageLocalizer'); ?>>*>)
		<?php  putGS('User may manage localizer'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cPublish" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'Publish'); ?>>*>)
		<?php  putGS('User may publish articles'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cManageTopics" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'ManageTopics'); ?>>*>)
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
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorBold" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorBold'); ?>>*>)
		<?php  putGS('User may use bold'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorItalic" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorItalic'); ?>>*>)
		<?php  putGS('User may use italic'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorUnderline" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorUnderline'); ?>>*>)
		<?php  putGS('User may use underline'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorStrikethrough" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorStrikethrough'); ?>>*>)
		<?php  putGS('User may use strikethrough'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorTextAlignment" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorTextAlignment'); ?>>*>)
		<?php  putGS('User may change text alignment'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorCopyCutPaste" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorCopyCutPaste'); ?>>*>)
		<?php  putGS('User may copy, cut, and paste'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorUndoRedo" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorUndoRedo'); ?>>*>)
		<?php  putGS('User may undo/redo'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorTextDirection" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorTextDirection'); ?>>*>)
		<?php  putGS('User may change text direction'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorIndent" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorIndent'); ?>>*>)
		<?php  putGS('User may set indents'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorLink" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorLink'); ?>>*>)
		<?php  putGS('User may add links'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorSubhead" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorSubhead'); ?>>*>)
		<?php  putGS('User may add subheads'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorImage" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorImage'); ?>>*>)
		<?php  putGS('User may insert images'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorSourceView" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorSourceView'); ?>>*>)
		<?php  putGS('User may view the HTML source'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorEnlarge" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorEnlarge'); ?>>*>)
		<?php  putGS('User may enlarge the editor'); ?>
	E_DIALOG_INPUT
	
	<tr>
		<td colspan="2" align="left" style="padding-top: 5px;">
			--- Line 2 ---
		</td>
	</tr>
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorFontFace" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorFontFace'); ?>>*>)
		<?php  putGS('User may change the font face'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorFontSize" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorFontSize'); ?>>*>)
		<?php  putGS('User may change the font size'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorListBullet" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorListBullet'); ?>>*>)
		<?php  putGS('User may create bulleted lists'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorListNumber" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorListNumber'); ?>>*>)
		<?php  putGS('User may create numbered lists'); ?>
	E_DIALOG_INPUT
	
	<tr>
		<td colspan="2" align="left" style="padding-top: 5px;">
			--- Line 3 ---
		</td>
	</tr>
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorTable" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorTable'); ?>>*>)
		<?php  putGS('User may insert tables'); ?>
	E_DIALOG_INPUT

	<tr>
		<td colspan="2" align="left" style="padding-top: 5px;">
			--- Line 4 ---
		</td>
	</tr>
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorHorizontalRule" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorHorizontalRule'); ?>>*>)
		<?php  putGS('User may insert horizontal rules'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorFontColor" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorFontColor'); ?>>*>)
		<?php  putGS('User may change the font color'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorSuperscript" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorSuperscript'); ?>>*>)
		<?php  putGS('User may use superscripts'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cEditorSubscript" class="input_checkbox"<?php  ifYthenCHECKED($uperm,'EditorSubscript'); ?>>*>)
		<?php  putGS('User may use subscripts'); ?>
	E_DIALOG_INPUT

	<?php 
	## added by sebastian
	if (function_exists ("incModFile"))
		incModFile ($User);
	?>

	E_DIALOG_PACKEDINPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<?php pencHTML($User); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/users/edit.php?uType=Staff&User=<?php pencHTML($User); ?>*>)
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

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
