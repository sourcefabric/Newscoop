<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/topics/topics_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageTopics')) {
	camp_html_display_error(getGS("You do not have the right to change topic name."));
	exit;
}

$f_topic_parent_id = Input::Get('f_topic_parent_id', 'int', 0);
$f_topic_edit_id = Input::Get('f_topic_edit_id', 'int', 0);
$f_name = trim(Input::Get('f_name'));

$correct = true;
$topic =& new Topic($f_topic_parent_id, 1);
$editTopic =& new Topic($f_topic_edit_id, 1);
$Path = camp_topic_path($topic);

if (empty($f_name)) {
	$correct = false; 
	$errorMsgs[] = getGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>');
}

if ($correct) {
	$updated = $editTopic->setName($f_name);
	if ($updated) {
		$logtext = getGS('Topic $1 updated', $topic->getTopicId());
		Log::Message($logtext, $User->getUserName(), 143);
		header("Location: /$ADMIN/topics/edit.php?f_topic_parent_id=$f_topic_parent_id&f_topic_edit_id=$f_topic_edit_id");
		exit;
	}
} else {
	$errorMsgs[] = getGS('The topic name could not be updated.');
}
$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Topics"), "/$ADMIN/topics/");
$crumbs[] = array(getGS("Change topic name"), "");
echo camp_html_breadcrumbs($crumbs);

?>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Topic"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php p($Path);?></TD>
</TR>
</TABLE>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Change topic name"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<BLOCKQUOTE>
	<?php foreach ($errorMsgs as $errorMsg) {
		p($errorMsg);
	}
	?>
    </BLOCKQUOTE>
    </TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/topics/edit.php?f_topic_parent_id=<?php p($f_topic_parent_id); ?>&f_topic_edit_id=<?php  p($f_topic_edit_id); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
