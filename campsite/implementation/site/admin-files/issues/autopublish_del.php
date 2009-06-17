<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/IssuePublish.php');

// Check permissions
if (!$g_user->hasPermission('Publish')) {
	camp_html_display_error(getGS("You do not have the right to schedule issues or articles for automatic publishing."));
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$event_id = trim(Input::Get('event_id', 'string', ''));

$action = new IssuePublish($event_id);
$deleted = $action->delete();

if ($deleted) {
        $issueObj = new Issue($Pub, $Language, $Issue);
        $logtext = getGS('Scheduled action deleted from issue #$1: "$2" (Publication: $3)', $Issue, $issueObj->getName(), $Pub);
        Log::Message($logtext, $g_user->getUserId(), 17);
        camp_html_goto_page("/$ADMIN/issues/edit.php?Pub=$Pub&Issue=$Issue&Language=$Language");
}
$publicationObj = new Publication($Pub);
$issueObj = new Issue($Pub, $Language, $Issue);
$crumbs = array("Pub" => $publicationObj, "Issue" => $issueObj);
camp_html_content_top(getGS("Delete scheduled publish action"), $crumbs);
?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Delete scheduled publish action"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<LI><?php putGS('The action scheduled on $1 could not be deleted.','<B>'.$publish_time.'</B>' ); ?></LI>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/issues/edit.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
