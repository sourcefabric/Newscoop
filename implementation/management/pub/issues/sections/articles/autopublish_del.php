<?PHP
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticlePublish.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission("Publish")) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS("You do not have the right to schedule issues or articles for automatic publishing." )));
	exit;
}

$Pub = Input::get('Pub', 'int', 0);
$Issue = Input::get('Issue', 'int', 0);
$Section = Input::get('Section', 'int', 0);
$Language = Input::get('Language', 'int', 0);
$sLanguage = Input::get('sLanguage', 'int', 0);
$Article = Input::get('Article', 'int', 0);
$publishTime = Input::get('publish_time');
$BackLink = Input::get('Back', 'string', "/$ADMIN/pub/issues/sections/articles/index.php", true);

if (!Input::isValid()) {
	header("Location: /$ADMIN/logout.php");
	exit;	
}

$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Publication does not exist.')));
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Issue does not exist.')));
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Section does not exist.')));
	exit;	
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Article does not exist.')));
	exit;
}

$languageObj =& new Language($Language);
$sLanguageObj =& new Language($sLanguage);

$articlePublishObj =& new ArticlePublish($Article, $sLanguage, $publishTime);
if ($articlePublishObj->exists()) {
	$articlePublishObj->delete();
}
header("Location: /$ADMIN/pub/issues/sections/articles/autopublish.php?Pub=$Pub&Issue=$Issue&Section=$Section&Article=$Article&Language=$Language&sLanguage=$sLanguage");
exit;
?>