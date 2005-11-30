<?php 
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/DbObjectArray.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/ArticlePublish.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0, true);
$f_article_offset = Input::Get('f_article_offset', 'int', 0, true);
$ArticlesPerPage = 20;

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;	
}

if ($f_article_offset < 0) {
	$f_article_offset = 0;
}

$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
if (!$sectionObj->exists()) {
	camp_html_display_error(getGS('Section does not exist.'));
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

$allArticleLanguages = Article::GetAllLanguages();

if ($f_language_selected) {
	// Only show a specific language.
	$allArticles = Article::GetArticles($f_publication_id, $f_issue_number, 
		$f_section_number, $f_language_selected, null, $f_language_id,
		$ArticlesPerPage, $f_article_offset);
	$totalArticles = count(Article::GetArticles($f_publication_id, 
		$f_issue_number, $f_section_number, $f_language_selected));
	$numUniqueArticles = $totalArticles;
	$numUniqueArticlesDisplayed = count($allArticles);
} else {
	// Show articles in all languages.
	$allArticles = Article::GetArticles($f_publication_id, $f_issue_number,
		$f_section_number, null, null, $f_language_id,
		$ArticlesPerPage, $f_article_offset, true);
	$totalArticles = count(Article::GetArticles($f_publication_id, $f_issue_number, $f_section_number, null));
	$numUniqueArticles = Article::GetNumUniqueArticles($f_publication_id, $f_issue_number, $f_section_number);
	$numUniqueArticlesDisplayed = count(array_unique(DbObjectArray::GetColumn($allArticles, 'Number')));
}

$previousArticleNumber = 0;

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 
				  'Section' => $sectionObj);
camp_html_content_top(getGS('Article List'), $topArray);
?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="action_buttons">
<TR>
<?php if ($User->hasPermission('AddArticle')) { ?>
	<TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><A HREF="add.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
			<TD><A HREF="add.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>" ><B><?php  putGS("Add new article"); ?></B></A></TD>
		</TR>
		</TABLE>
	</TD>
<?php  } ?>
</tr>
</TABLE>
<p>

<script>
function checkAll(field)
{
	if (field) {
		for (i = 0; i < field.length; i++) {
			field[i].checked = true ;
		}
	}
}

function uncheckAll(field)
{
	if (field) {
		for (i = 0; i < field.length; i++) {
			field[i].checked = false ;
		}
	}
}
</script>
<div style="position: fixed; top: 140px;">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" class="table_input" style="background-color: #D5C3DF; border-color: #A35ACF;">
<TR>
	<TD>
		<TABLE cellpadding="0" cellspacing="0">
		<TR>
			<TD ALIGN="left">
				<FORM METHOD="GET" ACTION="index.php" NAME="selected_language">
				<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php p($f_publication_id); ?>">
				<INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php p($f_issue_number); ?>">
				<INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php p($f_section_number); ?>">
				<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php p($f_language_id); ?>">
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" >
				<TR>
					<TD><?php  putGS('Language'); ?>:</TD>
					<TD valign="middle">
						<SELECT NAME="f_language_selected" class="input_select" onchange="this.form.submit();">
						<option><?php putGS("All"); ?></option>
						<?php 
						foreach ($allArticleLanguages as $languageItem) {
							echo '<OPTION value="'.$languageItem->getLanguageId().'"' ;
							if ($languageItem->getLanguageId() == $f_language_selected) {
								echo " selected";
							}
							echo '>'.htmlspecialchars($languageItem->getName()).'</option>';
						} ?>
						</SELECT>
					</TD>
				</TR>
				</TABLE>
				</FORM>
			</TD>
			<TD style="padding-left: 20px;">
				<script>
				function action_selected(dropdownElement) 
				{
					// Verify that at least one checkbox has been selected.
					checkboxes = document.forms.article_list["f_article_code[]"];
					if (checkboxes) {
						isValid = false;
						numCheckboxesChecked = 0;
						// Special case for single checkbox 
						// (when there is only one article in the section).
						if (!checkboxes.length) {
							isValid = checkboxes.checked;
							numCheckboxesChecked = isValid ? 1 : 0;
						}
						else {
							// Multiple checkboxes
							for (var index = 0; index < checkboxes.length; index++) {
								if (checkboxes[index].checked) {
									isValid = true;
									numCheckboxesChecked++;
								}
							}
						}
						if (!isValid) {
							alert("<?php putGS("You must select at least one article to perform an action."); ?>");
							dropdownElement.options[0].selected = true;
							return;
						}
					}
					else {
						dropdownElement.options[0].selected = true;
						return;
					}
							
					// Get the index of the "delete" option.
					deleteOptionIndex = -1;
					translateOptionIndex = -1;
					for (var index = 0; index < dropdownElement.options.length; index++) {
						if (dropdownElement.options[index].value == "delete") {
							deleteOptionIndex = index;
						}
						if (dropdownElement.options[index].value == "translate") {
							translateOptionIndex = index;
						}
					}
					
					// if the user has selected the "delete" option
					if (dropdownElement.selectedIndex == deleteOptionIndex) {
						ok = confirm("<?php putGS("Are you sure you want to delete the selected articles?"); ?>");
						if (!ok) {
							dropdownElement.options[0].selected = true;
							return;
						}
					}
					
					// if the user selected the "translate" option
					if ( (dropdownElement.selectedIndex == translateOptionIndex) 
						 && (numCheckboxesChecked > 1) ) {
						alert("<?php putGS("You may only translate one article at a time."); ?>");
						dropdownElement.options[0].selected = true;
						return;
					}
					
					// do the action if it isnt the first or second option
					if ( (dropdownElement.selectedIndex != 0) &&  (dropdownElement.selectedIndex != 1) ) {
						dropdownElement.form.submit(); 
					}
				}
				</script>
				<FORM name="article_list" action="do_article_list_action.php" method="POST">
				<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php  p($f_publication_id); ?>">
				<INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php  p($f_issue_number); ?>">
				<INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php  p($f_section_number); ?>">
				<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php  p($f_language_id); ?>">
				<INPUT TYPE="HIDDEN" NAME="f_language_selected" VALUE="<?php  p($f_language_selected); ?>">
				<SELECT name="f_article_list_action" class="input_select" onchange="action_selected(this);">
				<OPTION value=""><?php putGS("Actions"); ?>...</OPTION>
				<OPTION value="">-----------------------</OPTION>
				
				<?php if ($User->hasPermission('Publish')) { ?>
				<OPTION value="workflow_publish"><?php putGS("Status: Publish"); ?></OPTION>
				<?php } ?>
				
				<?php if ($User->hasPermission('ChangeArticle')) { ?>
				<OPTION value="workflow_submit"><?php putGS("Status: Submit"); ?></OPTION>
				<?php } ?>

				<?php if ($User->hasPermission('Publish')) { ?>
				<OPTION value="workflow_new"><?php putGS("Status: Set New"); ?></OPTION>
				<?php } ?>
				
				<OPTION value="schedule_publish"><?php putGS("Schedule Publish"); ?></OPTION>
				<OPTION value="unlock"><?php putGS("Unlock"); ?></OPTION>
				
				<?php  if ($User->hasPermission('DeleteArticle')) { ?>
				<OPTION value="delete"><?php putGS("Delete"); ?></OPTION>
				<?php } ?>
				
				<?php  if ($User->hasPermission('AddArticle')) { ?>
				<OPTION value="copy"><?php putGS("Copy"); ?></OPTION>
				<OPTION value="copy_interactive"><?php putGS("Copy to another section"); ?></OPTION>
				<OPTION value="translate"><?php putGS("Translate"); ?></OPTION>
				<?php } ?>
				
				<OPTION value="move"><?php putGS("Reorder"); ?></OPTION>
				<OPTION value="preview"><?php putGS("Preview"); ?></OPTION>
				</SELECT>
			</TD>
			
			<TD style="padding-left: 5px; font-weight: bold;">
				<input type="button" class="button" value="<?php putGS("Select All"); ?>" onclick="checkAll(document.article_list['f_article_code[]']);"> 
				<input type="button" class="button" value="<?php putGS("Select None"); ?>" onclick="uncheckAll(document.article_list['f_article_code[]']);"> 
			</TD>
		</TR>
		</TABLE>
	</TD>
	
</TR>
</TABLE>
</div>
<P>
<?php 
if ($numUniqueArticlesDisplayed > 0) {
	$counter = 0;
	$color = 0;
?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list" style="padding-top: 40px;">
<TR class="table_list_header">
	<TD>&nbsp;</TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Name <SMALL>(click to edit)</SMALL>"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Type"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Author"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Status"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP"><?php  echo str_replace(' ', '<br>', getGS("Scheduled Publishing")); ?></TD>
	<TD ALIGN="center" VALIGN="TOP"><?php  echo str_replace(' ', '<br>', getGS("On Front Page")); ?></TD>
	<TD ALIGN="center" VALIGN="TOP"><?php  echo str_replace(' ', '<br>', getGS("On Section Page")); ?></TD>
	<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Images"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Topics"); ?></TD>
</TR>
<?php 
$uniqueArticleCounter = 0;
foreach ($allArticles as $articleObj) {
	if ($articleObj->getArticleNumber() != $previousArticleNumber) {
		$uniqueArticleCounter++;
	}
	if ($uniqueArticleCounter > $ArticlesPerPage) {
		break;
	}
	$timeDiff = camp_time_diff_str($articleObj->getLockTime());
	if ($articleObj->isLocked() && ($timeDiff['days'] <= 0) && ($articleObj->getLockedByUser() != $User->getUserId())) {
	    $rowClass = "article_locked";
	}
	else {
    	if ($color) { 
    	    $color=0; 
    	    $rowClass = "list_row_even";
    	} else { 
    	    $color=1; 
    	    $rowClass = "list_row_odd";
    	} 
	}
	?>	
	<TR class="<?php p($rowClass); ?>">
		<TD>
			<input type="checkbox" value="<?php p($articleObj->getArticleNumber().'_'.$articleObj->getLanguageId()); ?>" name="f_article_code[]" class="input_checkbox">
		</TD>
		<TD <?php if ($articleObj->getArticleNumber() == $previousArticleNumber) { ?>class="translation_indent"<?php } ?>>
		
		<?php
		if ($articleObj->getArticleNumber() != $previousArticleNumber) { 
			echo $f_article_offset + $uniqueArticleCounter.". ";
		}
		// Is article locked?
		if ($articleObj->isLocked() && ($timeDiff['days'] <= 0)) {
            $lockUserObj =& new User($articleObj->getLockedByUser());
			if ($timeDiff['hours'] > 0) {
				$lockInfo = getGS('The article has been locked by $1 ($2) $3 hour(s) and $4 minute(s) ago.',
					  htmlspecialchars($lockUserObj->getRealName()),
					  htmlspecialchars($lockUserObj->getUserName()),
					  $timeDiff['hours'], $timeDiff['minutes']); 
			}
			else {
				$lockInfo = getGS('The article has been locked by $1 ($2) $3 minute(s) ago.',
					  htmlspecialchars($lockUserObj->getRealName()),
					  htmlspecialchars($lockUserObj->getUserName()),
					  $timeDiff['minutes']);
			}
		    
		    ?>
		    <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/lock.png" width="22" height="22" border="0" alt="<?php  p($lockInfo); ?>" title="<?php p($lockInfo); ?>">
		    <?php
		}
		?>
		<A HREF="/<?php echo $ADMIN; ?>/articles/edit.php?f_publication_id=<?php  p($f_publication_id); ?>&f_issue_number=<?php  p($f_issue_number); ?>&f_section_number=<?php  p($f_section_number); ?>&f_article_number=<?php p($articleObj->getArticleNumber()); ?>&f_language_id=<?php  p($f_language_id); ?>&f_language_selected=<?php p($articleObj->getLanguageId()); ?>"><?php  p(htmlspecialchars($articleObj->getTitle())); ?>&nbsp;</A> (<?php p(htmlspecialchars($articleObj->getLanguageName())); ?>)
		</TD>
		<TD ALIGN="RIGHT">
			<?php p(htmlspecialchars($articleObj->getType()));  ?>
		</TD>

		<TD ALIGN="RIGHT">
			<?php 
			$articleCreator =& new User($articleObj->getCreatorId());
			p(htmlspecialchars($articleCreator->getRealName()));  ?>
		</TD>

		<TD ALIGN="CENTER">
			<?php 
			if ($articleObj->getPublished() == "Y") { 
				putGS("Published");
			}
			elseif ($articleObj->getPublished() == "N") { 
				putGS("New");
			}
			elseif ($articleObj->getPublished() == "S") { 
				putGS("Submitted");
			}
			?>
		</TD>
		
		<TD ALIGN="CENTER">
			<?php 
			if ($articleObj->getPublished() != 'N') { 
				$events = ArticlePublish::GetArticleEvents($articleObj->getArticleNumber(),
					$articleObj->getLanguageId());
				if (count($events) > 0) { ?>
			<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/automatic_publishing.png" alt="<?php  putGS("Scheduled Publishing"); ?>" title="<?php  putGS("Scheduled Publishing"); ?>" border="0" width="22" height="22">
				<?php 
				}
			} else { ?>
				&nbsp;<?PHP
			}
			?>
		</TD>
		
		<TD><?php echo $articleObj->onFrontPage() ? "Yes" : "No"; ?></TD>
		<TD><?php echo $articleObj->onSectionPage() ? "Yes" : "No"; ?></TD>
		<TD><?php echo count(ArticleImage::GetImagesByArticleNumber($articleObj->getArticleNumber())); ?></TD>
		<TD><?php echo count(ArticleTopic::GetArticleTopics($articleObj->getArticleNumber())); ?></TD>
		
		<?php
		if ($articleObj->getArticleNumber() != $previousArticleNumber) {
			$previousArticleNumber = $articleObj->getArticleNumber();
		}
		?>	
	</TR>
	<?php 
} // foreach
?>	
</table>
<table class="table_list" style="padding-top: 5px;">
<tr>
	<td>
		<b><?php putGS("$1 articles found", $numUniqueArticles); ?></b>
	</td>
</tr>
<TR>
	<TD NOWRAP>
		<?php 
    	if ($f_article_offset > 0) { ?>
			<B><A HREF="index.php?f_publication_id=<?php  p($f_publication_id); ?>&f_issue_number=<?php  p($f_issue_number); ?>&f_section_number=<?php  p($f_section_number); ?>&f_language_id=<?php  p($f_language_id); ?>&f_language_selected=<?php  p($f_language_selected); ?>&f_article_offset=<?php  p(max(0, ($f_article_offset - $ArticlesPerPage))); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
		<?php  }

    	if ( ($f_article_offset + $ArticlesPerPage) < $numUniqueArticles) { 
    		if ($f_article_offset > 0) {
    			?>|<?php
    		}
    		?>
			 <B><A HREF="index.php?f_publication_id=<?php  p($f_publication_id); ?>&f_issue_number=<?php  p($f_issue_number); ?>&f_section_number=<?php  p($f_section_number); ?>&f_language_id=<?php  p($f_language_id); ?>&f_language_selected=<?php  p($f_language_selected); ?>&f_article_offset=<?php  p(min( ($numUniqueArticles-1), ($f_article_offset + $ArticlesPerPage))); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
		<?php  } ?>
	</TD>
</TR>
</TABLE>
</form>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No articles.'); ?></LI>
	</BLOCKQUOTE>
<?php  } ?>
<?php camp_html_copyright_notice(); ?>