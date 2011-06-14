<?php
require_once($GLOBALS['g_campsiteDir'] . "/$ADMIN_DIR/sections/section_common.php");
require_once($GLOBALS['g_campsiteDir'] . "/$ADMIN_DIR/articles/editor_load_tinymce.php");
require_once($GLOBALS['g_campsiteDir'] . '/classes/Template.php');
include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");
//@New theme management
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\IThemeManagementService;
use Newscoop\Service\IOutputSettingIssueService;
use Newscoop\Service\IOutputSettingSectionService;
//@New theme management

if (!$g_user->hasPermission('ManageSection')) {
    camp_html_display_error(getGS("You do not have the right to modify sections."));
    exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$Section = Input::Get('Section', 'int', 0);

$publicationObj = new Publication($Pub);
$issueObj = new Issue($Pub, $Language, $Issue);
$sectionObj = new Section($Pub, $Issue, $Language, $Section);

$templates = Template::GetAllTemplates(null, true, true, true);

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 'Section' => $sectionObj);
camp_html_content_top(getGS("Configure section"), $topArray);

$url_args1 = "Pub=$Pub&Issue=$Issue&Language=$Language";
$url_args2 = $url_args1."&Section=$Section";

$languageObj = new Language($Language);
if (!is_object($languageObj)) {
    $languageObj = new Language(1);
}
//$editorLanguage = camp_session_get('TOL_Language', $languageObj->getCode());
$editorLanguage = !empty($_COOKIE['TOL_Language']) ? $_COOKIE['TOL_Language'] : $languageObj->getCode();
editor_load_tinymce('cDescription', $g_user, 0, $editorLanguage, 'section');

//@New theme management
$resourceId = new ResourceId('Section/Edit');
$themeManagementService = $resourceId->getService(IThemeManagementService::NAME_1);
$outputSettingIssueService = $resourceId->getService(IOutputSettingIssueService::NAME);
$outputSettingSectionService = $resourceId->getService(IOutputSettingSectionService::NAME);

$outSetIssues = $outputSettingIssueService->findByIssue($issueObj->getIssueId());

$themePath = null;
$tplSectionPath = null;
$tplArticlePath = null;
if(count($outSetIssues) > 0){
	$outSetIssue = $outSetIssues[0];
	$themePath = $outSetIssue->getThemePath()->getPath();
	$outSetSections = $outputSettingSectionService->findBySection($sectionObj->getSectionId());
	if(count($outSetSections) > 0){
		$outSetSection = $outSetSections[0];
		if($outSetSection->getSectionPage() != null){
			$tplSectionPath = $outSetSection->getSectionPage()->getPath();
		}
		if($outSetSection->getArticlePage() != null){
			$tplArticlePath = $outSetSection->getArticlePage()->getPath();
		}
	}
}

$issueHasTheme = $themePath != null;
if($issueHasTheme){
	$allTemplates = $themeManagementService->getTemplates($themePath);
} else {
	$allTemplates = array();
}

//@New theme management
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/campsite.js"></script>

<table border="0" cellspacing="0" cellpadding="1" class="action_buttons" style="padding-top: 5px;">
<tr>
  <td><a href="/<?php echo $ADMIN; ?>/sections/?Pub=<?php p($Pub); ?>&Issue=<?php p($issueObj->getIssueNumber()); ?>&Language=<?php p($issueObj->getLanguageId()); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" border="0" /></a></td>
  <td><a href="/<?php echo $ADMIN; ?>/sections/?Pub=<?php p($Pub); ?>&Issue=<?php p($issueObj->getIssueNumber()); ?>&Language=<?php p($issueObj->getLanguageId()); ?>"><b><?php putGS("Section List"); ?></b></a></td>
  <td style="padding-left: 20px;"><a href="/<?php echo $ADMIN; ?>/articles/?f_publication_id=<?php p($Pub); ?>&f_issue_number=<?php p($sectionObj->getIssueNumber()); ?>&f_section_number=<?php p($sectionObj->getSectionNumber()); ?>&f_language_id=<?php p($sectionObj->getLanguageId()); ?>"><b><?php putGS("Go To Articles"); ?></b></a></td>
  <td><a href="/<?php echo $ADMIN; ?>/articles/?f_publication_id=<?php p($Pub); ?>&f_issue_number=<?php p($sectionObj->getIssueNumber()); ?>&f_section_number=<?php p($sectionObj->getSectionNumber()); ?>&f_language_id=<?php p($sectionObj->getLanguageId()); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/go_to.png" border="0" /></a></td>
</tr>
</table>

<p>
<table border="0" cellspacing="0" cellpadding="1" class="action_buttons">
<tr>
  <td><a href="/<?php echo $ADMIN; ?>/sections/add.php?<?php p($url_args1); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" border="0" /></a></td>
  <td><a href="/<?php echo $ADMIN; ?>/sections/add.php?<?php p($url_args1); ?>"><b><?php putGS("Add new section"); ?></b></a></td>
  <td style="padding-left: 20px;"><a href="/<?php echo $ADMIN; ?>/sections/duplicate.php?<?php p($url_args2); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/duplicate.png" border="0" /></a></td>
  <td><a href="/<?php echo $ADMIN; ?>/sections/duplicate.php?<?php p($url_args2); ?>" ><b><?php putGS("Duplicate"); ?></b></a></td>
  <td style="padding-left: 20px;"><a href="/<?php echo $ADMIN; ?>/sections/del.php?<?php p($url_args2); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" border="0" /></a></td>
  <td><a href="/<?php echo $ADMIN; ?>/sections/del.php?<?php p($url_args2); ?>" ><b><?php putGS("Delete"); ?></b></a></td>
</tr>
</table>

<?php camp_html_display_msgs(); ?>

<p>
    <form name="section_edit" method="POST" action="/<?php echo $ADMIN; ?>/sections/do_edit.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<table border="0" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td colspan="2">
    <b><?php putGS("Configure section"); ?></b>
    <hr noshade size="1" color="black">
  </td>
</tr>
<tr>
  <td align="right"><?php putGS("Number"); ?>:</td>
  <td>
    <?php p($sectionObj->getSectionNumber()); ?>
  </td>
</tr>
<tr>
  <td align="right"><?php putGS("Name"); ?>:</td>
  <td>
    <input type="text" class="input_text" name="cName" size="32" value="<?php p(htmlspecialchars($sectionObj->getName())); ?>" />
  </td>
</tr>
<tr>
  <td align="right"><?php putGS("URL Name"); ?>:</td>
  <td>
    <input type="text" class="input_text" name="cShortName" size="32" value="<?php p(htmlspecialchars($sectionObj->getUrlName())); ?>" alt="alnum|1|A|true|false|_" emsg="<?php putGS('The $1 field may only contain letters, digits and underscore (_) character.', "'" . getGS('URL Name') . "'"); ?>"/>
  </td>
</tr>
<?php
	if(SaaS::singleton()->hasPermission('ManageSectionSubscriptions')) {
?>
<tr>
  <td align="right"><?php putGS("Subscriptions"); ?>:</td>
  <td>
    <select name="cSubs" class="input_select">
    <option value="n"> --- </option>
    <option value="a"><?php putGS("Add section to all subscriptions."); ?></option>
    <option value="d"><?php putGS("Delete section from all subscriptions."); ?></option>
    </select>
  </td>
</tr>
<?php
	}
?>
<tr>
  <td align="right" valign="top"><?php putGS("Description"); ?>:</td>
  <td>
    <textarea name="cDescription" id="cDescription" class="tinymce"
    rows="20" cols="80"><?php p($sectionObj->getDescription()); ?></textarea>
  </td>
</tr>
<?php
	if(SaaS::singleton()->hasPermission('ManageSectionTemplates')) {
?>
<tr>
  <td colspan="2" style="padding-top:20px;">
    <b><?php putGS("Default templates"); ?></b>
    <hr noshade size="1" color="black" />
  </td>
</tr>
<?php if($issueHasTheme){ ?>
<tr>
  <td align="right"><?php putGS("Section Template"); ?>:</td>
  <td>
    <select name="cSectionTplId" class="input_select">
    <option value="0">&lt;<?php  putGS("default"); ?>&gt;</option>
    <?php
	foreach ($allTemplates as $template) {
		camp_html_select_option($template->getPath(), $tplSectionPath, $template->getName());
	}
    ?>
    </select>
  </td>
</tr>
<tr>
  <td align="right"><?php putGS("Article Template"); ?>:</td>
  <td>
    <select name="cArticleTplId" class="input_select">
    <option value="0">&lt;<?php  putGS("default"); ?>&gt;</option>
    <?php
	foreach ($allTemplates as $template) {
		camp_html_select_option($template->getPath(), $tplArticlePath, $template->getName());
	}
    ?>
    </select>
  </td>
</tr>
<?php } else {?>
<TR>
	<TD ALIGN="LEFT" colspan="2" style="color: red;">
	<INPUT TYPE="hidden" NAME="cSectionTplId" VALUE="0"/>
	<INPUT TYPE="hidden" NAME="cArticleTplId" VALUE="0"/>
	<?php putGS("You have to assign a theme to the issue");?>
	<br/>
	<?php putGS("before you can configure the section templates.");?>
	</TD>
</TR>
<?php }?>
<?php
    }
?>
<tr>
  <td colspan="2" align="center">
    <input type="hidden" name="Pub" value="<?php p($Pub); ?>" />
    <input type="hidden" name="Issue" value="<?php p($Issue); ?>" />
    <input type="hidden" name="Language" value="<?php p($Language); ?>" />
    <input type="hidden" name="Section" value="<?php p($Section); ?>" />
    <input type="hidden" name="f_language_selected" ID="f_language_selected" value="<?php p($editorLanguage); ?>" />
    <input type="submit" class="button" name="Save" value="<?php putGS('Save'); ?>" />
  </td>
</tr>
</table>
</form>
<p>
<script>
document.section_edit.cName.focus();
</script>

<?php CampPlugin::PluginAdminHooks(__FILE__); ?>
<?php camp_html_copyright_notice(); ?>
