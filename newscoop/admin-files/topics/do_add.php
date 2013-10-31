<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/topics/topics_common.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('ManageTopics')) {
	camp_html_display_error($translator->trans("You do not have the right to add topics.", array(), 'topics'));
	exit;
}

$f_topic_parent_id = Input::Get('f_topic_parent_id', 'int', 0);
// $f_topic_id is only for the case of translating a topic.
$f_topic_id = Input::Get('f_topic_id', 'int', 0, true);
$f_topic_language_id = Input::Get('f_topic_language_id', 'int', 0);
$f_topic_name = trim(Input::Get('f_topic_name'));
$correct = true;
$created = false;
$topicParent = new Topic($f_topic_parent_id);
$Path = camp_topic_path($topicParent, $f_topic_language_id);

$errorMsgs = array();
if (empty($f_topic_name)) {
	$correct = false;
	$errorMsgs[] = $translator->trans('You must fill in the $1 field.', array('$1' => '<B>'.$translator->trans('Name').'</B>'));
}
if ($f_topic_language_id <= 0) {
	$correct = false;
	$errorMsgs[] = $translator->trans('You must choose a language for the topic.', array(), 'topics');
}

if (!empty($f_topic_name)) {
	if ($f_topic_id == 0) {
		// Create new topic
		$topic = new Topic();
		$created = $topic->create(array('parent_id' => $f_topic_parent_id,
		'names'=>array($f_topic_language_id=>$f_topic_name)));
	} else {
		// Translate existing topic
		$topic = new Topic($f_topic_id);
		$created = $topic->setName($f_topic_language_id, $f_topic_name);
	}
	if ($created) {
		camp_html_goto_page("/$ADMIN/topics/index.php");
	} else {
		$errorMsgs[] = $translator->trans('The topic name is already in use by another topic.', array(), 'topics');
	}
} else {
	$errorMsgs[] = $translator->trans('You must fill in the $1 field.', array('$1' => '<B>'.$translator->trans('Name').'</B>'));
}

$crumbs = array();
$crumbs[] = array($translator->trans("Configure"), "");
$crumbs[] = array($translator->trans("Topics"), "/$ADMIN/topics/");
$crumbs[] = array($translator->trans("Adding new topic", array(), 'topics'), "");
echo camp_html_breadcrumbs($crumbs);

?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  echo $translator->trans("Topic"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php p($Path);?></TD>
</TR>
</TABLE>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  echo $translator->trans("Adding new topic", array(), 'topics'); ?> </B>
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
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/topics/index.php'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
