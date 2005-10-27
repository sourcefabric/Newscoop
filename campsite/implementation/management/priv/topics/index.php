<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/topics/topics_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$f_topic_parent_id = Input::Get('f_topic_parent_id', 'int', 0);
$topic =& new Topic($f_topic_parent_id);
$Path = camp_topic_path($topic);
$subtopics = $topic->getSubtopics();

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Topics"), "");
echo camp_html_breadcrumbs($crumbs);
?>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Topic"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php p($Path);?></TD>
</TR>
</TABLE>
<P>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%" class="action_buttons">
<TR>
	<?php  if ($User->hasPermission("ManageTopics")) { ?>	
	<TD ALIGN="LEFT">
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><A HREF="add.php?f_topic_parent_id=<?php p($f_topic_parent_id);?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
			<TD><A HREF="add.php?f_topic_parent_id=<?php p($f_topic_parent_id);?>"><B><?php  putGS("Add new topic"); ?></B></A></TD>
		</TR>
		</TABLE>
	</TD>
	<?php  } ?>
</TABLE>
<p>
<?PHP
if (count($subtopics) == 0) { ?>
	<BLOCKQUOTE>
	<LI><?php  putGS('No topics'); ?></LI>
	</BLOCKQUOTE>
	<?php  
} else {
?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Name"); ?></B></TD>
	<?php  if ($User->hasPermission("ManageTopics")) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP" ><B><?php  putGS("Change"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP" ><B><?php  putGS("Delete"); ?></B></TD>
	<?php  } ?>
</TR>

<?php 
$color= 0;
foreach ($subtopics as $subtopic) { ?>
	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD>
			<A HREF="index.php?f_topic_parent_id=<?php p($subtopic->getTopicId());?>"><?php p(htmlspecialchars($subtopic->getName())); ?></A>
		</TD>
		<?php  if ($User->hasPermission("ManageTopics")) { ?>
		<TD ALIGN="CENTER">
			<A HREF="edit.php?f_topic_parent_id=<?php p($f_topic_parent_id);?>&f_topic_edit_id=<?php p($subtopic->getTopicId()); ?>"><?php  putGS("Change"); ?></A>
		</TD>
		<TD ALIGN="CENTER">
			<A HREF="do_del.php?f_topic_parent_id=<?php p($f_topic_parent_id);?>&f_topic_delete_id=<?php p($subtopic->getTopicId()); ?>" onclick="return confirm('<?php putGS('Do you want to delete the topic $1?',htmlspecialchars($subtopic->getName())); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php  putGS('Delete topic $1', htmlspecialchars($subtopic->getName())); ?>" TITLE="<?php  putGS('Delete topic $1', htmlspecialchars($subtopic->getName())); ?>" ></A>
		</TD>
<?php  } ?>
    </TR>
	<?php } ?>
<?php } ?>
<?php camp_html_copyright_notice(); ?>
