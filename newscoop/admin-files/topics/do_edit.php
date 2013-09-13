<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/topics/topics_common.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('ManageTopics')) {
	camp_html_display_error($translator->trans("You do not have the right to change topic name.", array(), 'topics'));
	exit;
}

$f_topic_edit_id = Input::Get('f_topic_edit_id', 'int', 0);
$f_topic_language_id = Input::Get('f_topic_language_id', 'int', 0);
$f_name = trim(Input::Get('f_name', 'string', '', true));

$correct = true;
$editTopic = new Topic($f_topic_edit_id);
$path = camp_topic_path($editTopic, $f_topic_language_id);

$errorMsgs = array();
if (!empty($f_name)) {
	if ($editTopic->setName($f_topic_language_id, $f_name)) {
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
$crumbs[] = array($translator->trans("Change topic name", array(), 'topics'), "");
echo camp_html_breadcrumbs($crumbs);

?>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  echo $translator->trans("Topic"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php p($path);?></TD>
</TR>
</TABLE>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  echo $translator->trans("Change topic name", array(), 'topics'); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<BLOCKQUOTE>
	<?php foreach ($errorMsgs as $errorMsg) { ?>
		<li><?php p($errorMsg); ?></li>
		<?php
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
