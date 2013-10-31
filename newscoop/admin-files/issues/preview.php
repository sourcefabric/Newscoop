<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");
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

$translator = \Zend_Registry::get('container')->getService('translator');
$Language = Input::Get('Language', 'int', 0);
$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);

$errorStr = "";
$languageObj = new Language($Language);
if (!$languageObj->exists()) {
	$errorStr = $translator->trans('There was an error reading the language parameter.', array(), 'issues');
}
if ($errorStr == "") {
	$publicationObj = new Publication($Pub);
	if (!$publicationObj->exists())
	$errorStr = $translator->trans('There was an error reading the publication parameter.', array(), 'issues');
}
if ($errorStr == "") {
	$issueObj = new Issue($Pub, $Language, $Issue);
	if (!$issueObj->exists())
	$errorStr = $translator->trans('There was an error reading the issue parameter.', array(), 'issues');
}

if ($errorStr != "") {
	camp_html_display_error($errorStr, null, true);
}

$resourceId = new ResourceId('Publication/Edit');
$themeManagementService = $resourceId->getService(IThemeManagementService::NAME_1);
/* @var $themeManagementService \Newscoop\Service\Implementation\ThemeManagementServiceLocal */
$outputSettingIssueService = $resourceId->getService(IOutputSettingIssueService::NAME);
/* @var $outputSettingIssueService \Newscoop\Service\Implementation\OutputSettingIssueServiceDoctrine */
$issueService = $resourceId->getService(IIssueService::NAME);
/* @var $issueService \Newscoop\Service\Implementation\IssueServiceDoctrine */
$outputService = $resourceId->getService(IOutputService::NAME);
$syncRsc = $resourceId->getService(ISyncResourceService::NAME);

$outputIssueSettings = current($outputSettingIssueService->findByIssue($issueObj->getIssueId()));
/* @var $outputIssueSettings \Newscoop\Entity\Output\OutputSettingsIssue */

$publicationThemes = $themeManagementService->getThemes($publicationObj->getPublicationId());

if (!$outputIssueSettings) {
    if (count($publicationThemes) > 0) {
        $themePath = $publicationThemes[0]->getPath();
        $outputIssueSettings = new OutputSettingsIssue();
        $outputIssueSettings->setOutput($outputService->findByName('Web'));
        $outputIssueSettings->setIssue($issueService->getById($issueObj->getIssueId()));
        $outputIssueSettings->setThemePath($syncRsc->getThemePath($themePath));
        $outputIssueSettings->setFrontPage(null);
        $outputIssueSettings->setSectionPage(null);
        $outputIssueSettings->setArticlePage(null);
        $outputSettingIssueService->insert($outputIssueSettings);
    } else {
        $errorStr = $translator->trans('This issue cannot be previewed. Please make sure the publication has a theme assigned.', array(), 'issues');
        camp_html_display_error($errorStr, null, true);
    }
} else {
    $themePath = $outputIssueSettings->getThemePath()->getPath();
}
$frontPage = $outputIssueSettings->getFrontPage();
if (is_null($frontPage)) {
    foreach ($publicationThemes as $publicationTheme) {
        if ($publicationTheme->getPath() == $themePath) {
            $themeOutSettings = $themeManagementService->findOutputSetting($publicationTheme, $outputService->findByName('Web'));
            $frontPage = $themeOutSettings->getFrontPage();
        }
    }
}
$templateId = $frontPage->getPath();
$templateName = substr($templateId, strlen($themePath));

if (!$templateId) {
    $errorStr = $translator->trans('This issue cannot be previewed. Please make sure it has the front template selected.', array(), 'issues');
    camp_html_display_error($errorStr, null, true);
}

if (!isset($_SERVER['SERVER_PORT'])) {
	$_SERVER['SERVER_PORT'] = 80;
}
$scheme = $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';
$siteAlias = new Alias($publicationObj->getDefaultAliasId());
$websiteURL = $scheme.$siteAlias->getName();

$accessParams = "";
$urlType = $publicationObj->getProperty('IdURLType');
if ($urlType == 1) {
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
