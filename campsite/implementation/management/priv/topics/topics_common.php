<?PHP
camp_load_translation_strings("topics");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/User.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Topic.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Input.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

function camp_topic_path($p_topic, $p_languageId)
{
	$path = getGS("Top")." ";
	$topicPath = $p_topic->getPath();
	foreach ($topicPath as $tmpTopic) {
		$name = htmlspecialchars($tmpTopic->getName($p_languageId));
		if (empty($name)) {
			$name = "-----";
		}
		$path .= "/ $name ";
	}
	return $path;
}
?>