<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/topics/topics_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageTopics')) {
	camp_html_display_error(getGS("You do not have the right to add topics."));
	exit;
}

$f_topic_parent_id = Input::Get('f_topic_parent_id', 'int', 0);
$f_name = trim(Input::Get('f_name'));
$correct = true;
$created = false;
$topicParent =& new Topic($f_topic_parent_id);
$Path = camp_topic_path($topicParent);

$errorMsgs = array();
if (empty($f_name)) {
	$correct = false; 
	$errorMsgs[] = getGS('You must fill in the $1 field.','<B>'.getGS('Name').'</B>'); 
}

if ($correct) {
	$topic =& new Topic();
	$created = $topic->create(array("Name" => $f_name, "ParentId" => $f_topic_parent_id));
	if ($created) {
		header("Location: /$ADMIN/topics/index.php?f_topic_parent_id=$f_topic_parent_id");
		exit;
	}
	else {
		$errorMsgs[] = getGS('The topic could not be added.');
	}
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Topics"), "/$ADMIN/topics/");
$crumbs[] = array(getGS("Adding new topic"), "");
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
		<B> <?php  putGS("Adding new topic"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<?php 
		foreach ($errorMsgs as $errorMsg) { ?>
			<li><?php p($errorMsg); ?></li>
			<?PHP
		}
		?>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/topics/add.php?f_topic_parent_id=<?php p($f_topic_parent_id);?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
