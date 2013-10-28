<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");

$translator = \Zend_Registry::get('container')->getService('translator');
// Check permissions
if (!$g_user->hasPermission('ManageIssue')) {
	camp_html_display_error($translator->trans('You do not have the right to add issues.', array(), 'issues'));
	exit;
}
$f_publication_id = Input::Get('Pub', 'int');
$f_issue_number = Input::Get('Issue', 'int');
$f_language_id = Input::Get('Language', 'int');

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid Input: $1', array('$1' => Input::GetErrorString()), 'issues'));
	exit;
}
$publicationObj = new Publication($f_publication_id);
$issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
$allIssues = Issue::GetIssues($f_publication_id, null, $f_issue_number, null, null, false, null, true);
$unusedLanguages = $issueObj->getLanguages(true, true,
array(array('field'=>'byname', 'dir'=>'asc')), false, false);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

camp_html_content_top($translator->trans('Add new translation', array(), 'issues'), array('Pub' => $publicationObj, 'Issue' => $issueObj));

?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($f_publication_id); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($f_publication_id); ?>"><B><?php echo $translator->trans("Issue List"); ?></B></A></TD>
	<TD style="padding-left: 20px;"><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($f_publication_id); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/issues/edit.php?Pub=<?php  p($f_publication_id); ?>&Issue=<?php  p($issueObj->getIssueNumber()); ?>&Language=<?php p($issueObj->getLanguageId()); ?>"><B><?php  echo $translator->trans("Issue").": ".htmlspecialchars($issueObj->getName()); ?></B></A></TD>
</TR>
</TABLE>

<?php camp_html_display_msgs(); ?>

<P>
<FORM NAME="issue_translate" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/issues/do_translate.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="" CLASS="box_table">
<TR>
	<TD COLSPAN="2">
		<B><?php echo $translator->trans("Add new translation", array(), 'issues'); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" valign="top"><?php echo $translator->trans("Issue"); ?>:</TD>
	<TD>
		<?php
		$comma = 0;
		foreach ($allIssues as $tmpIssue) {
			if ($comma) {
			    print '<br>';
			}
			p(htmlspecialchars($tmpIssue->getName() .' ('.$tmpIssue->getLanguageName().')'));
			$comma =1;
		}
		?>
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_name" SIZE="32" alt="blank" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => $translator->trans('Name'))); ?>">
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("URL Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_url_name" size="32" value="<?php echo htmlspecialchars($issueObj->getUrlName()); ?>" alt="blank" emsg="<?php echo $translator->trans('You must fill in the $1 field.',array('$1' => $translator->trans('URL Name'))); ?>">
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("Language"); ?>:</TD>
	<TD>
		<SELECT NAME="f_new_language_id" class="input_select"><?php
		foreach ($unusedLanguages as $tmpLanguage) {
			camp_html_select_option($tmpLanguage->getLanguageId(),'',htmlspecialchars($tmpLanguage->getNativeName()));
        }
	    ?>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php p($f_publication_id);?>">
		<INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php p($f_issue_number); ?>">
		<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php p($f_language_id); ?>">
		<INPUT TYPE="submit" class="button" VALUE="<?php echo $translator->trans('Save'); ?>">
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<script>
document.issue_translate.f_name.focus();
</script>
<?php camp_html_copyright_notice(); ?>
