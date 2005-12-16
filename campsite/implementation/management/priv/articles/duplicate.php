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

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_code = Input::Get('f_article_code', 'array', 0);
$f_destination_publication_id = Input::Get('f_destination_publication_id', 'int', 0, true);
$f_destination_issue_id = Input::Get('f_destination_issue_id', 'int', 0, true);
$f_destination_section_id = Input::Get('f_destination_section_id', 'int', 0, true);
$f_mode = Input::Get('f_mode', 'string', 'single', true);

// Article names can change from page request to page request.
// We create a 2-dimensional array of article names indexed by article ID, language ID.
//
// The user can choose whether to duplicate articles from page request to page request.
// We create a 2-dimensional array of boolean values indexed by article ID, language ID.
$articleNames = array();
$doCopy = array();
foreach ($_REQUEST as $key => $value) {
	if (!strncmp($key, "f_article_name_", strlen("f_article_name_"))) {
		$tmpCodeStr = str_replace("f_article_name_", "", $key);
		list($articleId, $languageId) = split("_", $tmpCodeStr);
		$articleNames[$articleId][$languageId] = Input::Get($key, 'string', '', true);
	}
	if (!strncmp($key, "f_do_copy_", strlen("f_do_copy_"))) {
		$tmpCodeStr = str_replace("f_do_copy_", "", $key);
		list($articleId, $languageId) = split("_", $tmpCodeStr);
		$doCopy[$articleId][$languageId] = Input::Get($key, 'string', '', true);
	}
}


// Get all the articles we are copying.
$articles = array();
foreach ($f_article_code as $code) {
	list($articleNumber, $languageId) = split("_", $code);
	$tmpArticle =& new Article($languageId, $articleNumber);
	$articles[$articleNumber][$languageId] = $tmpArticle;
	
	// Initialize the article names on initial page request.
	// Initialize the $doCopy array on initial page request.
	if (!isset($articleNames[$articleNumber][$languageId])) {
		$articleNames[$articleNumber][$languageId] = $tmpArticle->getTitle();
		$doCopy[$articleNumber][$languageId] = $languageId;
	}
}


// Fill in article names for translations.
// The user is automatically given the choice to duplicate translations of articles
// when they get to this screen.
foreach ($articles as $articleNumber => $languageArray) {	
	$tmpArticle = camp_array_peek($languageArray);
	$translations = $tmpArticle->getTranslations();
	foreach ($translations as $article) {
		if (!isset($articleNames[$article->getArticleNumber()][$article->getLanguageId()])) {
			$articleNames[$article->getArticleNumber()][$article->getLanguageId()] = $article->getTitle();
		}
	}
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;	
}

$publicationObj =& new Publication($f_publication_id);
if (!$publicationObj->exists()) {
	camp_html_display_error(getGS('Publication does not exist.'));
	exit;	
}

$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
if (!$issueObj->exists()) {
	camp_html_display_error(getGS('Issue does not exist.'));
	exit;	
}

$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
if (!$sectionObj->exists()) {
	camp_html_display_error(getGS('Section does not exist.'));
	exit;	
}

//
// This section is executed when the user finally hits the "duplicate" button.
//
if (isset($_REQUEST["duplicate_button"])) {
	foreach ($doCopy as $articleNumber => $languageArray) {
		$languageArray = array_keys($languageArray);
		//echo "<pre>"; print_r($languageArray); echo "</pre>";
		
		$tmpLanguageId = camp_array_peek($languageArray);
		
		// Error checking
		if (!isset($articles[$articleNumber][$tmpLanguageId])) {
			//echo "error $articleNumber:$tmpLanguageId<br>";
			continue;
		}
		
		//echo "copying $articleNumber:$tmpLanguageId<br>";
		// Grab the first article - it doesnt matter which one.
		$tmpArticle = $articles[$articleNumber][$tmpLanguageId];
		
		// Copy all the translations requested.
		$newArticles = $tmpArticle->copy($f_destination_publication_id, 
						  				 $f_destination_issue_id, 
						  				 $f_destination_section_id, 
						  				 $User->getUserId(),
						  				 $languageArray);
		
		// Set the names of the new copies
		foreach ($newArticles as $newArticle) {
			$newArticle->setTitle($articleNames[$articleNumber][$newArticle->getLanguageId()]);
		}
	}
	if ($f_mode == "single") {
		$tmpArticle = camp_array_peek($newArticles);
		$url = camp_html_article_url($tmpArticle, $f_language_id, "edit.php");
		header("Location: $url");
	} else {
		$tmpArticle = camp_array_peek(camp_array_peek($articles));
		$url = camp_html_article_url($tmpArticle, $f_language_id, "index.php");
		header("Location: $url");
	}
	exit;
}

$allPublications = Publication::GetPublications();
$allIssues = array();
if ($f_destination_publication_id > 0) {
	$allIssues = Issue::GetIssues($f_destination_publication_id, $f_language_id);
}
$allSections = array();
if ($f_destination_issue_id > 0) {
	$allSections = Section::GetSections($f_destination_publication_id, $f_destination_issue_id, $f_language_id);
}

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 
				  'Section' => $sectionObj);
if (count($articles) > 1) {
	$crumbs = array(getGS("Articles") => "/$ADMIN/articles/index.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id&f_language_selected=$f_language_selected");
	camp_html_content_top(getGS("Duplicate articles"), $topArray, true, false, $crumbs);
}
else {
	$topArray['Article'] = camp_array_peek(camp_array_peek($articles));
	camp_html_content_top(getGS("Duplicate article"), $topArray);
}
?>

<P>
<div class="page_title">
<?php putGS("Duplicate articles"); ?>:
</div>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" style="margin-left: 5px;">
<FORM NAME="duplicate" METHOD="POST">
<input type="hidden" name="f_publication_id" value="<?php p($f_publication_id); ?>">
<input type="hidden" name="f_issue_number" value="<?php p($f_issue_number); ?>">
<input type="hidden" name="f_section_number" value="<?php p($f_section_number); ?>">
<input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>">
<input type="hidden" name="f_mode" value="<?php p($f_mode); ?>">
<?php 
foreach ($articles as $languageArray) {
	foreach ($languageArray as $article) {	?>
<input type="hidden" name="f_article_code[]" value="<?php p($article->getArticleNumber()."_".$article->getLanguageId()); ?>">
	<?php 
	}
}
?>
<input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>">
<TR>
	<TD>
		<TABLE cellpadding="3">
		<TR class="table_list_header">
			<TD valign="top"><?php putGS("Duplicate?"); ?></TD>
			<TD valign="top"><?php putGS("Name"); ?></TD>
			<TD valign="top"><?php putGS("Language"); ?></TD>
			<TD valign="top"><?php putGS("Type"); ?></TD>
		</TR>
		
		<?php 
		$color = 0;
		foreach ($articles as $languageArray) {
			$count = 0;
			$tmpArticle = camp_array_peek($languageArray);
			$translations = $tmpArticle->getTranslations();
			foreach ($translations as $article) { 
			?>
		<TR class="<?php if ($color) { ?>list_row_even<?php } else { ?>list_row_odd<?php } $color = !$color; ?>">
			<TD>
				<input type="checkbox" name="f_do_copy_<?php p($article->getArticleNumber()."_".$article->getLanguageId()); ?>" value="" <?php if (isset($doCopy[$article->getArticleNumber()][$article->getLanguageId()])) { ?>CHECKED<?php } ?>>
			</TD>
			<TD <?php if ($count++ > 0) { ?>class="translation_indent"<?php } ?>>
				<INPUT TYPE="TEXT" NAME="f_article_name_<?php p($article->getArticleNumber()."_".$article->getLanguageId()); ?>" SIZE="50" MAXLENGTH="256" VALUE="<?php  p(htmlspecialchars($articleNames[$article->getArticleNumber()][$article->getLanguageId()])); ?>" class="input_text">
			</TD>
			
			<TD>
				<B><?php p(htmlspecialchars($article->getLanguageName())); ?></B>
			</TD>
			
			<TD>
				<B><?php p(htmlspecialchars($article->getType())); ?></B>
			</TD>
			
		</TR>
		<?php 
			}
		} 
		?>
		</TABLE>
	</TD>
</TR>
</TABLE>
<p>
<div class="page_title">
<?php putGS("to section"); ?>:
</div>
<p>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
	<TD>
		<TABLE>
		<TR>
			<td colspan="2" style="padding-left: 20px; padding-bottom: 5px;font-size: 12pt; font-weight: bold;"><?php  putGS("Select destination"); ?></TD>
		</TR>
		<TR>
			<TD VALIGN="middle" ALIGN="RIGHT" style="padding-left: 20px;"><?php  putGS('Publication'); ?>: </TD>
			<TD valign="middle" ALIGN="LEFT">
				<?php if (count($allPublications) > 0) { ?>
				<SELECT NAME="f_destination_publication_id" class="input_select" ONCHANGE="if ((this.selectedIndex != 0) && (this.options[this.selectedIndex].value != <?php p($f_destination_publication_id); ?>)) {this.form.submit();}">
				<OPTION VALUE="0"><?php  putGS('---Select publication---'); ?></option>
				<?php 
				foreach ($allPublications as $tmpPublication) {
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
				<?php if (($f_destination_publication_id > 0) && (count($allIssues) > 0)) { ?>
				<SELECT NAME="f_destination_issue_id" class="input_select" ONCHANGE="if ((this.selectedIndex != 0) && (this.options[this.selectedIndex].value != <?php p($f_destination_issue_id); ?>)) { this.form.submit(); }">
				<OPTION VALUE="0"><?php  putGS('---Select issue---'); ?></option>
				<?php 
				foreach ($allIssues as $tmpIssue) {
					camp_html_select_option($tmpIssue->getIssueNumber(), $f_destination_issue_id, $tmpIssue->getName());
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
					camp_html_select_option($tmpSection->getSectionNumber(), $f_destination_section_id, $tmpSection->getName());
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
				if ( ($f_publication_id == $f_destination_publication_id) && ($f_issue_number == $f_destination_issue_id) && ($f_section_number == $f_destination_section_id)) {
					putGS("The destination section is the same as the source section."); echo "<BR>\n";
				}
			?></td>
		</tr>
		
		<tr>
			<td align="center" colspan="2">
				<INPUT TYPE="submit" Name="duplicate_button" Value="<?php putGS("Duplicate article"); ?>" <?php if (($f_destination_publication_id <= 0) || ($f_destination_issue_id <=0) || ($f_destination_section_id <= 0)) { echo 'class="button_disabled"'; } else { echo "class=\"button\""; }?> >
			</td>
		</tr>
		</TABLE>
	</TD>
</TR>
</FORM>
</table>
<p>

<?php camp_html_copyright_notice(); ?>