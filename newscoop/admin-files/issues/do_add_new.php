<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");

use Newscoop\Service\ISyncResourceService;
use Newscoop\Service\IIssueService;
use Newscoop\Service\IOutputService;
//@New theme management
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\IThemeManagementService;
use Newscoop\Service\IOutputSettingIssueService;
use Newscoop\Entity\Output\OutputSettingsIssue;

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('ManageIssue')) {
	camp_html_display_error($translator->trans('You do not have the right to add issues.', array(), 'issues'));
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int');
$f_issue_name = trim(Input::Get('f_issue_name', 'string', ''));
$f_issue_number = trim(Input::Get('f_issue_number', 'int'));
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_url_name = Input::Get('f_url_name');

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid Input: $1', array('$1' => Input::GetErrorString()), 'issues'));
	exit;
}

$backLink = "/$ADMIN/issues/add_new.php?Pub=$f_publication_id";
$created = false;
if ($f_language_id == 0) {
	camp_html_add_msg($translator->trans('You must select a language.'));
}
if (empty($f_issue_name)) {
	camp_html_add_msg($translator->trans('You must fill in the $1 field.', array('$1' => '<B>'.$translator->trans('Name').'</B>')));
}
if ($f_url_name == "") {
	camp_html_add_msg($translator->trans('You must fill in the $1 field.', array('$1' => '<B>'.$translator->trans('URL Name').'</B>')));
}
if (!camp_is_valid_url_name($f_url_name)) {
	camp_html_add_msg($translator->trans('The $1 field may only contain letters, digits and underscore (_) character.', array('$1' => '</B>' . $translator->trans('URL Name') . '</B>')));
}
if (empty($f_issue_number) || !is_numeric($f_issue_number) || ($f_issue_number <= 0)) {
	camp_html_add_msg($translator->trans('You must fill in the $1 field.', array('$1' => '<B>'.$translator->trans('Number').'</B>')));
}

if ($errorMsg = camp_is_issue_conflicting($f_publication_id, $f_issue_number, $f_language_id, $f_url_name, false)) {
	camp_html_add_msg($errorMsg);
}

if (camp_html_has_msgs()) {
	camp_html_goto_page($backLink);
}

$lastIssueObj = Issue::GetLastCreatedIssue($f_publication_id);

$publicationObj = new Publication($f_publication_id);
$newIssueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
$created = $newIssueObj->create($f_url_name, array('Name' => $f_issue_name));

//add default theme
$resourceId = new ResourceId('Publication/Edit');
$syncRsc = $resourceId->getService(ISyncResourceService::NAME);
$outputService = $resourceId->getService(IOutputService::NAME);
$outputSettingIssueService = $resourceId->getService(IOutputSettingIssueService::NAME);
$issueService = $resourceId->getService(IIssueService::NAME);
$themeManagementService = $resourceId->getService(IThemeManagementService::NAME_1);
$publicationThemes = $themeManagementService->getThemes($publicationObj->getPublicationId());

if (is_array($publicationThemes) && count($publicationThemes) > 0) {
    if ($lastIssueObj instanceof Issue) {
        $outSetIssues = $outputSettingIssueService->findByIssue($lastIssueObj->getIssueId());
        $themePath = null;
        if (count($outSetIssues) > 0) {
            $outSetIssue = $outSetIssues[0];
            $themePath = $outSetIssue->getThemePath()->getPath();
        }
        if ($themePath == null) {
            $themePath = $publicationThemes[0]->getPath();
        }
        if ($themePath == null) {
            $f_theme_id = '0';
        } else {
            $f_theme_id = $themePath;
        }
    } else {
        $f_theme_id = $publicationThemes[0]->getPath();
    }

    $issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);

    $outSetIssues = $outputSettingIssueService->findByIssue($issueObj->getIssueId());

    $newOutputSetting = false;
    if (count($outSetIssues) > 0) {
        $outSetIssue = $outSetIssues[0];
    } else {
        $outSetIssue = new OutputSettingsIssue();
        $outSetIssue->setOutput($outputService->findByName('Web'));
        $outSetIssue->setIssue($issueService->getById($issueObj->getIssueId()));
        $newOutputSetting = true;
    }
    $outSetIssue->setThemePath($syncRsc->getThemePath($f_theme_id));
    $outSetIssue->setFrontPage(null);
    $outSetIssue->setSectionPage(null);
    $outSetIssue->setArticlePage(null);

    if (SaaS::singleton()->hasPermission('ManageIssueTemplates')) {
        if($newOutputSetting){
            $outputSettingIssueService->insert($outSetIssue);
        } else {
            $outputSettingIssueService->update($outSetIssue);
        }
    }
    //end to add default theme
} else {
	$f_theme_id = null;
}


if ($created) {
	camp_html_add_msg($translator->trans("Issue created.", array(), 'issues'), "ok");
	camp_html_goto_page("/$ADMIN/issues/edit.php?Pub=$f_publication_id&Issue=$f_issue_number&Language=$f_language_id");
} else {
	camp_html_add_msg($translator->trans('The issue could not be added.', array(), 'issues'));
	camp_html_goto_page($backLink);
}
?>