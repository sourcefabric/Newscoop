<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission("AddArticle")) {
	camp_html_display_error(getGS("You do not have the right to add articles."));
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
$f_destination_publication_id = Input::Get('f_destination_publication_id', 'int', 0, true);
$f_destination_issue_id = Input::Get('f_destination_issue_id', 'int', 0, true);
$f_destination_section_id = Input::Get('f_destination_section_id', 'int', 0, true);
$f_article_name = Input::Get('f_article_name', 'string', '', true);
//$BackLink = Input::Get('Back', 'string', "/$ADMIN/articles/index.php", true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;	
}

$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	camp_html_display_error(getGS('Publication does not exist.'));
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	camp_html_display_error(getGS('Issue does not exist.'));
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	camp_html_display_error(getGS('Section does not exist.'));
	exit;	
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('Article does not exist.'));
	exit;
}

$languageObj =& new Language($Language);
$sLanguageObj =& new Language($sLanguage);

$allPublications = Publication::GetPublications();
$allIssues = array();
if ($f_destination_publication_id > 0) {
	$allIssues = Issue::GetIssues($f_destination_publication_id, $Language);
}
$allSections = array();
if ($f_destination_issue_id > 0) {
	$allSections = Section::GetSections($f_destination_publication_id, $f_destination_issue_id, $Language);
}

if (empty($f_article_name)) {
	$f_article_name = $articleObj->getUniqueName($articleObj->getTitle());
}

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 
				  'Section' => $sectionObj, 'Article'=>$articleObj);
camp_html_content_top(getGS("Duplicate article"), $topArray);
?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<FORM NAME="duplicate" METHOD="POST">
<input type="hidden" name="Pub" value="<?php p($Pub); ?>">
<input type="hidden" name="Issue" value="<?php p($Issue); ?>">
<input type="hidden" name="Section" value="<?php p($Section); ?>">
<input type="hidden" name="Language" value="<?php p($Language); ?>">
<input type="hidden" name="Article" value="<?php p($Article); ?>">
<input type="hidden" name="sLanguage" value="<?php p($sLanguage); ?>">
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="f_article_name" SIZE="64" MAXLENGTH="64" VALUE="<?php  p(htmlspecialchars($f_article_name)); ?>" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Type"); ?>:</TD>
	<TD>
	<B><?php p(htmlspecialchars($articleObj->getType())); ?></B>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Created"); ?>:</TD>
	<TD>
	<B><?php p(htmlspecialchars($articleObj->getUploadDate())); ?> <?php  putGS('(yyyy-mm-dd)'); ?></B>
	</TD>
</TR>

<TR>
	<td colspan="2">&nbsp;</TD>
</TR>
<TR>
	<td colspan="2" style="padding-left: 20px; font-size: 12pt; font-weight: bold;"><?php  putGS("Select destination"); ?></TD>
</TR>
<TR>
	<TD VALIGN="middle" ALIGN="RIGHT" style="padding-left: 20px;"><?php  putGS('Publication'); ?>: </TD>
	<TD valign="middle" ALIGN="LEFT">
		<?php if (count($allPublications) > 0) { ?>
		<SELECT NAME="f_destination_publication_id" class="input_select" ONCHANGE="if ((this.selectedIndex != 0) && (this.options[this.selectedIndex].value != <?php p($f_destination_publication_id); ?>)) {this.form.submit();}">
		<OPTION VALUE="0"><?php  putGS('---Select publication---'); ?></option>
		<?php 
		foreach ($allPublications as $tmpPublication) {
			pcombovar($tmpPublication->getPublicationId(), $f_destination_publication_id, $tmpPublication->getName());
		}
		?>
		</SELECT>
		<?php
		}
		else {
			?>
			<SELECT class="input_select" DISABLED><OPTION><?php  putGS('No publications'); ?></option></SELECT>
			<?php
		}
		?>
	</td>
</tr>

<tr>
	<TD VALIGN="middle" ALIGN="RIGHT" style="padding-left: 20px;"><?php  putGS('Issue'); ?>: </TD>
	<TD valign="middle" ALIGN="LEFT">
		<?php if (($f_destination_publication_id > 0) && (count($allIssues) > 0)) { ?>
		<SELECT NAME="f_destination_issue_id" class="input_select" ONCHANGE="if ((this.selectedIndex != 0) && (this.options[this.selectedIndex].value != <?php p($f_destination_issue_id); ?>)) { this.form.submit(); }">
		<OPTION VALUE="0"><?php  putGS('---Select issue---'); ?></option>
		<?php 
		foreach ($allIssues as $tmpIssue) {
			pcombovar($tmpIssue->getIssueId(), $f_destination_issue_id, $tmpIssue->getName());
		}
		?>
		</SELECT>
		<?php  
		} 
		else { 
			?><SELECT class="input_select" DISABLED><OPTION><?php  putGS('No issues'); ?></SELECT>
			<?php  
		} 
		?>
	</td>
</tr>

<tr>	
	<TD VALIGN="middle" ALIGN="RIGHT" style="padding-left: 20px;"><?php  putGS('Section'); ?>: </TD>
	<TD valign="middle" ALIGN="LEFT">
		<?php if (($f_destination_issue_id > 0) && (count($allSections) > 0)) { ?>
		<SELECT NAME="f_destination_section_id" class="input_select" ONCHANGE="if ((this.selectedIndex != 0) && (this.options[this.selectedIndex].value != <?php p($f_destination_section_id); ?>)) { this.form.submit(); }">
		<OPTION VALUE="0"><?php  putGS('---Select section---'); ?>
		<?php 
		foreach ($allSections as $tmpSection) {
			pcomboVar($tmpSection->getSectionId(), $f_destination_section_id, $tmpSection->getName());
		}
		?>
		</SELECT>
		<?php  
		} 
		else { 
			?><SELECT class="input_select" DISABLED><OPTION><?php  putGS('No sections'); ?></SELECT>
			<?php  
		}
		?>
		</TD>
</tr>

<tr>
	<td colspan="2"><?php 
		if ( ($Pub == $f_destination_publication_id) && ($Issue == $f_destination_issue_id) && ($Section == $f_destination_section_id)) {
			putGS("The destination section is the same as the source section."); echo "<BR>\n";
		}
	?></td>
</tr>

<tr>
	<td align="center" colspan="2">
		<INPUT TYPE="button" Name="Duplicate" Value="<?php putGS("Duplicate article"); ?>" <?php if (($f_destination_publication_id <= 0) || ($f_destination_issue_id <=0) || ($f_destination_section_id <= 0)) { echo 'class="button_disabled"'; } else { echo "class=\"button\" onclick=\"location.href='/$ADMIN/articles/do_duplicate.php?Pub=$Pub&Issue=$Issue&Section=$Section&Language=$Language&sLanguage=$sLanguage&Article=$Article&f_destination_publication_id=$f_destination_publication_id&f_destination_issue_id=$f_destination_issue_id&f_destination_section_id=$f_destination_section_id&f_article_name=".urlencode($f_article_name)."';\""; }?> >
	</td>
</tr>
</FORM>
</table>
<p>

<?php camp_html_copyright_notice(); ?>