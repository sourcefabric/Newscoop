<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");

// Check permissions
if (!$g_user->hasPermission('ManageIssue')) {
	camp_html_display_error(getGS('You do not have the right to add issues.'));
	exit;
}

$Pub = Input::Get('Pub', 'int');
if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));
	exit;
}
$publicationObj =& new Publication($Pub);
$allLanguages = Language::GetLanguages();
$newIssueId = Issue::GetUnusedIssueId($Pub);

camp_html_content_top(getGS('Add new issue'), array('Pub' => $publicationObj), true, true, array(getGS("Issues") => "/$ADMIN/issues/?Pub=$Pub"));
?>

<P>
<FORM METHOD="POST" ACTION="do_add_new.php" onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Add new issue"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_issue_name" SIZE="32" MAXLENGTH="64" alt="blank" emsg="<?php putGS('You must complete the $1 field.',"'".getGS('Name')."'"); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</TD>
	<TD>
	<SELECT NAME="f_language_id" class="input_select">
	   <?php
            foreach ($allLanguages as $language) {
            	camp_html_select_option($language->getLanguageId(), $publicationObj->getLanguageId(), $language->getNativeName());
            }
        ?>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Number"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_issue_number" VALUE="<?php p($newIssueId); ?>" SIZE="5" MAXLENGTH="10" alt="number|0|1|1000000000" emsg="<?php putGS("You must input a number greater than 0 into the $1 field.", "'".getGS("Number")."'"); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("URL Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_url_name" VALUE="<?php p($newIssueId); ?>" SIZE="32" MAXLENGTH="32" alt="alnum|1|A|true|false|_" emsg="<?php putGS('The $1 field may only contain letters, digits and underscore (_) character.', "'" . getGS('URL Name') . "'"); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php p($Pub); ?>">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
		&nbsp;&nbsp;
		<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/issues/?Pub=<?php p($Pub); ?>'">
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>