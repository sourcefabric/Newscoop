<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");

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
$publicationObj = new Publication($Pub);
$allLanguages = Language::GetLanguages(null, null, null, array(), array(), true);
$newIssueId = Issue::GetUnusedIssueId($Pub);
$lastCreatedIssue = Issue::GetLastCreatedIssue($Pub);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

camp_html_content_top(getGS('Copy previous issue'), array('Pub' => $publicationObj), true, true, array(getGS("Issues") => "/$ADMIN/issues/?Pub=$Pub"));


if (is_null($lastCreatedIssue)) { ?>
    <BLOCKQUOTE>
	<LI><?php  putGS('No previous issue.'); ?></LI>
    </BLOCKQUOTE>
    <?php
} else {
	camp_html_display_msgs();
	?>

<P>
<FORM name="issue_add" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/issues/do_add_prev.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Copy previous issue"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><?php  putGS('Copy structure from issue number $1','<B>'. $lastCreatedIssue->getIssueNumber().'</B>'); ?></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Issue Number"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_issue_number" VALUE="<?php  print ($lastCreatedIssue->getIssueNumber() + 1); ?>" SIZE="5" MAXLENGTH="10" alt="number|0|1|1000000000" emsg="<?php putGS("You must input a number greater than 0 into the $1 field.", "'".getGS("Number")."'"); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php p($Pub); ?>">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<script>
document.issue_add.f_issue_number.focus();
</script>
<?php  } ?>
<?php camp_html_copyright_notice(); ?>
