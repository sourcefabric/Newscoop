<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/SimplePager.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/IssuePublish.php");
camp_load_language("api");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

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
$publicationObj =& new Publication($Pub);
$allIssues = Issue::GetIssues($Pub, null, null, $publicationObj->getLanguageId(), array('LIMIT' => array('START' => $IssOffs, 'MAX_ROWS'=> $ItemsPerPage)));
$totalIssues = Issue::GetNumIssues($Pub);

$pager =& new SimplePager($totalIssues, $ItemsPerPage, "IssOffs_$Pub", "index.php?Pub=$Pub&");

camp_html_content_top(getGS('Issue List'), array('Pub' => $publicationObj));
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/pub/"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/pub/"><B><?php  putGS("Publication List"); ?></B></A></TD>
<?php
if ($User->hasPermission('ManageIssue')) {
	if (Issue::GetNumIssues($Pub) <= 0) {
		?>
			<TD style="padding-left: 20px;"><A HREF="add_new.php?Pub=<?php p($Pub); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
			<TD><A HREF="add_new.php?Pub=<?php p($Pub); ?>"><B><?php  putGS("Add new issue"); ?></B></A></TD>
	<?php  } else { ?>
			<TD style="padding-left: 20px;"><A HREF="qadd.php?Pub=<?php p($Pub); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
			<TD><A HREF="qadd.php?Pub=<?php p($Pub); ?>"><B><?php  putGS("Add new issue"); ?></B></A></TD>
	<?php  }
}
?>
</TR>
</TABLE>
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
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("Published<BR><SMALL>(yyyy-mm-dd)</SMALL>"); ?></B></TD>

		<?php  if ($User->hasPermission('ManageIssue')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("Configure"); ?></B></TD>
		<?php } ?>

		<?php if ($User->hasPermission('Publish')) { ?>
		<TD ALIGN="center" VALIGN="TOP"><B><?php echo str_replace(' ', '<br>', getGS("Scheduled Publishing")); ?></B></TD>
		<?php } ?>

		<?php  if ($User->hasPermission('ManageIssue')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("Translate"); ?></B></TD>
		<?php } ?>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("Preview"); ?></B></TD>

		<?php if ($User->hasPermission('DeleteIssue')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("Delete"); ?></B></TD>
		<?php  } ?>
	</TR>

<?php
$currentIssue = -1;
foreach ($allIssues as $issue) {
	$hasPendingEvents = (count(IssuePublish::GetIssueEvents($issue->getPublicationId(), $issue->getIssueNumber(), $issue->getLanguageId(), false)) > 0);
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
		<?php if ($User->hasPermission('ManageIssue')) { ?>
		<A HREF="/<?php echo $ADMIN; ?>/issues/status.php?Pub=<?php p($Pub); ?>&Issue=<?php  p($issue->getIssueNumber()); ?>&Language=<?php p($issue->getLanguageId()); ?>">
		<?php
		}
		if ($issue->getWorkflowStatus() == 'Y') {
			p(htmlspecialchars($issue->getPublicationDate()));
		} else {
			if ($User->hasPermission('ManageIssue')) {
				print putGS("Publish");
			} else {
				print putGS("No");
			}
		} ?>
		<?php if ($User->hasPermission('ManageIssue')) { ?>
		</A>
		<?php } ?>
	</TD>

	<?php  if ($User->hasPermission('ManageIssue')) { ?>
	<TD ALIGN="CENTER">
		<A HREF="/<?php echo $ADMIN; ?>/issues/edit.php?Pub=<?php p($Pub); ?>&Issue=<?php  p($issue->getIssueNumber()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/configure.png" alt="<?php  putGS("Configure"); ?>" title="<?php  putGS("Configure"); ?>"  border="0"></A>
	</TD>
	<?php } ?>

	<?php if ($User->hasPermission('Publish')) { ?>
	<TD ALIGN="CENTER">
		<A HREF="/<?php echo $ADMIN; ?>/issues/autopublish.php?Pub=<?php p($Pub); ?>&Issue=<?php p($issue->getIssueNumber()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/<?php if ($hasPendingEvents) { ?>automatic_publishing_active.png<?php } else { ?>automatic_publishing.png<?php } ?>" alt="<?php putGS("Scheduled Publishing"); ?>"  title="<?php putGS("Scheduled Publishing"); ?>" border="0"></A>
	</TD>
	<?php } ?>

	<?php  if ($User->hasPermission('ManageIssue')) { ?>
	<TD ALIGN="CENTER">
		<A HREF="/<?php echo $ADMIN; ?>/issues/translate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($issue->getIssueNumber()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/translate.png" alt="<?php  putGS("Translate"); ?>" title="<?php  putGS("Translate"); ?>" border="0"></A>
	</TD>
	<?php  } ?>

	<TD ALIGN="CENTER">
		<A HREF="" ONCLICK="window.open('/<?php echo $ADMIN; ?>/issues/preview.php?Pub=<?php  p($Pub); ?>&Issue=<?php p($issue->getIssueNumber()); ?>&Language=<?php p($issue->getLanguageId()); ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=yes, width=800, height=600'); return false"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/preview.png" alt="<?php  putGS("Preview"); ?>" title="<?php  putGS("Preview"); ?>" border="0"></A>
	</TD>

	<?php
    if ($User->hasPermission('DeleteIssue')) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/issues/do_del.php?Pub=<?php p($Pub); ?>&Issue=<?php  p($issue->getIssueNumber()); ?>&Language=<?php p($issue->getLanguageId()); ?>&IssOffs=<?php echo $IssOffs; ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the issue $1?', htmlspecialchars($issue->getName())); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php  putGS('Delete issue $1', htmlspecialchars($issue->getName())); ?>" title="<?php  putGS('Delete issue $1', htmlspecialchars($issue->getName())); ?>"></A>
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