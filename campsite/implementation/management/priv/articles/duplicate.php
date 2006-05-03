<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

// Optional input, for articles that are inside of sections.
$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);


$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_code = Input::Get('f_article_code', 'array', 0);
$f_destination_publication_id = Input::Get('f_destination_publication_id', 'int', 0, true);
$f_destination_issue_number = Input::Get('f_destination_issue_number', 'int', 0, true);
$f_destination_section_number = Input::Get('f_destination_section_number', 'int', 0, true);

// $f_mode can be "single" or "multi".  This governs
// the behavior of where the user goes after they perform the action.
$f_mode = Input::Get('f_mode', 'string', 'single', true);

// $f_action can be "duplicate", "move", or "publish".
$f_action = Input::Get('f_action');

//
// Check permissions
//
if ($f_action == "duplicate") {
	if (!$User->hasPermission("AddArticle")) {
		camp_html_display_error(getGS("You do not have the right to add articles."));
		exit;
	}
} elseif ($f_action == "move") {
	if (!$User->hasPermission("MoveArticle")) {
		camp_html_display_error(getGS("You do not have the right to move articles."));
		exit;
	}
} elseif ($f_action == "publish") {
	if (!$User->hasPermission("Publish")) {
		camp_html_display_error(getGS("You do not have the right to publish articles."));
		exit;
	}
}

// Article names can change from page request to page request.
// We create $articleNames, a 2-dimensional array of article names indexed by article ID, language ID.
//
// The user can choose whether to perform an action on articles from page request to page request.
// We create $doAction, a 2-dimensional array of boolean values indexed by article ID, language ID.
$articleNames = array();
$doAction = array();
foreach ($_REQUEST as $key => $value) {
	if (!strncmp($key, "f_article_name_", strlen("f_article_name_"))) {
		$tmpCodeStr = str_replace("f_article_name_", "", $key);
		list($articleId, $languageId) = split("_", $tmpCodeStr);
		$articleNames[$articleId][$languageId] = Input::Get($key, 'string', '', true);
	}
	if (!strncmp($key, "f_do_copy_", strlen("f_do_copy_"))) {
		$tmpCodeStr = str_replace("f_do_copy_", "", $key);
		list($articleId, $languageId) = split("_", $tmpCodeStr);
		$doAction[$articleId][$languageId] = Input::Get($key, 'string', '', true);
	}
}


// $articles array:
// The articles that were initially selected to perform the move or duplicate upon.
$articles = array();
$firstArticle = null;
foreach ($f_article_code as $code) {
	list($articleNumber, $languageId) = split("_", $code);
	$tmpArticle =& new Article($languageId, $articleNumber);
	if (is_null($firstArticle)) {
		$firstArticle = $tmpArticle;
	}
	$articles[$articleNumber][$languageId] = $tmpArticle;

	// Initialize the article names on initial page request.
	// Initialize the $doAction array on initial page request.
	if (!isset($articleNames[$articleNumber][$languageId])) {
		$articleNames[$articleNumber][$languageId] = $tmpArticle->getTitle();
		$doAction[$articleNumber][$languageId] = $languageId;
	}
}


// Fill in article names for translations.
// The user is automatically given the choice to perform actions on translations
// when they get to this screen.
foreach ($articles as $articleNumber => $languageArray) {
	$tmpArticle = camp_array_peek($languageArray);
	$translations = $tmpArticle->getTranslations();
	foreach ($translations as $article) {
		$articleNumber = $article->getArticleNumber();
		$articleLanguage = $article->getLanguageId();
		if (!isset($articleNames[$articleNumber][$articleLanguage])) {
			$articleNames[$articleNumber][$articleLanguage] = $article->getTitle();
		}
		if (!isset($articles[$articleNumber][$articleLanguage])) {
			$articles[$articleNumber][$articleLanguage] = $article;
		}
	}
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;
}

if ($f_publication_id > 0) {
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
}

// Get all the publications
$allPublications = Publication::GetPublications();
// Automatically select the publication if there is only one.
if (count($allPublications) == 1) {
	$tmpPublication = camp_array_peek($allPublications);
	$f_destination_publication_id = $tmpPublication->getPublicationId();
}

// Get the most recent issues.
$allIssues = array();
if ($f_destination_publication_id > 0) {
	$allIssues = Issue::GetIssues($f_destination_publication_id, $firstArticle->getLanguageId(), null, null, array("LIMIT" => 50, "ORDER BY" => array("Number" => "DESC")));
	// Automatically select the issue if there is only one.
	if (count($allIssues) == 1) {
		$tmpIssue = camp_array_peek($allIssues);
		$f_destination_issue_number = $tmpIssue->getIssueNumber();
	}
}

// Get all the sections.
$allSections = array();
if ($f_destination_issue_number > 0) {
	$destIssue =& new Issue($f_destination_publication_id);
	$allSections = Section::GetSections($f_destination_publication_id, $f_destination_issue_number, $firstArticle->getLanguageId(), array("ORDER BY" => array("Number" => "DESC")));
	// Automatically select the section if there is only one.
	if (count($allSections) == 1) {
		$tmpSection = camp_array_peek($allSections);
		$f_destination_section_number = $tmpSection->getSectionNumber();
	}
}


// Special case:
// You cannot copy the articles if there is no cooresponding translated issue/section
// in the destination issue.  For example, you cannot copy a french article to an
// issue that has ONLY an english translation.
$issueLanguages = array();
if ($f_destination_issue_number > 0) {
	$issueTranslations = Issue::GetIssues($f_destination_publication_id, null, $f_destination_issue_number);
	$issueLanguages = DbObjectArray::GetColumn($issueTranslations, "IdLanguage");
}
// $actionDenied is TRUE if any articles cannot be moved/duped.
$actionDenied = false;
foreach ($articles as $articleNumber => $languageArray) {
	foreach ($languageArray as $languageId => $article) {
		$tmpActionDenied = (count($issueLanguages) > 0) && !in_array($languageId, $issueLanguages);
		$actionDenied |= $tmpActionDenied;

		// Uncheck any articles that cannot be moved/duped.
		if ($tmpActionDenied) {
			unset($doAction[$articleNumber][$languageId]);
			if (count($doAction[$articleNumber]) == 0) {
				unset($doAction[$articleNumber]);
			}
		}
	}
}


//
// This section is executed when the user finally hits the action button.
//
if (isset($_REQUEST["action_button"])) {

	$srcArticleIndexUrl = "/$ADMIN/articles/"
				."?f_publication_id=$f_publication_id"
				."&f_issue_number=$f_issue_number"
				."&f_section_number=$f_section_number"
				."&f_language_id=$f_language_id";
	$destArticleIndexUrl = "/$ADMIN/articles/"
				."?f_publication_id=$f_destination_publication_id"
				."&f_issue_number=$f_destination_issue_number"
				."&f_section_number=$f_destination_section_number"
				."&f_language_id=$f_language_id";

	// If no actions were selected, dont do anything.
	if (($f_action != "move") && (count($doAction) == 0) ) {
		header("Location: $srcArticleIndexUrl");
		exit;
	}

	if ($f_action == "duplicate") {
		foreach ($doAction as $articleNumber => $languageArray) {
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
							  				 $f_destination_issue_number,
							  				 $f_destination_section_number,
							  				 $User->getUserId(),
							  				 $languageArray);

			// Set the names of the new copies
			foreach ($newArticles as $newArticle) {
				$newArticle->setTitle($articleNames[$articleNumber][$newArticle->getLanguageId()]);
			}
		}
		if ($f_mode == "single") {
			$tmpArticle = camp_array_peek($newArticles);
			$url = camp_html_article_url($tmpArticle, $tmpArticle->getLanguageId(), "edit.php");
		} else {
			$url = $destArticleIndexUrl;
		}
		header("Location: $url");
		exit;

	} elseif ($f_action == "move") {

		// Move all the translations requested.
		$tmpArticles = array();
		foreach ($articles as $articleNumber => $languageArray) {
			$tmpArticle = camp_array_peek($languageArray);
			$translations = $tmpArticle->getTranslations();
			foreach ($translations as $tmpArticle2) {
				$articleNumber = $tmpArticle2->getArticleNumber();
				$articleLanguage = $tmpArticle2->getLanguageId();
				$tmpArticle2->move($f_destination_publication_id,
							   	   $f_destination_issue_number,
							   	   $f_destination_section_number);
				$tmpArticle2->setTitle($articleNames[$articleNumber][$articleLanguage]);
				$tmpArticles[] = $tmpArticle2;
			}
		}
		$tmpArticle = camp_array_peek($tmpArticles);
		if ($f_mode == "single") {
			$url = camp_html_article_url($tmpArticle, $tmpArticle->getLanguageId(), "edit.php");
		} else {
			$url = $destArticleIndexUrl;
		}
		header("Location: $url");
		exit;

	} elseif ($f_action == "publish") {

		// Publish all the articles requested.
		$tmpArticles = array();
		foreach ($doAction as $articleNumber => $languageArray) {
			foreach ($languageArray as $languageId => $action) {
				$tmpArticle =& new Article($languageId, $articleNumber);

				$tmpArticle->move($f_destination_publication_id,
								  $f_destination_issue_number,
								  $f_destination_section_number);

				$tmpArticle->setTitle($articleNames[$articleNumber][$languageId]);
				$tmpArticle->setWorkflowStatus('Y');
				$tmpArticles[] = $tmpArticle;
			}
		}
		$tmpArticle = camp_array_peek($tmpArticles);
		if ($f_mode == "single") {
			$url = camp_html_article_url($tmpArticle, $tmpArticle->getLanguageId(), "edit.php");
		} else {
			$url = $destArticleIndexUrl;
		}
		header("Location: $url");
		exit;
	}
} // END perform the action


$title = "";
if (count($doAction) > 1) {
	if ($f_action == "duplicate") {
		$title = getGS("Duplicate articles");
	} elseif ($f_action == "move") {
		$title = getGS("Move articles");
	} elseif ($f_action == "publish") {
		$title = getGS("Publish articles");
	}
} else {
	if ($f_action == "duplicate") {
		$title = getGS("Duplicate article");
	} elseif ($f_action == "move") {
		$title = getGS("Move article");
	} elseif ($f_action == "publish") {
		$title = getGS("Publish article");
	}
}

if ($f_publication_id > 0) {
	$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj,
					  'Section' => $sectionObj);
	if (count($articles) > 1) {
		$crumbs = array(getGS("Articles") => "/$ADMIN/articles/index.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id&f_language_selected=$f_language_selected");
		camp_html_content_top($title, $topArray, true, false, $crumbs);
	} else {
		$topArray['Article'] = camp_array_peek(camp_array_peek($articles));
		camp_html_content_top($title, $topArray);
	}
} else {
	$crumbs = array();
	$crumbs[] = array(getGS("Actions"), "");
	$crumbs[] = array($title, "");
	echo camp_html_breadcrumbs($crumbs);
}
?>

<?php if ($f_mode == "single") { ?>
<table cellpadding="1" cellspacing="0" class="action_buttons" style="padding-top: 10px;">
<tr>
	<td><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></td>
	<td><a href="<?php echo camp_html_article_url($article, $f_language_id, "edit.php"); ?>"><b><?php putGS("Back to Edit Article"); ?></b></a></td>
</table>
<?php } ?>

<P>
<div class="page_title" style="padding-left: 18px;">
<?php p($title); ?>:
</div>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" style="margin-left: 10px;">
<FORM NAME="move_duplicate" METHOD="POST">
<?php if ($f_publication_id > 0) { ?>
<input type="hidden" name="f_publication_id" value="<?php p($f_publication_id); ?>">
<input type="hidden" name="f_issue_number" value="<?php p($f_issue_number); ?>">
<input type="hidden" name="f_section_number" value="<?php p($f_section_number); ?>">
<input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>">
<?php } ?>
<input type="hidden" name="f_mode" value="<?php p($f_mode); ?>">
<input type="hidden" name="f_action" value="<?php p($f_action); ?>">
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
			<?php if ($f_action != "move") { ?>
			<TD valign="top">
				<?php
				if ($f_action == "duplicate") {
					putGS("Duplicate?");
//				} elseif ($f_action == "move") {
//					putGS("Move?");
				} elseif ($f_action == "publish") {
					putGS("Publish?");
				}
				?>
			</TD>
			<?php } ?>
			<TD valign="top"><?php putGS("Name"); ?></TD>
			<TD valign="top"><?php putGS("Language"); ?></TD>
			<TD valign="top"><?php putGS("Type"); ?></TD>
		</TR>

		<?php
		$color = 0;
		foreach ($articles as $languageArray) {
			$count = 0;
			foreach ($languageArray as $languageId => $article) {
				$bad = (count($issueLanguages) > 0) && !in_array($languageId, $issueLanguages);
				$articleNumber = $article->getArticleNumber();
			?>
		<TR class="<?php if ($color) { ?>list_row_even<?php } else { ?>list_row_odd<?php } $color = !$color; ?>" >
			<?php
			// When moving articles, you must move all translations as well,
			// so the user is not allowed to opt-opt of moving them.
			if ($f_action != "move") { ?>
			<TD <?php if ($bad) { ?>style="border-left: 3px solid #AF2041; background-color: #FFD4E4;"<?php } ?>>
				<input type="checkbox" name="f_do_copy_<?php p($articleNumber."_".$languageId); ?>" value="" <?php if ($bad) { echo "disabled"; } elseif (isset($doAction[$articleNumber][$languageId])) { echo "CHECKED"; } ?>>
			</TD>
			<?php } ?>
			<TD <?php if ($count++ > 0) { ?>class="translation_indent"<?php } ?> <?php if ($bad) { ?>style="background-color: #FFD4E4;"<?php } ?>>
				<INPUT TYPE="TEXT" NAME="f_article_name_<?php p($articleNumber."_".$languageId); ?>" SIZE="50" MAXLENGTH="256" VALUE="<?php  p(htmlspecialchars($articleNames[$articleNumber][$languageId])); ?>" class="input_text">
			</TD>

			<TD <?php if ($bad) { ?>style="background-color: #FFD4E4"<?php } ?>>
				<B><?php p(htmlspecialchars($article->getLanguageName())); ?></B>
			</TD>

			<TD <?php if ($bad) { ?>style="background-color: #FFD4E4; border-right: 3px solid #AF2041;"<?php } ?>>
				<B><?php p(htmlspecialchars($article->getTranslateType())); ?></B>
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

<?php if ($actionDenied) { ?>
<table width="565px">
<tr>
	<td colspan="2" style="padding-left: 17px; padding-bottom: 8px;" align="center">
		<div style="border: 1px solid #AF2041; background-color: #FFD4E4; font-size: 12pt; padding: 5px; font-weight: bold; color: #AF2041;">
		<?php
			putGS("You cannot $1 the articles marked in red because the destination issue has not been translated into the appropriate language.", ($f_action == "move") ? getGS("move") : getGS("duplicate"));
		?>
		</div>
	</td>
</tr>
</table>
<?php } ?>

<p>
<div class="page_title" style="padding-left: 18px;">
<?php putGS("to section"); ?>:
</div>
<p>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input" width="400px" style="margin-left: 18px;">
<TR>
	<TD align="left">
		<TABLE align="left" border="0" width="100%">
		<TR>
			<td colspan="2" style="padding-left: 20px; padding-bottom: 5px;font-size: 12pt; font-weight: bold;"><?php  putGS("Select destination"); ?></TD>
		</TR>
		<TR>
			<td>
				<!-- BEGIN table for pub/issue/section selection -->
				<table border="0">

				<!-- PUBLICATION -->
				<tr>
					<TD VALIGN="middle" ALIGN="RIGHT" style="padding-left: 20px;"><?php  putGS('Publication'); ?>: </TD>
					<TD valign="middle" ALIGN="LEFT">
						<?php if (count($allPublications) > 1) { ?>
						<SELECT NAME="f_destination_publication_id" class="input_select" ONCHANGE="if (this.options[this.selectedIndex].value != <?php p($f_destination_publication_id); ?>) {this.form.submit();}">
						<OPTION VALUE="0"><?php  putGS('---Select publication---'); ?></option>
						<?php
						foreach ($allPublications as $tmpPublication) {
							camp_html_select_option($tmpPublication->getPublicationId(), $f_destination_publication_id, $tmpPublication->getName());
						}
						?>
						</SELECT>
						<?php } elseif (count($allPublications) == 1) {
							$tmpPublication = camp_array_peek($allPublications);
							p(htmlspecialchars($tmpPublication->getName()));
							?>
							<input type="hidden" name="f_destination_publication_id" value="<?php p($tmpPublication->getPublicationId()); ?>">

						<?php } else { ?>
							<SELECT class="input_select" DISABLED><OPTION><?php  putGS('No publications'); ?></option></SELECT>
						<?php }	?>
					</td>
				</tr>

				<!-- ISSUE -->
				<tr>
					<TD VALIGN="middle" ALIGN="RIGHT" style="padding-left: 20px;"><?php  putGS('Issue'); ?>: </TD>
					<TD valign="middle" ALIGN="LEFT">
						<?php if (($f_destination_publication_id > 0) && (count($allIssues) > 1)) { ?>
						<SELECT NAME="f_destination_issue_number" class="input_select" ONCHANGE="if (this.options[this.selectedIndex].value != <?php p($f_destination_issue_number); ?>) { this.form.submit(); }">
						<OPTION VALUE="0"><?php  putGS('---Select issue---'); ?></option>
						<?php
						foreach ($allIssues as $tmpIssue) {
							camp_html_select_option($tmpIssue->getIssueNumber(), $f_destination_issue_number, $tmpIssue->getIssueNumber().". ".$tmpIssue->getName());
						}
						?>
						</SELECT>
						<?php } elseif (($f_destination_publication_id > 0) && (count($allIssues) == 1)) {
							$tmpIssue = camp_array_peek($allIssues);
							p(htmlspecialchars($tmpIssue->getName()));
							?>
							<input type="hidden" name="f_destination_issue_number" value="<?php p($f_destination_issue_number); ?>">
						<?php } else { ?>
							<SELECT class="input_select" DISABLED><OPTION><?php  putGS('No issues'); ?></SELECT>
						<?php } ?>
					</td>
				</tr>

				<!-- SECTION -->
				<tr>
					<TD VALIGN="middle" ALIGN="RIGHT" style="padding-left: 20px;"><?php  putGS('Section'); ?>: </TD>
					<TD valign="middle" ALIGN="LEFT">
						<?php if (($f_destination_issue_number > 0) && (count($allSections) > 1)) { ?>
						<SELECT NAME="f_destination_section_number" class="input_select" ONCHANGE="this.form.submit();">
						<OPTION VALUE="0"><?php  putGS('---Select section---'); ?></OPTION>
						<?php
						$previousSection = camp_array_peek($allSections);
						foreach ($allSections as $tmpSection) {
							camp_html_select_option($tmpSection->getSectionNumber(), $f_destination_section_number, $tmpSection->getName());
						}
						?>
						</SELECT>
						<?php } elseif (($f_destination_issue_number > 0) && (count($allSections) == 1)) {
							$tmpSection = camp_array_peek($allSections);
							p(htmlspecialchars($tmpSection->getName()));
							?>
							<input type="hidden" name="f_destination_section_number" value="<?php p($f_destination_section_number); ?>">
						<?php } else { ?>
							<SELECT class="input_select" DISABLED><OPTION><?php  putGS('No sections'); ?></SELECT>
						<?php }	?>
					</td>
				</tr>
				</table>
				<!-- END table for pub/issue/section selection -->
			</TD>
		</tr>

		<tr>
			<td colspan="2"><?php
				if ( ($f_publication_id == $f_destination_publication_id) && ($f_issue_number == $f_destination_issue_number) && ($f_section_number == $f_destination_section_number)) {
					putGS("The destination section is the same as the source section."); echo "<BR>\n";
				}
			?></td>
		</tr>

		<tr>
			<td align="center" colspan="2">
				<INPUT TYPE="submit" Name="action_button" Value="<?php p($title); ?>" <?php if ( ($f_destination_publication_id <= 0) || ($f_destination_issue_number <= 0) || ($f_destination_section_number <= 0)) { echo 'class="button_disabled"'; } else { echo "class=\"button\""; }?> >
			</td>
		</tr>
		</TABLE>
	</TD>
</TR>
</FORM>
</table>
<p>

<?php camp_html_copyright_notice(); ?>