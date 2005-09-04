<?PHP
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/topics/topic_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Topic.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
$TopicId = Input::Get('IdCateg', 'int', 0, true);
$TopicOffset = Input::Get('CatOffs', 'int', 0, true);
$DeleteTopicId = Input::Get('DelTopic', 'int', 0);

if ($TopicOffset < 0) {
	$TopicOffset = 0;
}
$searchTopicsString = trim(Input::Get('search_topics_string', 'string', '', true));

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;	
}

$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	camp_html_display_error(getGS('Publication does not exist.'));
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	camp_html_display_error(getGS('Issue does not exist.'));
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	camp_html_display_error(getGS('Section does not exist.'));
	exit;		
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('Article does not exist.'));
	exit;		
}

$topic =& new Topic($DeleteTopicId, $sLanguage);
if (!$topic->exists()) {
	camp_html_display_error(getGS('Topic does not exist.'));
	exit;	
}

if (!$articleObj->userCanModify($User)) {
	camp_html_display_error(getGS("You do not have the right to add topics to article."));
	exit;	
}
ArticleTopic::RemoveTopicFromArticle($DeleteTopicId, $Article);

$logtext = getGS('Article topic $1 deleted', $topic->getName()); 
Log::Message($logtext, $User->getUserName(), 145);

header("Location: index.php?Pub=$Pub&Issue=$Issue&Section=$Section&Article=$Article&Language=$Language&sLanguage=$sLanguage&IdCateg=$TopicId&CatOffs=$TopicOffset");
exit;

?>