<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageIssue')) {
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
$lastCreatedIssue = Issue::GetLastCreatedIssue($Pub);

camp_html_content_top(getGS('Copy previous issue'), array('Pub' => $publicationObj), true, false, array(getGS("Issues") => "/$ADMIN/issues/?Pub=$Pub"));


if (is_null($lastCreatedIssue)) { ?>
    <BLOCKQUOTE>
	<LI><?php  putGS('No previous issue.'); ?></LI>
    </BLOCKQUOTE>
    <?php  
} else { ?>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add_prev.php"  >
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Copy previous issue"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><?php  putGS('Copy structure from issue nr $1','<B>'. $lastCreatedIssue->getIssueId().'</B>'); ?></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Issue Number"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cNumber" VALUE="<?php  print ($lastCreatedIssue->getIssueId() + 1); ?>" SIZE="5" MAXLENGTH="5">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php p($Pub); ?>">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
	<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/issues/?Pub=<?php  p($Pub); ?>'">-->
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<?php  } ?>
<?php camp_html_copyright_notice(); ?>
