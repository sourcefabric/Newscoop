<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");

// Check permissions
if (!$g_user->hasPermission('ManageIssue')) {
	camp_html_display_error(getGS('You do not have the right to change issues.'));
	exit;
}
$Pub = Input::Get('Pub', 'int');
$Issue = Input::Get('Issue', 'int');
$Language = Input::Get('Language', 'int');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));
	exit;
}
$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);

if ($issueObj->getWorkflowStatus() == 'Y') {
	$t2 = getGS('Published');
	$t3 = getGS('Not published');
}
else {
	$t2 = getGS('Not published');
	$t3 = getGS('Published');
}

camp_html_content_top(getGS('Change issue status'), array('Pub' => $publicationObj, 'Issue' => $issueObj));

?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Change issue status"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE><LI>
		<?php  putGS('Are you sure you want to change the issue $1 status from $2 to $3?','<B>'.$issueObj->getIssueNumber().'. '.htmlspecialchars($issueObj->getName()).' ('.htmlspecialchars($issueObj->getLanguageName()).')</B>',"<B>$t2</B>","<B>$t3</B>"); ?>
		</LI></BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<FORM METHOD="POST" ACTION="do_status.php">
	<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php p($Pub); ?>">
	<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php p($Issue); ?>">
	<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php p($Language); ?>">
	<INPUT TYPE="submit" class="button" NAME="Yes" VALUE="<?php putGS('Yes'); ?>">
	<INPUT TYPE="button" class="button" NAME="No" VALUE="<?php putGS('No'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/issues/?Pub=<?php p($Pub); ?>'">
	</FORM>
	</DIV>
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
