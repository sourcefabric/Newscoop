<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");

$translator = \Zend_Registry::get('container')->getService('translator');
// Check permissions
if (!$g_user->hasPermission('ManageIssue') || !SaaS::singleton()->hasPermission('ManageIssueTemplates')) {
	camp_html_display_error($translator->trans('You do not have the right to add issues.', array(), 'issues'));
	exit;
}

$Pub = Input::Get('Pub', 'int');
if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid Input: $1', array('$1' => Input::GetErrorString()), 'issues'));
	exit;
}
$publicationObj = new Publication($Pub);
$allLanguages = Language::GetLanguages(null, null, null, array(), array(), true);
$newIssueId = Issue::GetUnusedIssueId($Pub);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

camp_html_content_top($translator->trans('Add new issue'), array('Pub' => $publicationObj), true, true, array($translator->trans("Issues") => "/$ADMIN/issues/?Pub=$Pub"));

camp_html_display_msgs();
?>

<P>
<FORM name="issue_add" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/issues/do_add_new.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR>
	<TD COLSPAN="2">
		<B><?php echo $translator->trans("Add new issue"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_issue_name" SIZE="32" alt="blank" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => "'".$translator->trans('Name')."'")); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("Language"); ?>:</TD>
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
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("Number"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_issue_number" VALUE="<?php p($newIssueId); ?>" SIZE="5" MAXLENGTH="10" alt="number|0|1|1000000000" emsg="<?php echo $translator->trans("You must input a number greater than 0 into the $1 field.", array('$1' => "'".$translator->trans("Number")."'")); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("URL Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_url_name" VALUE="<?php p($newIssueId); ?>" SIZE="32"  alt="alnum|1|A|true|false|_" emsg="<?php echo $translator->trans('The $1 field may only contain letters, digits and underscore (_) character.', array('$1' => "'" . $translator->trans('URL Name') . "'")); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php p($Pub); ?>">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php echo $translator->trans('Save'); ?>">
		&nbsp;&nbsp;
		<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php echo $translator->trans('Cancel'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/issues/?Pub=<?php p($Pub); ?>'">
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<script>
document.issue_add.f_issue_name.focus();
</script>
<?php camp_html_copyright_notice(); ?>
