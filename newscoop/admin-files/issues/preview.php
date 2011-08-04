<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Template.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Alias.php');



use Newscoop\Service\ISyncResourceService;
use Newscoop\Service\IIssueService;
use Newscoop\Service\IOutputService;

//@New theme management
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\IThemeManagementService;
use Newscoop\Service\IOutputSettingIssueService;
use Newscoop\Entity\Output\OutputSettingsIssue;
//@New theme management


$Language = Input::Get('Language', 'int', 0);
$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);

$errorStr = "";
$languageObj = new Language($Language);
if (!$languageObj->exists()) {
	$errorStr = getGS('There was an error reading the language parameter.');
}
if ($errorStr == "") {
	$publicationObj = new Publication($Pub);
	if (!$publicationObj->exists())
	$errorStr = getGS('There was an error reading the publication parameter.');
}
if ($errorStr == "") {
	$issueObj = new Issue($Pub, $Language, $Issue);
	if (!$issueObj->exists())
	$errorStr = getGS('There was an error reading the issue parameter.');
}

if ($errorStr != "") {
	camp_html_display_error($errorStr, null, true);
}


//allow preview by creating defaut OutputSettingsIssue
$resourceId = new ResourceId('Publication/Edit');
$themeManagementService = $resourceId->getService(IThemeManagementService::NAME_1);
$outputSettingIssueService = $resourceId->getService(IOutputSettingIssueService::NAME);
$outputService = $resourceId->getService(IOutputService::NAME);
$issueService = $resourceId->getService(IIssueService::NAME);
$syncRsc = $resourceId->getService(ISyncResourceService::NAME);

$outSetIssues = $outputSettingIssueService->findByIssue($issueObj->getIssueId());
if(count($outSetIssues) == 0){
    $publicationThemes = $themeManagementService->getThemes($publicationObj->getPublicationId());
    $publicationHasThemes = count($publicationThemes) > 0;
    if($publicationHasThemes){
        $themePath = $publicationThemes[0]->getPath();
        $outSetIssue = new OutputSettingsIssue();
        $outSetIssue->setOutput($outputService->findByName('Web'));
        $outSetIssue->setIssue($issueService->getById($issueObj->getIssueId()));
        $outSetIssue->setThemePath($syncRsc->getThemePath($themePath));
        $outSetIssue->setFrontPage(null);
        $outSetIssue->setSectionPage(null);
        $outSetIssue->setArticlePage(null);
        $issueObj->commit();
        $outputSettingIssueService->insert($outSetIssue);
    }
}

if (!isset($_SERVER['SERVER_PORT']))
{
	$_SERVER['SERVER_PORT'] = 80;
}
$scheme = $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';
$siteAlias = new Alias($publicationObj->getDefaultAliasId());
$websiteURL = $scheme.$siteAlias->getName();

$accessParams = "LoginUserId=" . $g_user->getUserId() . "&LoginUserKey=" . $g_user->getKeyId()
. "&AdminAccess=all";
$urlType = $publicationObj->getProperty('IdURLType');
if ($urlType == 1) {
	$templateObj = new Template($templateId);
	$url = "$websiteURL"  . $Campsite['SUBDIR'] . "/tpl/" . $templateObj->getName()
	. "?IdLanguage=$Language&IdPublication=$Pub&NrIssue=$Issue&$accessParams";
} else {
	$url = "$websiteURL" . $Campsite['SUBDIR'] . '/' . $languageObj->getCode()
	. "/" . $issueObj->getUrlName() . "?$accessParams";
}

$selectedLanguage = (int)CampRequest::GetVar('Language');
$url .= "&previewLang=$selectedLanguage";

if ($g_user->hasPermission("ManageTempl") || $g_user->hasPermission("DeleteTempl")) {
	// Show dual-pane view for those with template management priviledges
	?>
<FRAMESET ROWS="60%,*" BORDER="1">
	<FRAME SRC="<?php echo "$url&preview=on"; ?>" NAME="body"
		FRAMEBORDER="1">
	<FRAME NAME="e" SRC="empty.php" FRAMEBORDER="1">
</FRAMESET>
	<?php
} else {
	// Show single pane for everyone else.
	?>
<FRAMESET ROWS="100%">
	<FRAME SRC="<?php print "$url&preview=on"; ?>" NAME="body"
		FRAMEBORDER="1">
</FRAMESET>
	<?php
}
?>
