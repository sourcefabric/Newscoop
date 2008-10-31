<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/topics/topics_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/ArticleTopic.php");

if (!$g_user->hasPermission('ManageTopics')) {
	camp_html_display_error(getGS("You do not have the right to delete topics."));
	exit;
}

$f_topic_language_id = Input::Get('f_topic_language_id', 'int', 0);
$f_topic_delete_id = Input::Get('f_topic_delete_id', 'int', 0);
$errorMsgs = array();
$doDelete = true;
$deleteTopic = new Topic($f_topic_delete_id);
if (($deleteTopic->getNumTranslations() == 1) && $deleteTopic->hasSubtopics()) {
	$doDelete = false;
	$errorMsgs[] = getGS('This topic has subtopics, therefore it cannot be deleted.');
}
$numArticles = count(ArticleTopic::GetArticlesWithTopic($f_topic_delete_id));
if ($numArticles > 0) {
	$doDelete = false;
	$errorMsgs[] = getGS('There are $1 articles using the topic.', $numArticles);
}

if ($doDelete) {
	$deleted = $deleteTopic->delete($f_topic_language_id);
	if ($deleted) {
		camp_html_goto_page("/$ADMIN/topics/index.php");
	}
	else {
		$errorMsgs[] = getGS('The topic $1 could not be deleted.','<B>'.$deleteTopic->getName($f_topic_language_id).'</B>');
	}
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Topics"), "/$ADMIN/topics/");
$crumbs[] = array(getGS("Deleting topic"), "");
echo camp_html_breadcrumbs($crumbs);
?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Deleting topic"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<BLOCKQUOTE>
	<?php foreach ($errorMsgs as $errorMsg) { ?>
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
