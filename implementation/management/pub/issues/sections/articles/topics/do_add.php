<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/pub/issues/sections/articles/article_common.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Topic.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /priv/logout.php");
	exit;
}
if (!$User->hasPermission('ChangeArticle')) {
	header('Location: /priv/ad.php?ADReason='.urlencode(getGS("You do not have the right to add topics to article.")));
	exit;	
}

$Pub = Input::get('Pub', 'int', 0);
$Issue = Input::get('Issue', 'int', 0);
$Section = Input::get('Section', 'int', 0);
$Language = Input::get('Language', 'int', 0);
$sLanguage = Input::get('sLanguage', 'int', 0);
$Article = Input::get('Article', 'int', 0);
$TopicId = Input::get('IdCateg', 'int', 0, true);
$TopicOffset = Input::get('CatOffs', 'int', 0, true);
$AddTopicId = Input::get('AddTopic', 'int', 0);

if ($TopicOffset < 0) {
	$TopicOffset = 0;
}
$searchTopicsString = trim(Input::get('search_topics_string', 'string', '', true));

if (!Input::isValid()) {
	header("Location: /priv/logout.php");
	exit;	
}

$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	header("Location: /priv/logout.php");
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	header("Location: /priv/logout.php");
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	header("Location: /priv/logout.php");
	exit;		
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	header("Location: /priv/logout.php");
	exit;		
}

$topic =& new Topic($AddTopicId, $sLanguage);
if (!$topic->exists()) {
	header("Location: /priv/logout.php");
	exit;		
}

ArticleTopic::AddTopicToArticle($AddTopicId, $Article);

$logtext = getGS('Topic $1 added to article', $topic->getName());
Log::Message($logtext, $User->getUserName(), 144);

header("Location: index.php?Pub=$Pub&Issue=$Issue&Section=$Section&Article=$Article&Language=$Language&sLanguage=$sLanguage&IdCateg=$TopicId&CatOffs=$TopicOffset");
exit;
?>