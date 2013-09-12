<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/SimplePager.php");
require_once($GLOBALS['g_campsiteDir']."/classes/IssuePublish.php");
$translator = \Zend_Registry::get('container')->getService('translator');

$Pub = Input::Get('Pub', 'int', 0);
$IssOffs = camp_session_get("IssOffs_$Pub", 0);
if ($IssOffs < 0) {
	$IssOffs = 0;
}
$ItemsPerPage = 15;

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', Input::GetErrorString(), array(), 'issues'), $_SERVER['REQUEST_URI']);
	exit;
}
$publicationObj = new Publication($Pub);
$allIssues = Issue::GetIssues($Pub, null, null, null, $publicationObj->getLanguageId(), false, array('LIMIT' => array('START' => $IssOffs, 'MAX_ROWS'=> $ItemsPerPage)), true);
$totalIssues = Issue::GetNumIssues($Pub);

$pager = new SimplePager($totalIssues, $ItemsPerPage, "IssOffs_$Pub", "index.php?Pub=$Pub&");
include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

camp_html_content_top($translator->trans('Issue List'), array('Pub' => $publicationObj));
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/pub/"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/pub/"><B><?php echo $translator->trans("Publication List"); ?></B></A></TD>
<?php
if ($g_user->hasPermission('ManageIssue')) {
	if (Issue::GetNumIssues($Pub) <= 0) {
		?>
            <TD style="padding-left: 20px;"><A HREF="/<?php echo $ADMIN; ?>/issues/add_new.php?Pub=<?php p($Pub); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
            <TD><A HREF="/<?php echo $ADMIN; ?>/issues/add_new.php?Pub=<?php p($Pub); ?>"><B><?php echo $translator->trans("Add new issue"); ?></B></A></TD>
	<?php  } else { ?>
            <TD style="padding-left: 20px;"><A HREF="/<?php echo $ADMIN; ?>/issues/qadd.php?Pub=<?php p($Pub); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
            <TD><A HREF="/<?php echo $ADMIN; ?>/issues/qadd.php?Pub=<?php p($Pub); ?>"><B><?php echo $translator->trans("Add new issue"); ?></B></A></TD>
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
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php echo $translator->trans("Number"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php echo $translator->trans("Name<BR><SMALL>(click to see sections)</SMALL>"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php echo $translator->trans("URL Name"); ?></B></TD>
		<TD ALIGN="center" VALIGN="TOP"><B><?php echo $translator->trans("Publish Date $1", array('$1' => "<br><small>(".$translator->trans("YYYY-MM-DD").")</small>"), 'issues'); ?></B></TD>

		<?php  if ($g_user->hasPermission('ManageIssue')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php echo $translator->trans("Configure"); ?></B></TD>
		<?php } ?>

		<?php  if ($g_user->hasPermission('ManageIssue')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php echo $translator->trans("Translate"); ?></B></TD>
		<?php } ?>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php echo $translator->trans("Preview"); ?></B></TD>

		<?php if ($g_user->hasPermission('DeleteIssue')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php echo $translator->trans("Delete"); ?></B></TD>
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
			print $translator->trans("Not published");
		}
		?>
		<?php
		if (count($pendingEvents) > 0) {
			echo "<br>";
			$nextEvent = array_shift($pendingEvents);
			if ($nextEvent->getPublishAction() == 'P') {
				echo $translator->trans("Publish on: $1", array('$1' => $nextEvent->getActionTime()), 'issues');
			} else {
				echo $translator->trans("Unpublish on: $1", array('$1' => $nextEvent->getActionTime()), 'issues');
			}
		}
		?>
		<br>
		<?php if ($g_user->hasPermission('ManageIssue')) {
			if ($issue->getWorkflowStatus() == 'Y') {
				$t2 = $translator->trans('Published');
				$t3 = $translator->trans('Not published');
			}
			else {
				$t2 = $translator->trans('Not published');
				$t3 = $translator->trans('Published');
			}
			?>
			<A HREF="/<?php echo $ADMIN; ?>/issues/do_status.php?Pub=<?php p($Pub); ?>&Issue=<?php  p($issue->getIssueNumber()); ?>&Language=<?php p($issue->getLanguageId()); ?>&f_target=index.php&<?php echo SecurityToken::URLParameter(); ?>" onclick="return confirm('<?php  echo $translator->trans('Are you sure you want to change the issue $1 status from $2 to $3?', array('$1' => $issue->getIssueNumber().'. '.htmlspecialchars($issue->getName()).' ('.htmlspecialchars($issue->getLanguageName()).')', '$2' => "\'$t2\'", '$3' => "\'$t3\'"), 'issues'); ?>
	');"><?php echo ($issue->getWorkflowStatus() == 'Y') ? $translator->trans("Unpublish") : $translator->trans("Publish"); ?></A>
			- <A HREF="/<?php echo $ADMIN; ?>/issues/edit.php?Pub=<?php p($Pub); ?>&Issue=<?php  p($issue->getIssueNumber()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><?php echo $translator->trans("Schedule", array(), 'issues'); ?></A>
			<?php
		}
		?>
	</TD>

	<?php  if ($g_user->hasPermission('ManageIssue')) { ?>
	<TD ALIGN="CENTER">
		<A HREF="/<?php echo $ADMIN; ?>/issues/edit.php?Pub=<?php p($Pub); ?>&Issue=<?php  p($issue->getIssueNumber()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/configure.png" alt="<?php echo $translator->trans("Configure"); ?>" title="<?php echo $translator->trans("Configure"); ?>"  border="0"></A>
	</TD>
	<?php } ?>

	<?php  if ($g_user->hasPermission('ManageIssue')) { ?>
	<TD ALIGN="CENTER">
		<A HREF="/<?php echo $ADMIN; ?>/issues/translate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($issue->getIssueNumber()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/translate.png" alt="<?php echo $translator->trans("Translate"); ?>" title="<?php echo $translator->trans("Translate"); ?>" border="0"></A>
	</TD>
	<?php  } ?>

	<TD ALIGN="CENTER">
		<A HREF="" ONCLICK="window.open('/<?php echo $ADMIN; ?>/issues/preview.php?Pub=<?php  p($Pub); ?>&Issue=<?php p($issue->getIssueNumber()); ?>&Language=<?php p($issue->getLanguageId()); ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=yes, width=800, height=600'); return false"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/preview.png" alt="<?php echo $translator->trans("Preview"); ?>" title="<?php echo $translator->trans("Preview"); ?>" border="0"></A>
	</TD>

	<?php
    if ($g_user->hasPermission('DeleteIssue')) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/issues/delete.php?f_publication_id=<?php p($Pub); ?>&f_issue_number=<?php  p($issue->getIssueNumber()); ?>&f_language_id=<?php p($issue->getLanguageId()); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php echo $translator->trans('Delete issue $1', array('$1' => htmlspecialchars($issue->getName())), 'issues'); ?>" title="<?php echo $translator->trans('Delete issue $1', array('$1' => htmlspecialchars($issue->getName())), 'issues'); ?>"></A>
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
	<LI><?php echo $translator->trans('No issues.'); ?></LI>
	</BLOCKQUOTE>
	<?php
} ?>

<?php camp_html_copyright_notice(); ?>
