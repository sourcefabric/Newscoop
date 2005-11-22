<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/sections/section_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Template.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('ManageSection')) {
	camp_html_display_error(getGS("You do not have the right to change section details"));	
	exit;
}
$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$Section = Input::Get('Section', 'int', 0);


$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
$templates = Template::GetAllTemplates(array('ORDER BY' => array('Level' => 'ASC', 'Name' => 'ASC')));

## added by sebastian
if (function_exists ("incModFile")) {
  incModFile ();
}

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 'Section' => $sectionObj);
camp_html_content_top(getGS("Configure section"), $topArray);

$url_args1 = "Pub=$Pub&Issue=$Issue&Language=$Language";
$url_args2 = $url_args1."&Section=$Section";

?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/articles/?f_publication_id=<?php p($Pub); ?>&f_issue_number=<?php  p($sectionObj->getIssueNumber()); ?>&f_section_number=<?php p($sectionObj->getSectionNumber()); ?>&f_language_id=<?php  p($sectionObj->getLanguageId()); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/go_to.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/articles/?f_publication_id=<?php p($Pub); ?>&f_issue_number=<?php  p($sectionObj->getIssueNumber()); ?>&f_section_number=<?php p($sectionObj->getSectionNumber()); ?>&f_language_id=<?php  p($sectionObj->getLanguageId()); ?>"><B><?php  putGS("Go To Articles"); ?></B></A></TD>
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

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<FORM NAME="dialog" METHOD="POST" ACTION="do_edit.php" >
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Configure section"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="64" value="<?php  p(htmlspecialchars($sectionObj->getName())); ?>">
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
	<TD ALIGN="RIGHT" ><?php  putGS("URL Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cShortName" SIZE="32" MAXLENGTH="32" value="<?php  p(htmlspecialchars($sectionObj->getUrlName())); ?>">
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

 <?php
 ?>

<TR>
	<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
	  	<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
	  	<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
	  	<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
	  	<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
	  	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	  	<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>'">-->
	 	</DIV>
	</TD>
</TR>
</FORM>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>