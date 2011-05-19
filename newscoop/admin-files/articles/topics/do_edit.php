<?PHP
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/topics/topic_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Topic.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DbObjectArray.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleTopic.php');

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_topic_ids = Input::Get('f_topic_ids', 'array', array(), true);
$articleTopics = ArticleTopic::GetArticleTopics($f_article_number);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), null, true);
	exit;
}

if (!$g_user->hasPermission('AttachTopicToArticle')) {
	camp_html_display_error(getGS("You do not have the right to detach topics from articles."), null, true);
	exit;
}

// delete
foreach ($articleTopics as $topic) {
    if (!in_array($topic->getTopicId(), $f_topic_ids)) {
        ArticleTopic::RemoveTopicFromArticle($topic->getTopicId(), $f_article_number);
    } else {
        unset($f_topic_ids[array_search($topic->getTopicId(), $f_topic_ids)]);
    }
}

// insert rest
foreach ($f_topic_ids as $topicIdString) {
    // Verify topic exists
    $tmpTopic = new Topic($topicIdString);
    if ($tmpTopic->exists()) {
        ArticleTopic::AddTopicToArticle($topicIdString, $f_article_number);
    }
}

?>

<script type="text/javascript">
<?php if (!is_null($f_topic_ids)) { ?>
try {
    parent.$.fancybox.reload = true;
    parent.$.fancybox.message = '<?php putGS('Topics updated.'); ?>';
} catch (e) {}
<?php } ?>
parent.$.fancybox.close();
</script>
