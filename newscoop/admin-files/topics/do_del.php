<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/topics/topics_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/ArticleTopic.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('ManageTopics')) {
	camp_html_display_error($translator->trans("You do not have the right to delete topics.", array(), 'topics'));
	exit;
}

$f_confirmed = Input::Get('f_confirmed', 'int', 0);
$f_topic_language_id = Input::Get('f_topic_language_id', 'int', 0);
$f_topic_delete_id = Input::Get('f_topic_delete_id', 'int', 0);
$errorMsgs = array();
$doDelete = true;
$deleteTopic = new Topic($f_topic_delete_id);

if ($deleteTopic->hasSubtopics()) {
	$doDelete = false;
	$errorMsgs[] = $translator->trans('This topic has subtopics, therefore it cannot be deleted.', array(), 'topics');
}
$numArticles = count(ArticleTopic::GetArticlesWithTopic($f_topic_delete_id));
if ($numArticles > 0) {
	$doDelete = false;
	$errorMsgs[] = $translator->trans('There are $1 articles using the topic.', array('$1' => $numArticles), 'topics');
}

if ($f_confirmed == 1) {
    // get a list of subtopics
    $deleteTopics = $deleteTopic->getSubtopics();
    // detach all subtopics from all articles
    foreach ($deleteTopics as $topic) {
        ArticleTopic::RemoveTopicFromArticles($topic->getTopicId());
    }
    // delete all subtopics
    foreach ($deleteTopics as $topic) {
        $topic->delete($f_topic_language_id);
    }
    $doDelete = true;
}

if ($doDelete) {
    ArticleTopic::RemoveTopicFromArticles($deleteTopic->getTopicId());
    $deleted = $deleteTopic->delete($f_topic_language_id);
	if ($deleted) {
	    camp_html_add_msg($translator->trans("Topic was deleted.", array(), 'topics'), "ok");
		camp_html_goto_page("/$ADMIN/topics/index.php");
	}
	else {
		$errorMsgs[] = $translator->trans('The topic $1 could not be deleted.', array('$1' => '<B>'.$deleteTopic->getName($f_topic_language_id).'</B>'), 'topics');
	}
}

$crumbs = array();
$crumbs[] = array($translator->trans("Configure"), "");
$crumbs[] = array($translator->trans("Topics"), "/$ADMIN/topics/");
$crumbs[] = array($translator->trans("Deleting topic", array(), 'topics'), "");
echo camp_html_breadcrumbs($crumbs);
?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  echo $translator->trans("Deleting topic", array(), 'topics'); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<BLOCKQUOTE>
	<?php
        if (!empty($errorMsgs)) {
            foreach ($errorMsgs as $errorMsg) {
                ?>
                <li><?php p($errorMsg); ?></li>
                <?php
            }
            ?>
            <li><?php echo $translator->trans("If you continue, topic and all subtopics will be detached from all articles, and deleted.", array(), 'topics'); ?></li>
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
    <?php
        if ($doDelete == false) {
            ?>
            <?php echo SecurityToken::FormParameter(); ?>
            <INPUT TYPE="button" class="button" VALUE="<?php echo $translator->trans('Delete anyway', array(), 'topics'); ?>" ONCLICK="location.href=location.href + '&f_confirmed=1'">
            <?php
        }
    ?>
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
