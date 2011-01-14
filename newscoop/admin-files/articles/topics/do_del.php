<?PHP
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/topics/topic_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Topic.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DbObjectArray.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleTopic.php');

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_topic_id = Input::Get('f_topic_id', 'int', 0, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), null, true);
	exit;
}

if (!$g_user->hasPermission('AttachTopicToArticle')) {
	camp_html_display_error(getGS("You do not have the right to detach topics from articles."), null, true);
	exit;
}

$articleObj = new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('Article does not exist.'), null, true);
	exit;
}
$topicObj = new Topic($f_topic_id);
if (!$topicObj->exists()) {
	camp_html_display_error(getGS('Topic does not exist.'), null, true);
	exit;
}

ArticleTopic::RemoveTopicFromArticle($f_topic_id, $f_article_number);
$topicName = $topicObj->getName($f_language_selected);
if (empty($topicName)) {
	$topicName = $topicObj->getName(1);
}
camp_html_add_msg(getGS("The topic '$1' has been removed from article.", $topicName), "ok");
$url = camp_html_article_url($articleObj, $f_language_id, "edit.php");
camp_html_goto_page($url);

?>