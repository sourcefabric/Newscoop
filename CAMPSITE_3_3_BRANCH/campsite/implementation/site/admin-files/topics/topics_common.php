<?PHP
camp_load_translation_strings("topics");
require_once($GLOBALS['g_campsiteDir']."/classes/Language.php");
require_once($GLOBALS['g_campsiteDir']."/classes/User.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Topic.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Input.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Log.php");

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