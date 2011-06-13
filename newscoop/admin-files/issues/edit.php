<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/IssuePublish.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Template.php');
//@New theme management
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\IThemeManagementService;
use Newscoop\Service\IOutputSettingIssueService;
//@New theme management
camp_load_translation_strings("articles");
camp_load_translation_strings("logs");

// Check permissions
if (!$g_user->hasPermission('ManageIssue')) {
	camp_html_display_error(getGS('You do not have the right to change issue details.'));
	exit;
}
$Pub = Input::Get('Pub', 'int');
$Issue = Input::Get('Issue', 'int');
$Language = Input::Get('Language', 'int');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));
	exit;
}
$publicationObj = new Publication($Pub);

if (!$publicationObj->exists()) {
	camp_html_display_error(getGS('Publication does not exist.'));
	exit;
}
$issueObj = new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	camp_html_display_error(getGS('Issue does not exist.'));
	exit;
}

$allLanguages = Language::GetLanguages(null, null, null, array(), array(), true);

// Get translations of this issue
$issueTranslations = Issue::GetIssues($Pub, null, $Issue, null, null, false, null, true);
$excludeLanguageIds = DbObjectArray::GetColumn($issueTranslations, 'IdLanguage');

$allEvents = IssuePublish::GetIssueEvents($Pub, $Issue, $Language);

$publish_date = date("Y-m-d");
$publish_hour = (date("H") + 1);
$publish_min = "00";

camp_html_content_top(getGS('Change issue details'), array('Pub' => $publicationObj, 'Issue' => $issueObj), true, true);

$url_args1 = "Pub=$Pub";
$url_args2 = $url_args1."&Issue=$Issue&Language=$Language";

$url_args3 = "f_publication_id=$Pub&f_issue_number=$Issue&f_language_id=$Language";

if (Issue::GetNumIssues($Pub) <= 0) {
	$url_add = "add_new.php";
} else {
	$url_add = "qadd.php";
}

//@New theme management
$resourceId = new ResourceId('Publication/Edit');
$themeManagementService = $resourceId->getService(IThemeManagementService::NAME_1);
$outputSettingIssueService = $resourceId->getService(IOutputSettingIssueService::NAME);

$outSetIssues = $outputSettingIssueService->findByIssue($issueObj->getIssueId());
$themePath = null;
$tplFrontPath = null;
$tplSectionPath = null;
$tplArticlePath = null;
if(count($outSetIssues) > 0){
	$outSetIssue = $outSetIssues[0];
	$themePath = $outSetIssue->getThemePath()->getPath();
	if($outSetIssue->getFrontPage() != null){
		$tplFrontPath = $outSetIssue->getFrontPage()->getPath();
	}
	if($outSetIssue->getSectionPage() != null){
		$tplSectionPath = $outSetIssue->getSectionPage()->getPath();
	}
	if($outSetIssue->getArticlePage() != null){
		$tplArticlePath = $outSetIssue->getArticlePage()->getPath();
	}
}

$publicationThemes = $themeManagementService->getThemes($publicationObj->getPublicationId());
$publicationHasThemes = count($publicationThemes) > 0;
if($themePath == null && $publicationHasThemes){
	$themePath = $publicationThemes[0]->getPath();
}

if($themePath != null){
	$allTemplates = $themeManagementService->getTemplates($themePath);
} else {
	$allTemplates = array();
}

//@New theme management
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($Pub); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($Pub); ?>"><B><?php  putGS("Issue List"); ?></B></A></TD>
	<TD style="padding-left: 20px;"><A HREF="/<?php echo $ADMIN; ?>/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($issueObj->getIssueNumber()); ?>&Language=<?php p($issueObj->getLanguageId()); ?>"><B><?php  putGS("Go To Sections"); ?></B></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($issueObj->getIssueNumber()); ?>&Language=<?php p($issueObj->getLanguageId()); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/go_to.png" BORDER="0"></A></TD>
</TR>
</TABLE>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons">
<TR>
	<TD><A HREF="<?php p($url_add); ?>?<?php p($url_args1); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
	<TD><A HREF="<?php p($url_add); ?>?<?php p($url_args1); ?>" ><B><?php  putGS("Add new issue"); ?></B></A></TD>

    <TD style="padding-left: 20px;"><A HREF="" ONCLICK="window.open('/<?php echo $ADMIN; ?>/issues/preview.php?<?php p($url_args2); ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=yes, width=800, height=600'); return false;"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/preview.png" BORDER="0"></A></TD>
    <TD><A HREF="" ONCLICK="window.open('/<?php echo $ADMIN; ?>/issues/preview.php?<?php p($url_args2); ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=yes, width=800, height=600'); return false;"><B><?php  putGS("Preview"); ?></B></A></TD>

    <TD style="padding-left: 20px;"><A HREF="/<?php echo $ADMIN; ?>/issues/translate.php?<?php p($url_args2); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/translate.png" BORDER="0"></A></TD>
    <TD><A HREF="/<?php echo $ADMIN; ?>/issues/translate.php?<?php p($url_args2); ?>" ><B><?php  putGS("Translate"); ?></B></A></TD>

    <TD style="padding-left: 20px;"><A HREF="/<?php echo $ADMIN; ?>/issues/delete.php?<?php p($url_args3); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0"></A></TD>
    <TD><A HREF="/<?php echo $ADMIN; ?>/issues/delete.php?<?php p($url_args3); ?>"><B><?php  putGS("Delete"); ?></B></A></TD>
</TR>
</TABLE>

<?php camp_html_display_msgs("1em", 0); ?>

<P>
<table>
<tr>
	<td valign="top">
        <FORM name="issue_edit" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/issues/do_edit.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
		<?php echo SecurityToken::FormParameter(); ?>
		<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="f_current_language_id" VALUE="<?php p($Language); ?>">
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
		<TR>
			<TD COLSPAN="2">
				<B><?php  putGS("Change issue details"); ?></B>
				<HR NOSHADE SIZE="1" COLOR="BLACK">
			</TD>
		</TR>

		<TR>
			<TD ALIGN="RIGHT" ><?php  putGS("Number"); ?>:</TD>
			<TD>
				<?php  p($issueObj->getIssueNumber()); ?>
			</TD>
		</TR>

		<TR>
			<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
			<TD>
			<INPUT TYPE="TEXT" class="input_text" NAME="f_issue_name" SIZE="32" value="<?php  p(htmlspecialchars($issueObj->getName())); ?>" alt="blank" emsg="<?php putGS('You must fill in the $1 field.',"'".getGS('Name')."'"); ?>">
			</TD>
		</TR>

		<TR>
			<TD ALIGN="RIGHT"><?php  putGS("URL Name"); ?>:</TD>
			<TD>
			<INPUT TYPE="TEXT" class="input_text" NAME="f_url_name" SIZE="32" value="<?php  p(htmlspecialchars($issueObj->getUrlName())); ?>" alt="alnum|1|A|true|false|_" emsg="<?php putGS('The $1 field may only contain letters, digits and underscore (_) character.', "'" . getGS('URL Name') . "'"); ?>">
			</TD>
		</TR>

		<TR>
			<TD ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</TD>
			<TD>
			    <SELECT NAME="f_new_language_id" class="input_select">
				<?php
				foreach ($allLanguages as $tmpLanguage) {
					$langId = $tmpLanguage->getLanguageId();
					if (($langId == $issueObj->getLanguageId()) || !in_array($langId, $excludeLanguageIds)) {
						camp_html_select_option($langId, $issueObj->getLanguageId(), $tmpLanguage->getNativeName());
					}
			    }
				?>
				</SELECT>
			</TD>
		</TR>

		<?php if($publicationHasThemes){?>
		<TR>
			<TD ALIGN="RIGHT"><?php  putGS("Publication date<BR><SMALL>(yyyy-mm-dd)</SMALL>"); ?>:</TD>
			<TD>
				<?php
				if ($issueObj->getWorkflowStatus() == 'Y') {
					$t2 = getGS('Published');
					$t3 = getGS('Not published');
				}
				else {
					$t2 = getGS('Not published');
					$t3 = getGS('Published');
				}
				?>

				<?php if ($issueObj->getWorkflowStatus() == 'Y') { ?>
				<INPUT TYPE="TEXT" class="input_text" NAME="f_publication_date" SIZE="20" MAXLENGTH="19" value="<?php  p(htmlspecialchars($issueObj->getPublicationDate())); ?>">
				<?php } ?>
				<A HREF="/<?php echo $ADMIN; ?>/issues/do_status.php?Pub=<?php p($Pub); ?>&Issue=<?php  p($issueObj->getIssueNumber()); ?>&Language=<?php p($issueObj->getLanguageId()); ?>&f_target=edit.php&<?php echo SecurityToken::URLParameter(); ?>" onclick="return confirm('<?php  putGS('Are you sure you want to change the issue $1 status from $2 to $3?',$issueObj->getIssueNumber().'. '.htmlspecialchars($issueObj->getName()).' ('.htmlspecialchars($issueObj->getLanguageName()).')',"\'$t2\'","\'$t3\'"); ?>
		');">
				<?php if ($issueObj->getWorkflowStatus() == 'Y') {
						print putGS("Unpublish");
					} else {
						print putGS("Click here to publish this issue");
					}
				?>
				</A>
			</TD>
		</TR>
		<?php
            }
			if(SaaS::singleton()->hasPermission('ManageIssueTemplates')) {
		?>
		<TR>
			<TD COLSPAN="2" style="padding-top: 20px;">
				<B><?php  putGS("Default templates"); ?></B>
				<HR NOSHADE SIZE="1" COLOR="BLACK">
			</TD>
		</TR>
		<?php if($publicationHasThemes){?>
		<TR>
			<TD ALIGN="RIGHT"><?php  putGS("Issue Theme"); ?>:</TD>
			<TD>
				<SELECT ID="f_theme_id" NAME="f_theme_id" class="input_select">
				<?php
				foreach ($publicationThemes as $theme) {
					camp_html_select_option($theme->getPath(), $themePath, $theme->getName());
				}
				?>
				</SELECT>
			</TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT"><?php  putGS("Front Page Template"); ?>:</TD>
			<TD>
				<SELECT ID="f_issue_template_id" NAME="f_issue_template_id" class="input_select">
				<OPTION VALUE="0">&lt;<?php  putGS("default"); ?>&gt;</OPTION>
				<?php
				foreach ($allTemplates as $template) {
					camp_html_select_option($template->getPath(), $tplFrontPath, $template->getName());
				}
				?>
				</SELECT>
			</TD>
		</TR>

		<TR>
			<TD ALIGN="RIGHT"><?php  putGS("Section Template"); ?>:</TD>
			<TD>
				<SELECT ID="f_section_template_id" NAME="f_section_template_id" class="input_select">
				<OPTION VALUE="0">&lt;<?php  putGS("default"); ?>&gt;</OPTION>
				<?php
				foreach ($allTemplates as $template) {
					camp_html_select_option($template->getPath(), $tplSectionPath, $template->getName());
				}
				?>
				</SELECT>
			</TD>
		</TR>

		<TR>
			<TD ALIGN="RIGHT"><?php  putGS("Article Template"); ?>:</TD>
			<TD>
				<SELECT ID="f_article_template_id" NAME="f_article_template_id" class="input_select">
				<OPTION VALUE="0">&lt;<?php  putGS("default"); ?>&gt;</OPTION>
				<?php
				foreach ($allTemplates as $template) {
					camp_html_select_option($template->getPath(), $tplArticlePath, $template->getName());
				}
				?>
				</SELECT>
			</TD>
		</TR>
		<?php
			}
		?>
		<?php } else {?>
		<TR>
			<INPUT TYPE="hidden" NAME="f_theme_id" VALUE="0"/>
			<INPUT TYPE="hidden" NAME="f_issue_template_id" VALUE="0"/>
			<INPUT TYPE="hidden" NAME="f_section_template_id" VALUE="0"/>
			<INPUT TYPE="hidden" NAME="f_article_template_id" VALUE="0"/>
			<TD ALIGN="LEFT" colspan="2" style="color: red;">
			<?php putGS("Please assign at least one theme to the publication");?>
			<br/>
			<?php putGS("in order to be able to assigned to the issue.");?>
			<br/>
			<?php putGS("Only than the issue can be published");?>
			</TD>
		</TR>
		<?php }?>
		<TR>
			<TD COLSPAN="2" align="center" style="padding-top: 15px;">
				<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
			</TD>
		</TR>
		</TABLE>
		</FORM>
		<P>
		<?php CampPlugin::PluginAdminHooks(__FILE__); ?>
	</td>

	<td valign="top">
		<div class="action_buttons" style="font-size: 10pt; font-weight: bold;"><?php putGS('Issue Publishing Schedule'); ?></div>
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
		<TR class="table_list_header">
			<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Date/Time"); ?></B></TD>
			<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Action"); ?></B></TD>
			<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Publish articles"); ?></B></TD>
			<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Delete"); ?></B></TD>
		</TR>
		<?php
		//
		// Scheduled Publishing
		//
		if (count($allEvents) == 0) { ?>
			<tr><td colspan="4" class="list_row_odd"><?php putGS("No events."); ?></td></tr>
			<?php
		} else {
			$color= 0;
			foreach ($allEvents as $event) {
				$url_publish_time = urlencode($event->getActionTime());
				?>
				<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>

				<TD>
					<?php if (!$event->isCompleted()) { ?><A HREF="/<?php echo $ADMIN; ?>/issues/autopublish.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>&event_id=<?php echo $event->getEventId(); ?>"><?php } else { echo "<strike>"; } ?><?php p(htmlspecialchars($event->getActionTime())); ?><?php if (!$event->isCompleted()) { ?></A><?php } else { echo "</strike>"; } ?>
				</TD>

				<TD >
					<?php
						$action = $event->getPublishAction();
						if ($action == "P") {
							putGS("Publish");
						}
						else {
							putGS("Unpublish");
						}
					?>&nbsp;
				</TD>

				<TD >
					<?php
						$publish_articles = $event->getPublishArticlesAction();
						if ($publish_articles == "Y") {
							putGS("Yes");
						}
						else {
							putGS("No");
						}
					?>&nbsp;
				</TD>

				<TD ALIGN="CENTER">
					<A HREF="/<?php echo $ADMIN; ?>/issues/autopublish_del.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>&event_id=<?php echo $event->getEventId(); ?>&<?php echo SecurityToken::URLParameter(); ?>" onclick="return confirm('<?php putGS("Are you sure you want to delete this scheduled action?"); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php putGS('Delete entry'); ?>"></A>
				</TD>

			<?php } // foreach ?>
		<?php
		} // if
		?>
		</TR>
		</table>

        <FORM NAME="dialog" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/issues/autopublish_do_add.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
		<?php echo SecurityToken::FormParameter(); ?>
        <INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php echo $Pub; ?>">
        <INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php echo $Issue; ?>">
        <INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php echo $Language; ?>">
        <p>
        <?php if($publicationHasThemes){ ?>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="box_table">
		<TR>
			<TD COLSPAN="2">
				<B><?php  putGS("Schedule a new action"); ?></B>
				<HR NOSHADE SIZE="1" COLOR="BLACK">
			</TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT" ><?php  putGS("Date"); ?>:</TD>
			<TD>
				<?php $now = getdate(); ?>
                <input type="text" class="input_text date minDate_0" name="publish_date" id="publish_date" maxlength="10" size="11" value="<?php p($publish_date); ?>" alt="date|yyyy/mm/dd|-|4|<?php echo $now["year"]."/".$now["mon"]."/".$now["mday"]; ?>" emsg="<?php putGS('You must fill in the $1 field.',"'".getGS('Date')."'"); ?> <?php putGS("The date must be in the future."); ?>" />
			</TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT" ><?php  putGS("Time"); ?>:</TD>
			<TD>
			<INPUT TYPE="TEXT" class="input_text" NAME="publish_hour" SIZE="2" MAXLENGTH="2" VALUE="<?php p($publish_hour); ?>" alt="number|0|0|23" emsg="<?php putGS('You must fill in the $1 field.',"'".getGS('Time')."'" ); ?>"> :
			<INPUT TYPE="TEXT" class="input_text" NAME="publish_min" SIZE="2" MAXLENGTH="2" VALUE="<?php p($publish_min); ?>" alt="number|0|0|59" emsg="<?php putGS('You must fill in the $1 field.',"'".getGS('Time')."'" ); ?>">
			</TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT" ><?php  putGS("Action"); ?>:</TD>
			<TD>
			<SELECT NAME="action" class="input_select" alt="select" emsg="<?php putGS('You must select an action.'); ?>">
				<OPTION VALUE=" ">---</OPTION>
				<OPTION VALUE="P"><?php putGS("Publish"); ?></OPTION>
				<OPTION VALUE="U"><?php putGS("Unpublish"); ?></OPTION>
			</SELECT>
			</TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT" ><?php  putGS("Publish articles"); ?>:</TD>
			<TD>
			<SELECT NAME="publish_articles" class="input_select">
				<OPTION VALUE="Y"><?php putGS("Yes"); ?></OPTION>
				<OPTION VALUE="N"><?php putGS("No"); ?></OPTION>
			</SELECT>
			</TD>
		</TR>
		<TR>
			<TD COLSPAN="2" align="center">
				<INPUT TYPE="submit" class="button" VALUE="<?php  putGS('Save'); ?>">
			</TD>
		</TR>
		</TABLE>
		<?php } ?>
		</FORM>
	</td>
</tr>
</table>
<script>
document.forms.issue_edit.f_issue_name.focus();
</script>

<script type="text/javascript">

$(function() {
    // list templates per theme
    $('select[name=f_theme_id]').change(function() {
        var themePath = $(this).val();
        $.getJSON('get_templates.php', {
            'themePath': themePath
        }, function(data) {
			var selects = [$('select[name=f_issue_template_id]'),
			   			$('select[name=f_section_template_id]'),
			   			$('select[name=f_article_template_id]')];
			for(i = 0; i < selects.length; i++){
				select = selects[i];
				select.empty().append('<option selected value="0">&lt;<?php  putGS("default"); ?>&gt;</option>');
				$.each(data, function(key, value) { 
					select.append('<option value="' + key + '">' + value + '</option>');
				});
	        }
        });
    });

});

</script>

<?php camp_html_copyright_notice(); ?>
