<?PHP
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticlePublish.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission("Publish")) {
	CampsiteInterface::DisplayError("You do not have the right to schedule issues or articles for automatic publishing." );
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
$publishTime = Input::Get('publish_time');
$BackLink = Input::Get('Back', 'string', "/$ADMIN/pub/issues/sections/articles/index.php", true);

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(array('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;	
}

$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	CampsiteInterface::DisplayError('Publication does not exist.', $BackLink);
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	CampsiteInterface::DisplayError('Issue does not exist.', $BackLink);
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	CampsiteInterface::DisplayError('Section does not exist.', $BackLink);
	exit;	
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	CampsiteInterface::DisplayError('Article does not exist.', $BackLink);
	exit;
}

$languageObj =& new Language($Language);
$sLanguageObj =& new Language($sLanguage);

$articlePublishObj =& new ArticlePublish($Article, $sLanguage, $publishTime);
if ($articlePublishObj->exists()) {
	$articlePublishObj->delete();
}
$redirect = CampsiteInterface::ArticleUrl($articleObj, $Language, "autopublish.php", $BackLink);
header("Location: $redirect");
exit;
?>