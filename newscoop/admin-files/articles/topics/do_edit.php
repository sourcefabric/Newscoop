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

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

$f_search = Input::Get('search', 'text', '');
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_topic_ids = Input::Get('f_topic_ids', 'array', array(), true);
$articleTopics = ArticleTopic::GetArticleTopics($f_article_number);
$updated = false;

$articleObj = new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
    camp_html_display_error(getGS('No such article.'), $BackLink);
    exit;
}

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), null, true);
	exit;
}

if (!$g_user->hasPermission('AttachTopicToArticle')) {
	camp_html_display_error($translator->trans("You do not have the right to detach topics from articles.", array(), 'article_topics'), null, true);
	exit;
}

// delete
foreach ($articleTopics as $topic) {
    if (!in_array($topic->getTopicId(), $f_topic_ids)) {
        $updated = true;
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
        $updated = true;
        ArticleTopic::AddTopicToArticle($topicIdString, $f_article_number);
    }
}

// attach new topic
if ($f_search) {
    $topicService = \Zend_Registry::get('container')->getService('topic');
    $tmpTopic = $topicService->getTopicByIdOrName($f_search, $f_language_selected);
    if ($tmpTopic) {
        $updated = true;
        $topicService->addTopicToArticle($tmpTopic->getTopicId(), $f_article_number);
    }
}

if ($updated) {
    // Make sure that the time_updated timestamp is updated
    $articleObj->setProperty('time_updated', 'NOW()', true, true);
}

$cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
$cacheService->clearNamespace('article');

?>

<script type="text/javascript">
<?php if (!is_null($f_topic_ids)) { ?>
try {
    parent.$.fancybox.reload = true;
    parent.$.fancybox.message = '<?php echo $translator->trans('Topics updated.', array(), 'article_topics'); ?>';
} catch (e) {}
<?php } ?>
parent.$.fancybox.close();
</script>
