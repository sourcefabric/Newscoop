<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/sections/section_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/editor_load_tinymce.php");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Template.php');

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

$templates = Template::GetAllTemplates(array('ORDER BY' => array('Level' => 'ASC', 'Name' => 'ASC')));

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 'Section' => $sectionObj);
camp_html_content_top(getGS("Configure section"), $topArray);

$url_args1 = "Pub=$Pub&Issue=$Issue&Language=$Language";
$url_args2 = $url_args1."&Section=$Section";

$languageObj = new Language($Language);
if (!is_object($languageObj)) {
  $languageObj = new Language(1);
}
$editorLanguage = camp_session_get('TOL_Language', $languageObj->getCode());
editor_load_tinymce('cDescription', $g_user, 0, $editorLanguage);
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite.js"></script>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/sections/?Pub=<?php p($Pub); ?>&Issue=<?php p($issueObj->getIssueNumber()); ?>&Language=<?php p($issueObj->getLanguageId()); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/sections/?Pub=<?php p($Pub); ?>&Issue=<?php p($issueObj->getIssueNumber()); ?>&Language=<?php p($issueObj->getLanguageId()); ?>"><B><?php  putGS("Section List"); ?></B></A></TD>
	<TD style="padding-left: 20px;"><A HREF="/<?php echo $ADMIN; ?>/articles/?f_publication_id=<?php p($Pub); ?>&f_issue_number=<?php  p($sectionObj->getIssueNumber()); ?>&f_section_number=<?php p($sectionObj->getSectionNumber()); ?>&f_language_id=<?php  p($sectionObj->getLanguageId()); ?>"><B><?php  putGS("Go To Articles"); ?></B></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/articles/?f_publication_id=<?php p($Pub); ?>&f_issue_number=<?php  p($sectionObj->getIssueNumber()); ?>&f_section_number=<?php p($sectionObj->getSectionNumber()); ?>&f_language_id=<?php  p($sectionObj->getLanguageId()); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/go_to.png" BORDER="0"></A></TD>
</TR>
</TABLE>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons">
<TR>
	<TD><A HREF="add.php?<?php p($url_args1); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
	<TD><A HREF="add.php?<?php p($url_args1); ?>" ><B><?php  putGS("Add new section"); ?></B></A></TD>

	<TD style="padding-left: 20px;"><A HREF="duplicate.php?<?php p($url_args2); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/duplicate.png" BORDER="0"></A></TD>
	<TD><A HREF="duplicate.php?<?php p($url_args2); ?>" ><B><?php  putGS("Duplicate"); ?></B></A></TD>

	<TD style="padding-left: 20px;"><A HREF="del.php?<?php p($url_args2); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0"></A></TD>
	<TD><A HREF="del.php?<?php p($url_args2); ?>" ><B><?php  putGS("Delete"); ?></B></A></TD>
</TR>
</TABLE>

<?php camp_html_display_msgs(); ?>

<P>
<FORM NAME="section_edit" METHOD="POST" ACTION="do_edit.php" >
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Configure section"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Number"); ?>:</TD>
	<TD>
		<?php  p($sectionObj->getSectionNumber()); ?>
 	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" value="<?php  p(htmlspecialchars($sectionObj->getName())); ?>">
 	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("URL Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cShortName" SIZE="32" value="<?php  p(htmlspecialchars($sectionObj->getUrlName())); ?>">
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Subscriptions"); ?>:</TD>
	<TD>
		<SELECT NAME="cSubs" class="input_select">
	   	<OPTION VALUE="n"> --- </OPTION>
	   	<OPTION VALUE="a"><?php  putGS("Add section to all subscriptions."); ?></OPTION>
	   	<OPTION VALUE="d"><?php  putGS("Delete section from all subscriptions."); ?></OPTION>
	  	</SELECT>
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" VALIGN="top" ><?php  putGS("Description"); ?>:</TD>
	<TD><TEXTAREA name="cDescription"
			id="cDescription"
			rows="20" cols="80"><?php p($sectionObj->getDescription()); ?></TEXTAREA>
	</TD>
</TR>

<TR>
	<TD COLSPAN="2" style="padding-top:20px;">
		<B><?php  putGS("Default templates"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Section Template"); ?>:</TD>
	<TD>
		<SELECT NAME="cSectionTplId" class="input_select">
		<OPTION VALUE="0">---</OPTION>
		<?php
		foreach ($templates as $template) {
			camp_html_select_option($template->getTemplateId(), $sectionObj->getSectionTemplateId(), $template->getName());
		}
		?>
		</SELECT>
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Article Template"); ?>:</TD>
	<TD>
		<SELECT NAME="cArticleTplId" class="input_select">
		<OPTION VALUE="0">---</OPTION>
		<?php
		foreach ($templates as $template) {
			camp_html_select_option($template->getTemplateId(), $sectionObj->getArticleTemplateId(), $template->getName());
		}
		?>
		</SELECT>
	</TD>
</TR>

<TR>
	<TD COLSPAN="2" align="center">
	  	<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
	  	<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
	  	<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
	  	<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
	  	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<SCRIPT>
document.section_edit.cName.focus();
</SCRIPT>

<?php CampPlugin::PluginAdminHooks(__FILE__); ?>
<?php camp_html_copyright_notice(); ?>
