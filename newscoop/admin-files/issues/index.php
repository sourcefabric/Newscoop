<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/SimplePager.php");
require_once($GLOBALS['g_campsiteDir']."/classes/IssuePublish.php");
camp_load_translation_strings("api");

$Pub = Input::Get('Pub', 'int', 0);
$IssOffs = camp_session_get("IssOffs_$Pub", 0);
if ($IssOffs < 0) {
	$IssOffs = 0;
}
$ItemsPerPage = 15;

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}
$publicationObj = new Publication($Pub);
$allIssues = Issue::GetIssues($Pub, null, null, null, $publicationObj->getLanguageId(), false, array('LIMIT' => array('START' => $IssOffs, 'MAX_ROWS'=> $ItemsPerPage)), true);
$totalIssues = Issue::GetNumIssues($Pub);

$pager = new SimplePager($totalIssues, $ItemsPerPage, "IssOffs_$Pub", "index.php?Pub=$Pub&");
include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

camp_html_content_top(getGS('Issue List'), array('Pub' => $publicationObj));
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/pub/"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/pub/"><B><?php  putGS("Publication List"); ?></B></A></TD>
<?php
if ($g_user->hasPermission('ManageIssue')) {
	if (Issue::GetNumIssues($Pub) <= 0) {
		?>
            <TD style="padding-left: 20px;"><A HREF="/<?php echo $ADMIN; ?>/issues/add_new.php?Pub=<?php p($Pub); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
            <TD><A HREF="/<?php echo $ADMIN; ?>/issues/add_new.php?Pub=<?php p($Pub); ?>"><B><?php  putGS("Add new issue"); ?></B></A></TD>
	<?php  } else { ?>
            <TD style="padding-left: 20px;"><A HREF="/<?php echo $ADMIN; ?>/issues/qadd.php?Pub=<?php p($Pub); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
            <TD><A HREF="/<?php echo $ADMIN; ?>/issues/qadd.php?Pub=<?php p($Pub); ?>"><B><?php  putGS("Add new issue"); ?></B></A></TD>
	<?php  }
}
?>
</TR>
</TABLE>
<?php camp_html_display_msgs(); ?>
<P>
<?php
if (count($allIssues) > 0) {
	$color = 0;
	?>
	<table class="indent">
	<TR>
		<TD>
			<?php echo $pager->render(); ?>
		</TD>
	</TR>
	</TABLE>

	<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("Number"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("Name<BR><SMALL>(click to see sections)</SMALL>"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("URL Name"); ?></B></TD>
		<TD ALIGN="center" VALIGN="TOP"><B><?php putGS("Publish Date $1", "<br><small>(".getGS("YYYY-MM-DD").")</small>"); ?></B></TD>

		<?php  if ($g_user->hasPermission('ManageIssue')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("Configure"); ?></B></TD>
		<?php } ?>

		<?php  if ($g_user->hasPermission('ManageIssue')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("Translate"); ?></B></TD>
		<?php } ?>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("Preview"); ?></B></TD>

		<?php if ($g_user->hasPermission('DeleteIssue')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("Delete"); ?></B></TD>
		<?php  } ?>
	</TR>

<?php
$currentIssue = -1;
foreach ($allIssues as $issue) {
	$pendingEvents = IssuePublish::GetIssueEvents($issue->getPublicationId(), $issue->getIssueNumber(), $issue->getLanguageId(), false);
	?>
	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>

	<TD ALIGN="RIGHT">
		<?php p($issue->getIssueNumber()); ?>
 	</TD>

	<TD <?php if ($currentIssue == $issue->getIssueNumber()) { ?> class="translation_indent" <?php } ?>>
		<A HREF="/<?php echo $ADMIN; ?>/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($issue->getIssueNumber()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><?php p(htmlspecialchars($issue->getName())); ?></A> (<?php p(htmlspecialchars($issue->getLanguageName())); ?>)
	</TD>

	<TD>
		<?php p(htmlspecialchars($issue->getUrlName())); ?>
	</TD>

	<TD ALIGN="CENTER">
		<?php
		if ($issue->getWorkflowStatus() == 'Y') {
			p(htmlspecialchars($issue->getPublicationDate()));
		} else {
			print putGS("Not published");
		}
		?>
		<?php
		if (count($pendingEvents) > 0) {
			echo "<br>";
			$nextEvent = array_shift($pendingEvents);
			if ($nextEvent->getPublishAction() == 'P') {
				putGS("Publish on: $1", $nextEvent->getActionTime());
			} else {
				putGS("Unpublish on: $1", $nextEvent->getActionTime());
			}
		}
		?>
		<br>
		<?php if ($g_user->hasPermission('ManageIssue')) {
			if ($issue->getWorkflowStatus() == 'Y') {
				$t2 = getGS('Published');
				$t3 = getGS('Not published');
			}
			else {
				$t2 = getGS('Not published');
				$t3 = getGS('Published');
			}
			?>
			<A HREF="/<?php echo $ADMIN; ?>/issues/do_status.php?Pub=<?php p($Pub); ?>&Issue=<?php  p($issue->getIssueNumber()); ?>&Language=<?php p($issue->getLanguageId()); ?>&f_target=index.php&<?php echo SecurityToken::URLParameter(); ?>" onclick="return confirm('<?php  putGS('Are you sure you want to change the issue $1 status from $2 to $3?',$issue->getIssueNumber().'. '.htmlspecialchars($issue->getName()).' ('.htmlspecialchars($issue->getLanguageName()).')',"\'$t2\'","\'$t3\'"); ?>
	');"><?php ($issue->getWorkflowStatus() == 'Y') ? putGS("Unpublish") : putGS("Publish"); ?></A>
			- <A HREF="/<?php echo $ADMIN; ?>/issues/edit.php?Pub=<?php p($Pub); ?>&Issue=<?php  p($issue->getIssueNumber()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><?php  putGS("Schedule"); ?></A>
			<?php
		}
		?>
	</TD>

	<?php  if ($g_user->hasPermission('ManageIssue')) { ?>
	<TD ALIGN="CENTER">
		<A HREF="/<?php echo $ADMIN; ?>/issues/edit.php?Pub=<?php p($Pub); ?>&Issue=<?php  p($issue->getIssueNumber()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/configure.png" alt="<?php  putGS("Configure"); ?>" title="<?php  putGS("Configure"); ?>"  border="0"></A>
	</TD>
	<?php } ?>

	<?php  if ($g_user->hasPermission('ManageIssue')) { ?>
	<TD ALIGN="CENTER">
		<A HREF="/<?php echo $ADMIN; ?>/issues/translate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($issue->getIssueNumber()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/translate.png" alt="<?php  putGS("Translate"); ?>" title="<?php  putGS("Translate"); ?>" border="0"></A>
	</TD>
	<?php  } ?>

	<TD ALIGN="CENTER">
		<A HREF="" ONCLICK="window.open('/<?php echo $ADMIN; ?>/issues/preview.php?Pub=<?php  p($Pub); ?>&Issue=<?php p($issue->getIssueNumber()); ?>&Language=<?php p($issue->getLanguageId()); ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=yes, width=800, height=600'); return false"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/preview.png" alt="<?php  putGS("Preview"); ?>" title="<?php  putGS("Preview"); ?>" border="0"></A>
	</TD>

	<?php
    if ($g_user->hasPermission('DeleteIssue')) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/issues/delete.php?f_publication_id=<?php p($Pub); ?>&f_issue_number=<?php  p($issue->getIssueNumber()); ?>&f_language_id=<?php p($issue->getLanguageId()); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php  putGS('Delete issue $1', htmlspecialchars($issue->getName())); ?>" title="<?php  putGS('Delete issue $1', htmlspecialchars($issue->getName())); ?>"></A>
		</TD>
	<?php  } ?>
	</TR>

	<?php
    $currentIssue = $issue->getIssueNumber();
}
?>
</table>
<table class="indent">
<TR>
	<TD>
		<?php echo $pager->render(); ?>
	</TD>
</TR>
</TABLE>
<?php
}
else { ?>
	<BLOCKQUOTE>
	<LI><?php  putGS('No issues.'); ?></LI>
	</BLOCKQUOTE>
	<?php
} ?>

<?php camp_html_copyright_notice(); ?>
