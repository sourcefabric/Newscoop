<?PHP
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files("topics");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/User.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Topic.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Input.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

function camp_topic_path($p_topic)
{
	$path = "<A HREF=index.php>".getGS("Top")."</A>";
	$topicPath = $p_topic->getPath();
	foreach ($topicPath as $tmpTopic) {
		$path .= "/<A HREF=index.php?f_topic_parent_id=".$tmpTopic->getTopicId()."> ".$tmpTopic->getName()."</A>";	
	}
	return $path;
}
?>