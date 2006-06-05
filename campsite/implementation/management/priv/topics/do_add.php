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
// $f_topic_id is only for the case of translating a topic.
$f_topic_id = Input::Get('f_topic_id', 'int', 0, true);
$f_topic_language_id = Input::Get('f_topic_language_id', 'int', 0);
$f_topic_name = trim(Input::Get('f_topic_name'));
$correct = true;
$created = false;
$topicParent =& new Topic($f_topic_parent_id);
$Path = camp_topic_path($topicParent, $f_topic_language_id);

$errorMsgs = array();
if (empty($f_topic_name)) {
	$correct = false;
	$errorMsgs[] = getGS('You must fill in the $1 field.','<B>'.getGS('Name').'</B>');
}
if ($f_topic_language_id <= 0) {
	$correct = false; 
	$errorMsgs[] = getGS('You must choose a language for the topic.'); 
}

if (!empty($f_topic_name)) {
	if ($f_topic_id == 0) {
		// Create new topic
		$topic =& new Topic();
		$created = $topic->create(array('Name' => $f_topic_name,
										'ParentId' => $f_topic_parent_id,
										'LanguageId'=>$f_topic_language_id));
	} else {
		// Translate existing topic
		$topic =& new Topic($f_topic_id);
		$created = $topic->setName($f_topic_language_id, $f_topic_name);
	}
	if ($created) {
		header("Location: /$ADMIN/topics/index.php");
		exit;
	} else {
		$errorMsgs[] = getGS('The topic name is already in use by another topic.');
	}
} else {
	$errorMsgs[] = getGS('You must fill in the $1 field.','<B>'.getGS('Name').'</B>'); 
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
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/topics/index.php'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
