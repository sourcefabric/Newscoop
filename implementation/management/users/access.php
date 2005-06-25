<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");

list($access, $User) = check_basic_access($_REQUEST);
$uType = 'Staff';
compute_user_rights($User, &$canManage, &$canDelete);
if (!$canManage) {
	$error = getGS("You do not have the right to change user account permissions.");
	CampsiteInterface::DisplayError($error);
	exit;
}

$userId = Input::Get('User', 'int', 0);
if ($userId > 0) {
	$editUser = new User($userId);
	if ($editUser->getUserName() == '') {
		CampsiteInterface::DisplayError(getGS('No such user account.'));
		exit;
	}
} else {
	CampsiteInterface::DisplayError(getGS('No such user account.'));
	exit;
}

?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Edit user account permissions"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/users/?uType=Staff" class="breadcrumb" ><?php  putGS("Staff");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("User account"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($uacc,'UName'); ?></TD>

</TR></TABLE>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_access.php" >
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS("Edit user account permissions"); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD>&nbsp;</TD><TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">

	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cManagePub" class="input_checkbox" <?php  ifTrueThenChecked($editUser->hasPermission('ManagePub')); ?>></TD>
		<TD>
		<?php  putGS('User may add/change publications'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cDeletePub" class="input_checkbox" <?php  ifTrueThenChecked($editUser->hasPermission('DeletePub')); ?>></TD>
		<TD>
		<?php  putGS('User may delete publications'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cManageIssue" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('ManageIssue')); ?>></TD>
		<TD>
		<?php  putGS('User may add/change issues'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cDeleteIssue" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('DeleteIssue')); ?>></TD>
		<TD>
		<?php  putGS('User may delete issues'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cManageSection" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('ManageSection')); ?>></TD>
		<TD>
		<?php  putGS('User may add/change sections'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cDeleteSection" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('DeleteSection')); ?>></TD>
		<TD>
		<?php  putGS('User may delete sections'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cAddArticle" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('AddArticle')); ?>></TD>
		<TD>
		<?php  putGS('User may add articles'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cChangeArticle" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('ChangeArticle')); ?>></TD>
		<TD>
		<?php  putGS('User may change articles'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cDeleteArticle" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('DeleteArticle')); ?>></TD>
		<TD>
		<?php  putGS('User may delete articles'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cAddImage" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('AddImage')); ?>></TD>
		<TD>
		<?php  putGS('User may add images'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cChangeImage" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('ChangeImage')); ?>></TD>
		<TD>
		<?php  putGS('User may change images'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cDeleteImage" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('DeleteImage')); ?>></TD>
		<TD>
		<?php  putGS('User may delete images'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cManageTempl" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('ManageTempl')); ?>></TD>
		<TD>
		<?php  putGS('User may manage templates'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cDeleteTempl" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('DeleteTempl')); ?>></TD>
		<TD>
		<?php  putGS('User may delete templates'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cManageUsers" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('ManageUsers')); ?>></TD>
		<TD>
		<?php  putGS('User may add/change user accounts and passwords'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cDeleteUsers" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('DeleteUsers')); ?>></TD>
		<TD>
		<?php  putGS('User may delete user accounts'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cManageReaders" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('ManageReaders')); ?>></TD>
		<TD>
		<?php  putGS('User may add/change subscribers accounts and passwords'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cManageSubscriptions" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('ManageSubscriptions')); ?>></TD>
		<TD>
		<?php  putGS('User may manage user subscriptions'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cManageUserTypes" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('ManageUserTypes')); ?>></TD>
		<TD>
		<?php  putGS('User may manage account types'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cManageArticleTypes" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('ManageArticleTypes')); ?>></TD>
		<TD>
		<?php  putGS('User may add/change article types'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cDeleteArticleTypes" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('DeleteArticleTypes')); ?>></TD>
		<TD>
		<?php  putGS('User may delete article types'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cManageLanguages" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('ManageLanguages')); ?>></TD>
		<TD>
		<?php  putGS('User may add languages and manage language information'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cDeleteLanguages" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('DeleteLanguages')); ?>></TD>
		<TD>
		<?php  putGS('User may delete languages'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cManageCountries" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('ManageCountries')); ?>></TD>
		<TD>
		<?php  putGS('User may add/change country entries'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cDeleteCountries" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('DeleteCountries')); ?>></TD>
		<TD>
		<?php  putGS('User may delete country entries'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cViewLogs" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('ViewLogs')); ?>></TD>
		<TD>
		<?php  putGS('User may view audit logs'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cMailNotify" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('MailNotify')); ?>></TD>
		<TD>
		<?php  putGS('User will be notified on several events'); ?>
		</TD>
	</TR>
	
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cManageLocalizer" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('ManageLocalizer')); ?>></TD>
		<TD>
		<?php  putGS('User may manage localizer'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cPublish" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('Publish')); ?>></TD>
		<TD>
		<?php  putGS('User may publish articles'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cManageTopics" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('ManageTopics')); ?>></TD>
		<TD>
		<?php  putGS('User may manage topics'); ?>
		</TD>
	</TR>
	
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
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorBold" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorBold')); ?>></TD>
		<TD>
		<?php  putGS('User may use bold'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorItalic" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorItalic')); ?>></TD>
		<TD>
		<?php  putGS('User may use italic'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorUnderline" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorUnderline')); ?>></TD>
		<TD>
		<?php  putGS('User may use underline'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorStrikethrough" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorStrikethrough')); ?>></TD>
		<TD>
		<?php  putGS('User may use strikethrough'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorTextAlignment" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorTextAlignment')); ?>></TD>
		<TD>
		<?php  putGS('User may change text alignment'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorCopyCutPaste" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorCopyCutPaste')); ?>></TD>
		<TD>
		<?php  putGS('User may copy, cut, and paste'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorUndoRedo" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorUndoRedo')); ?>></TD>
		<TD>
		<?php  putGS('User may undo/redo'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorTextDirection" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorTextDirection')); ?>></TD>
		<TD>
		<?php  putGS('User may change text direction'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorIndent" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorIndent')); ?>></TD>
		<TD>
		<?php  putGS('User may set indents'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorLink" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorLink')); ?>></TD>
		<TD>
		<?php  putGS('User may add links'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorSubhead" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorSubhead')); ?>></TD>
		<TD>
		<?php  putGS('User may add subheads'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorImage" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorImage')); ?>></TD>
		<TD>
		<?php  putGS('User may insert images'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorSourceView" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorSourceView')); ?>></TD>
		<TD>
		<?php  putGS('User may view the HTML source'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorEnlarge" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorEnlarge')); ?>></TD>
		<TD>
		<?php  putGS('User may enlarge the editor'); ?>
		</TD>
	</TR>
	
	<tr>
		<td colspan="2" align="left" style="padding-top: 5px;">
			--- Line 2 ---
		</td>
	</tr>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorFontFace" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorFontFace')); ?>></TD>
		<TD>
		<?php  putGS('User may change the font face'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorFontSize" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorFontSize')); ?>></TD>
		<TD>
		<?php  putGS('User may change the font size'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorListBullet" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorListBullet')); ?>></TD>
		<TD>
		<?php  putGS('User may create bulleted lists'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorListNumber" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorListNumber')); ?>></TD>
		<TD>
		<?php  putGS('User may create numbered lists'); ?>
		</TD>
	</TR>
	
	<tr>
		<td colspan="2" align="left" style="padding-top: 5px;">
			--- Line 3 ---
		</td>
	</tr>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorTable" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorTable')); ?>></TD>
		<TD>
		<?php  putGS('User may insert tables'); ?>
		</TD>
	</TR>

	<tr>
		<td colspan="2" align="left" style="padding-top: 5px;">
			--- Line 4 ---
		</td>
	</tr>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorHorizontalRule" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorHorizontalRule')); ?>></TD>
		<TD>
		<?php  putGS('User may insert horizontal rules'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorFontColor" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorFontColor')); ?>></TD>
		<TD>
		<?php  putGS('User may change the font color'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorSuperscript" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorSuperscript')); ?>></TD>
		<TD>
		<?php  putGS('User may use superscripts'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cEditorSubscript" class="input_checkbox"<?php  ifTrueThenChecked($editUser->hasPermission('EditorSubscript')); ?>></TD>
		<TD>
		<?php  putGS('User may use subscripts'); ?>
		</TD>
	</TR>

	<?php 
	## added by sebastian
	if (function_exists ("incModFile"))
		incModFile ($User);
	?>

		</TABLE>
	</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<?php pencHTML($User); ?>">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
		<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/admin/users/edit.php?uType=Staff&User=<?php pencHTML($User); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
<P>
<?php CampsiteInterface::CopyrightNotice(); ?>
</BODY>

</HTML>
