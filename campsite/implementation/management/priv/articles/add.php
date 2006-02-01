<?php 
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/ArticleType.php");

global $Campsite;

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('AddArticle')) {
	camp_html_display_error(getGS("You do not have the right to add articles."));
	exit;
}

// Only for use when in the article screens.
$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);

// The article location dropdowns cause this page to reload,
// so we need to preserve the state with each refresh.
$f_article_name = Input::Get('f_article_name', 'string', '', true);
$f_article_type = Input::Get('f_article_type', 'string', '', true);
$f_article_language = Input::Get('f_article_language', 'int', $f_language_id, true);

// For choosing the article location.
$f_destination_publication_id = Input::Get('f_destination_publication_id', 'int', 0, true);
$f_destination_issue_number = Input::Get('f_destination_issue_number', 'int', 0, true);
$f_destination_section_number = Input::Get('f_destination_section_number', 'int', 0, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;	
}

// Only for the article screens.
$publicationObj = null;
$issueObj = null;
$sectionObj = null;
if ($f_publication_id > 0) {
	$publicationObj =& new Publication($f_publication_id);
	if (($f_issue_number > 0) && ($f_article_language > 0)) {
		$issueObj =& new Issue($f_publication_id, $f_article_language, $f_issue_number);
		if ($f_section_number > 0) {
			$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_article_language, $f_section_number);
		}
	}
}

$allArticleTypes = ArticleType::GetArticleTypes();
$allLanguages = Language::GetLanguages();

// added by sebastian
if (function_exists ("incModFile")) {
	incModFile ();
}

if ($f_publication_id > 0) {
	$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 
					  'Section' => $sectionObj);
	camp_html_content_top(getGS('Add new article'), $topArray, true, false, array(getGS("Articles") => "/$ADMIN/articles/?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id"));
} else {
	$crumbs = array();
	$crumbs[] = array(getGS("Actions"), "");
	$crumbs[] = array(getGS("Add new article"), "");
	echo camp_html_breadcrumbs($crumbs);
}

?>

<?php
if (sizeof($allArticleTypes) == 0) {
?>
<p>
<table border="0" cellspacing="0" cellpadding="6" align="center" class="table_input">
<tr>
	<td align="center">
	<font color="red">
	<?php putGS("No article types were defined. You must create an article type first."); ?>
	</font>
	<p><b><a href="/<?php echo $ADMIN; ?>/article_types/"><?php putGS("Edit article types"); ?></a></b></p>
	</td>
</tr>
</table>
</p>
<?php
} else {
?>

<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.config.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.core.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.validators.js"></script>	

<P>
<FORM NAME="add_article" METHOD="GET" ACTION="add.php" onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
<?php if ($f_publication_id > 0) { ?>
<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php p($f_publication_id); ?>">
<?php } ?>
<?php if ($f_issue_number > 0) { ?>
<INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php p($f_issue_number); ?>">
<?php } ?>
<?php if ($f_section_number > 0) { ?>
<INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php p($f_section_number); ?>">
<?php } ?>
<?php if ($f_language_id > 0) { ?>
<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php  p($f_language_id); ?>">
<?php } ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Add new article"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<td valign="top">
		<table>
		<tr>
			<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
			<TD>
			<INPUT TYPE="TEXT" NAME="f_article_name" SIZE="40" MAXLENGTH="255" class="input_text" alt="blank" emsg="<?php putGS('You must complete the $1 field.', getGS('Name')); ?>" value="<?php echo htmlspecialchars($f_article_name); ?>">
			</TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT" ><?php  putGS("Type"); ?>:</TD>
			<TD>
				<SELECT NAME="f_article_type" class="input_select" alt="select" emsg="<?php putGS('You must complete the $1 field.', getGS('Article Type')); ?>">
				<option></option>
				<?php 
				foreach ($allArticleTypes as $tmpType) {
					camp_html_select_option($tmpType, $f_article_type, $tmpType);
				}
				?>
				</SELECT>
			</TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</TD>
			<TD>
				<SELECT NAME="f_article_language" class="input_select" <?php if ($f_publication_id <= 0) { ?>onchange="this.form.submit();"<?php } ?>>
				<option value="0"><?php putGS("---Select language---"); ?></option>
				<?php 
			 	foreach ($allLanguages as $tmpLanguage) {
			 		camp_html_select_option($tmpLanguage->getLanguageId(), 
			 								$f_article_language, 
			 								$tmpLanguage->getNativeName());
		        }
				?>			
				</SELECT>
			</TD>
		</TR>
		</table>
	</td>
	
	<?php if (($f_publication_id <= 0) && $User->hasPermission("MoveArticle")) { ?>
	<td style="border-left: 1px solid black;">
		<TABLE>
		<TR>
			<td colspan="2" style="padding-left: 20px; padding-bottom: 5px;font-size: 10pt; font-weight: bold;"><?php  putGS("Select location (optional):"); ?></TD>
		</TR>
		<TR>
			<TD VALIGN="middle" ALIGN="RIGHT" style="padding-left: 20px;"><?php  putGS('Publication'); ?>: </TD>
			<TD valign="middle" ALIGN="LEFT">
				<?php if ( ($f_article_language > 0) && count($Campsite["publications"]) > 0) { ?>
				<SELECT NAME="f_destination_publication_id" class="input_select" ONCHANGE="if (this.options[this.selectedIndex].value != <?php p($f_destination_publication_id); ?>) {this.form.submit();}">
				<OPTION VALUE="0"><?php  putGS('---Select publication---'); ?></option>
				<?php 
				foreach ($Campsite["publications"] as $tmpPublication) {
					camp_html_select_option($tmpPublication->getPublicationId(), $f_destination_publication_id, $tmpPublication->getName());
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
				<?php 
				$tmpIssues = array();
				if ($f_destination_publication_id > 0) {
					// Only get Issues with given language.
					foreach ($Campsite["issues"][$f_destination_publication_id] as $tmpIssue) {
						if ($tmpIssue->getLanguageId() == $f_article_language) {
							$tmpIssues[] = $tmpIssue;
						}
					}					
				}
				if (($f_destination_publication_id > 0) && (count($tmpIssues) > 0)) {
					?>
					<SELECT NAME="f_destination_issue_number" class="input_select" ONCHANGE="if (this.options[this.selectedIndex].value != <?php p($f_destination_issue_number); ?>) { this.form.submit(); }">
					<OPTION VALUE="0"><?php  putGS('---Select issue---'); ?></option>
					<?php 
					foreach ($tmpIssues as $tmpIssue) {
						camp_html_select_option($tmpIssue->getIssueNumber(), $f_destination_issue_number, $tmpIssue->getName());
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
				<?php if (($f_destination_publication_id > 0) 
						  && (count($tmpIssues) > 0) 
						  && ($f_destination_issue_number > 0) 
						  && (count($Campsite["sections"]) > 0)) { ?>
				<SELECT NAME="f_destination_section_number" class="input_select" onchange="if (this.selectedIndex != 0) { document.forms.add_article.save.className='button'; document.forms.add_article.save.disabled = false; } else { document.forms.add_article.save.className='button_disabled'; document.forms.add_article.save.disabled = true;}">
				<OPTION VALUE="0"><?php  putGS('---Select section---'); ?>
				<?php 
				$issueLanguageId = $Campsite["issues"][$f_destination_publication_id][0]->getLanguageId();
				$sections = $Campsite["sections"][$f_destination_publication_id][$f_destination_issue_number][$issueLanguageId];
				foreach ($sections as $tmpSection) {
					camp_html_select_option($tmpSection->getSectionNumber(), $f_destination_section_number, $tmpSection->getName());
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
		</TABLE>
	</td>
	<?php } ?>
</tr>
<TR>
	<TD COLSPAN="2" align="center">
		<HR NOSHADE SIZE="1" COLOR="BLACK">
		<INPUT TYPE="submit" NAME="save" VALUE="<?php  putGS('Save'); ?>" <?php if (($f_destination_publication_id > 0) && (($f_destination_issue_number <= 0) || (count($tmpIssues) <= 0) || ($f_destination_section_number <= 0) || (count($Campsite["sections"]) <= 0))) { echo 'class="button_disabled" disabled'; } else { echo "class=\"button\""; }?> onclick="document.forms.add_article.action='do_add.php';">
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php } ?>
<?php camp_html_copyright_notice(); ?>