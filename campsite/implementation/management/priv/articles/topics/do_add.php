<?PHP
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/topics/topic_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Topic.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_topic_ids = Input::Get('f_topic_ids', 'array', null, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;	
}

if (!is_null($f_topic_ids)) {
	$articleObj =& new Article($f_language_selected, $f_article_number);
	if (!$articleObj->exists()) {
		camp_html_display_error(getGS('Article does not exist.'), null, true);
		exit;		
	}
	
	if (!$User->hasPermission('AttachTopicToArticle')) {
		camp_html_display_error(getGS("You do not have the right to attach topics to articles."), null, true);
		exit;	
	}
	
	foreach ($f_topic_ids as $topicIdString) {
		list($topicId, $languageId) = split("_", $topicIdString);
		// Verify topic exists
		$tmpTopic =& new Topic($topicId, $languageId);
		if ($tmpTopic->exists()) {
			ArticleTopic::AddTopicToArticle($topicId, $f_article_number);	
		}
	}
}
?>
<script>
<?php if (!is_null($f_topic_ids)) { ?>
window.opener.document.forms.article_edit.onsubmit();
window.opener.document.forms.article_edit.submit();
<?php } ?>
window.close();
</script>
