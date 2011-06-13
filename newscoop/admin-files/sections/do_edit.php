<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/sections/section_common.php");
//@New theme management
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\ISectionService;
use Newscoop\Service\IOutputService;
use Newscoop\Service\ISyncResourceService;
use Newscoop\Service\IOutputSettingSectionService;
use Newscoop\Entity\Output\OutputSettingsSection;
//@New theme management

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('ManageSection')) {
    camp_html_display_error(getGS("You do not have the right to add sections."));
    exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
if(SaaS::singleton()->hasPermission('ManageSectionTemplates')) {
	$cSubs = Input::Get('cSubs', 'string', '', true);
} else {
	$cSubs = 'n';
}
$cShortName = trim(Input::Get('cShortName', 'string'));
$cDescription = trim(Input::Get('cDescription'));

if(SaaS::singleton()->hasPermission('ManageSectionTemplates')) {
	$cSectionTplId = Input::Get('cSectionTplId', 'int', 0);
	$cArticleTplId = Input::Get('cArticleTplId', 'int', 0);
} else {
	$sectionObj = new Section($Pub, $Issue, $Language, $Section);
	$cSectionTplId = $sectionObj->getSectionTemplateId() > 0 ? $sectionObj->getSectionTemplateId() : 0;
	$cArticleTplId = $sectionObj->getArticleTemplateId() > 0 ? $sectionObj->getArticleTemplateId() : 0;
}

$cName = Input::Get('cName');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

$issueObj = new Issue($Pub, $Language, $Issue);
$publicationObj = new Publication($Pub);
$sectionObj = new Section($Pub, $Issue, $Language, $Section);

if (!$publicationObj->exists()) {
    camp_html_display_error(getGS('Publication does not exist.'));
    exit;
}
if (!$issueObj->exists()) {
	camp_html_display_error(getGS('No such issue.'));
	exit;
}

$correct = true;
$modified = false;

$errors = array();
if ($cName == "") {
	camp_html_add_msg(getGS('You must fill in the $1 field.','"'.getGS('Name').'"'));
}
if ($cShortName == "")  {
	camp_html_add_msg(getGS('You must fill in the $1 field.','"'.getGS('URL Name').'"'));
}
$isValidShortName = camp_is_valid_url_name($cShortName);

if (!$isValidShortName) {
	camp_html_add_msg(getGS('The $1 field may only contain letters, digits and underscore (_) character.', '"' . getGS('URL Name') . '"'));
}

$editUrl = "/$ADMIN/sections/edit.php?Pub=$Pub&Issue=$Issue&Language=$Language&Section=$Section";
if (!camp_html_has_msgs()) {
	$modified = true;
	$modified &= $sectionObj->setName($cName);
	$modified &= $sectionObj->setDescription($cDescription);

	//@New theme management
	$resourceId = new ResourceId('Section/Edit');
	$outputSettingSectionService = $resourceId->getService(IOutputSettingSectionService::NAME);
	$outputService = $resourceId->getService(IOutputService::NAME);
	$sectionService = $resourceId->getService(ISectionService::NAME);
	$syncRsc = $resourceId->getService(ISyncResourceService::NAME);
	
	$newOutputSetting = false;
	
	$dSection = $sectionService->getById($sectionObj->getSectionId());
	$outSetSections = $outputSettingSectionService->findBySection($dSection);
	if(count($outSetSections) > 0){
		$outSetSection = $outSetSections[0];
	} else {
		$outSetSection = new OutputSettingsSection();
		$outSetSection->setOutput($outputService->findByName('Web'));
		$outSetSection->setSection($dSection);
		$newOutputSetting = true;
	}

	if($cSectionTplId != null && $cSectionTplId != '0'){
		$outSetSection->setSectionPage($syncRsc->getResource('sectionPage', $cSectionTplId));
	} else {
		$outSetSection->setSectionPage(null);
	}
	if($cArticleTplId != null && $cArticleTplId != '0'){
		$outSetSection->setArticlePage($syncRsc->getResource('articlePage', $cArticleTplId));
	} else {
		$outSetSection->setArticlePage(null);
	}
	//@New theme management
	
	if ($cSubs == "a") {
	$numSubscriptionsAdded = Subscription::AddSectionToAllSubscriptions($Pub, $Section);
		if ($numSubscriptionsAdded < 0) {
			$errors[] = getGS('Error updating subscriptions.');
		}
	}
	if ($cSubs == "d") {
		$numSubscriptionsDeleted = Subscription::DeleteSubscriptionsInSection($Pub, $Section);
		if ($numSubscriptionsDeleted < 0) {
			$errors[] = getGS('Error updating subscriptions.');
		}
	}

	$conflictingSection = array_pop(Section::GetSections($Pub, $Issue, $Language, $cShortName, null, null, true));
	if (is_object($conflictingSection) && ($conflictingSection->getSectionNumber() != $Section)) {
		$conflictingSectionLink = "/$ADMIN/sections/edit.php?Pub=$Pub&Issue=$Issue&Language=$Language&Section=".$conflictingSection->getSectionNumber();

		$msg = getGS('The URL name must be unique for all sections in this issue.<br>The URL name you specified ("$1") conflicts with section "$2$3. $4$5"',
			$cShortName,
			"<a href='$conflictingSectionLink' class='error_message' style='color:#E30000;'>",
			$conflictingSection->getSectionNumber(),
			htmlspecialchars($conflictingSection->getName()),
			"</a>");
		camp_html_add_msg($msg);
		// placeholder for localization string - we might need this later.
		// getGS("The section could not be changed.");
	} else {
		$modified &= $sectionObj->setUrlName($cShortName);
		//@New theme management
		if($newOutputSetting){
			$outputSettingSectionService->insert($outSetSection);
		} else {
			$outputSettingSectionService->update($outSetSection);
		}
		//@New theme management
		camp_html_add_msg(getGS("Section updated"), "ok");
	}
	$logtext = getGS('Section "$1" ($2) updated. (Publication: $3, Issue: $4)',
			 $cName, $Section, $publicationObj->getPublicationId(), $issueObj->getIssueNumber());
	Log::Message($logtext, $g_user->getUserId(), 21);
}
camp_html_goto_page($editUrl);

?>