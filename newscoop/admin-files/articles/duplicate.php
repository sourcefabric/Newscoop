<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/article_common.php");

$translator = \Zend_Registry::get('container')->getService('translator');
// Optional input, for articles that are inside of sections.
$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);


$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_code = Input::Get('f_article_code', 'array', array(), true);
$f_destination_publication_id = Input::Get('f_destination_publication_id', 'int', 0, true);

$f_destination_issue_number_language = explode('_', Input::Get('f_destination_issue_number', 'str', '0_'.$f_language_id, true));
$f_destination_issue_number = $f_destination_issue_number_language[0];
$f_destination_issue_language_id = $f_destination_issue_number_language[1];

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
	if (!$g_user->hasPermission("AddArticle")) {
		camp_html_display_error($translator->trans("You do not have the right to add articles."));
		exit;
	}
} elseif ($f_action == "move") {
	if (!$g_user->hasPermission("MoveArticle")) {
		camp_html_display_error($translator->trans("You do not have the right to move articles.", array(), 'articles'));
		exit;
	}
} elseif ($f_action == "publish") {
	if (!$g_user->hasPermission("Publish")) {
		camp_html_display_error($translator->trans("You do not have the right to publish articles.", array(), 'articles'));
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
		list($articleId, $languageId) = explode("_", $tmpCodeStr);
		$articleNames[$articleId][$languageId] = Input::Get($key, 'string', '', true);
	}
	if (!strncmp($key, "f_do_copy_", strlen("f_do_copy_"))) {
		$tmpCodeStr = str_replace("f_do_copy_", "", $key);
		list($articleId, $languageId) = explode("_", $tmpCodeStr);
		$doAction[$articleId][$languageId] = Input::Get($key, 'string', '', true);
	}
}


// $articles array:
// The articles that were initially selected to perform the move or duplicate upon.
$articles = array();
$firstArticle = null;
foreach ($f_article_code as $code) {
	list($articleNumber, $languageId) = explode("_", $code);
	$tmpArticle = new Article($languageId, $articleNumber);
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
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())));
	exit;
}

if ($f_publication_id > 0) {
	$publicationObj = new Publication($f_publication_id);
	if (!$publicationObj->exists()) {
		camp_html_display_error($translator->trans('Publication does not exist.'));
		exit;
	}

	if ($f_issue_number > 0) {
	    $issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
	    if (!$issueObj->exists()) {
	        camp_html_display_error($translator->trans('Issue does not exist.'));
	        exit;
	    }

	    if ($f_section_number > 0) {
	        $sectionObj = new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
	        if (!$sectionObj->exists()) {
	            camp_html_display_error($translator->trans('Section does not exist.'));
	            exit;
	        }
	    }
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
$allIssues = Issue::GetIssues($f_destination_publication_id, null, null, null, null, false, array("LIMIT" => 300, "ORDER BY" => array("Number" => "DESC")/*, 'GROUP BY' => 'Number'*/), true);
	// Automatically select the issue if there is only one.
	if (count($allIssues) == 1) {
		$tmpIssue = camp_array_peek($allIssues);
		$f_destination_issue_number = $tmpIssue->getIssueNumber();
	}
}

// Get all the sections.
$allSections = array();
if ($f_destination_issue_number > 0) {
    $destIssue = new Issue($f_destination_publication_id, $f_destination_issue_language_id);
    $allSections = Section::GetSections($f_destination_publication_id, $f_destination_issue_number, $f_destination_issue_language_id, null, null, array("ORDER BY" => array("Number" => "DESC"), 'GROUP BY' => 'Number'), true);
    // Automatically select the section if there is only one.
    if (count($allSections) == 1) {
        $tmpSection = camp_array_peek($allSections);
        $f_destination_section_number = $tmpSection->getSectionNumber();
    } else {
        $tmpSection = new Section($f_destination_publication_id, $f_destination_issue_number,
            $firstArticle->getLanguageId(), $f_destination_section_number);
        if (!$tmpSection->exists()) {
            $f_destination_section_number = 0;
        }
    }
}


// Special case:
// You cannot copy the articles if there is no cooresponding translated issue/section
// in the destination issue.  For example, you cannot copy a french article to an
// issue that has ONLY an english translation.
$issueLanguages = array();
if ($f_destination_issue_number > 0) {
	$issueTranslations = Issue::GetIssues($f_destination_publication_id, null, $f_destination_issue_number, null, null, false, null, true);
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
			if (isset($doAction[$articleNumber])
			    && count($doAction[$articleNumber]) == 0) {
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
		camp_html_goto_page($srcArticleIndexUrl);
	}

	if (!empty($f_action) && !SecurityToken::isValid()) {
		camp_html_display_error($translator->trans('Invalid security token!'));
		exit;
	}

	if ($f_action == "duplicate") {
		global $controller;
        $em = Zend_Registry::get('container')->getService('em');
        $ArticleDatetimeRepository = $controller->getHelper('entity')->getRepository('Newscoop\Entity\ArticleDatetime');
        foreach ($doAction as $articleNumber => $languageArray) {
			$events = $ArticleDatetimeRepository->findBy(array('articleId' => $articleNumber));

            $languageArray = array_keys($languageArray);
			$tmpLanguageId = camp_array_peek($languageArray);

			// Error checking
			if (!isset($articles[$articleNumber][$tmpLanguageId])) {
				continue;
			}

			// Grab the first article - it doesnt matter which one.
			$tmpArticle = $articles[$articleNumber][$tmpLanguageId];

			// Copy all the translations requested.
			$newArticles = $tmpArticle->copy($f_destination_publication_id,
							  				 $f_destination_issue_number,
							  				 $f_destination_section_number,
							  				 $g_user->getUserId(),
							  				 $languageArray);

			// Set properties for each new copy.
			foreach ($newArticles as $newArticle) {
    			// Set the name of the new copy
				$newArticle->setTitle($articleNames[$articleNumber][$newArticle->getLanguageId()]);
				// Set the default "comment enabled" status based
				// on the publication config settings.
				if ($f_destination_publication_id > 0) {
                    $tmpPub = new Publication($f_destination_publication_id);
                    $commentDefault = $tmpPub->commentsArticleDefaultEnabled();
                    $newArticle->setCommentsEnabled($commentDefault);
            	}

                foreach ($events as $event) {
                    //$repo->add($timeSet, $articleId, 'schedule');
                    $newEvent = $ArticleDatetimeRepository->getEmpty();
                    $newEvent->setArticleId($newArticle->getArticleNumber());
                    $newEvent->setArticleType($event->getArticleType());
                    $newEvent->setStartDate($event->getStartDate());
                    $newEvent->setStartTime($event->getStartTime());
                    $newEvent->setEndDate($event->getEndDate());
                    $newEvent->setEndTime($event->getEndTime());
                    $newEvent->setRecurring($event->getRecurring());
                    $newEvent->setFieldName($event->getFieldName());
                    $em->persist($newEvent);
                }

                $em->flush();

                \Zend_Registry::get('container')->getService('dispatcher')
                  ->dispatch('article.duplicate', new \Newscoop\EventDispatcher\Events\GenericEvent($this, array(
                        'article' => $newArticle,
                        'orginal_article_number' => $articleNumber
                    )));
			}
		}
		if ($f_mode == "single") {
			$tmpArticle = camp_array_peek($newArticles);
			$url = camp_html_article_url($tmpArticle, $tmpArticle->getLanguageId(), "edit.php");
		} else {
			$url = $destArticleIndexUrl;
		}
		ArticleIndex::RunIndexer(3, 10, true);

		camp_html_add_msg($translator->trans("Article(s) duplicated.", array(), 'articles'), "ok");
		camp_html_goto_page($url);

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

                \Zend_Registry::get('container')->getService('dispatcher')
                    ->dispatch('article.move', new \Newscoop\EventDispatcher\Events\GenericEvent($this, array(
                        'article' => $tmpArticle,
                    )));
			}
		}
		$tmpArticle = camp_array_peek($tmpArticles);
		if ($f_mode == "single") {
			$url = camp_html_article_url($tmpArticle, $tmpArticle->getLanguageId(), "edit.php");
		} else {
			$url = $destArticleIndexUrl;
		}
		ArticleIndex::RunIndexer(3, 10, true);
		camp_html_add_msg($translator->trans("Article moved.", array(), 'articles'), "ok");
		camp_html_goto_page($url);

	} elseif ($f_action == "publish") {

		// Publish all the articles requested.
		$tmpArticles = array();
		foreach ($doAction as $articleNumber => $languageArray) {
			foreach ($languageArray as $languageId => $action) {
				$tmpArticle = new Article($languageId, $articleNumber);
				$tmpArticle->setTitle($articleNames[$articleNumber][$languageId]);

				// Check if the name already exists in the destination section.
				$conflictingArticles = Article::GetByName($tmpArticle->getTitle(),
								          $f_destination_publication_id,
							 	          $f_destination_issue_number,
								          $f_destination_section_number, null, true);
				if (count($conflictingArticles) > 0) {
					$conflictingArticle = array_pop($conflictingArticles);
					$conflictingArticleLink = camp_html_article_url($conflictingArticle,
									$conflictingArticle->getLanguageId(),
									"edit.php");
    				camp_html_add_msg($translator->trans("The article could not be published.", array(), 'articles')." ".$translator->trans("You cannot have two articles in the same section with the same name.  The article name you specified is already in use by the article $1.", array(
    					'$1' => "<a href='$conflictingArticleLink'>".$conflictingArticle->getName()."</a>"), 'articles'));
     				$args = $_REQUEST;
     				unset($args["action_button"]);
					unset($args["f_article_code"]);
					$argsStr = camp_implode_keys_and_values($args, "=", "&");
					foreach ($_REQUEST["f_article_code"] as $code) {
						$argsStr .= "&f_article_code[]=$code";
					}
					$backLink = "/$ADMIN/articles/duplicate.php?$argsStr";
					camp_html_goto_page($backLink);
				} else {
					$tmpArticle->move($f_destination_publication_id,
					 	              $f_destination_issue_number,
								      $f_destination_section_number);
					$tmpArticle->setWorkflowStatus('Y');

                    \Zend_Registry::get('container')->getService('dispatcher')
                        ->dispatch('article.publish', new \Newscoop\EventDispatcher\Events\GenericEvent($this, array(
                            'article' => $tmpArticle,
                        )));

					$tmpArticles[] = $tmpArticle;
				}
			}
		}
		$tmpArticle = camp_array_peek($tmpArticles);
		if ($f_mode == "single") {
			$url = camp_html_article_url($tmpArticle, $tmpArticle->getLanguageId(), "edit.php");
		} else {
			$url = $destArticleIndexUrl;
		}
		ArticleIndex::RunIndexer(3, 10, true);
		camp_html_goto_page($url);
	} elseif ($f_action == "submit") {

		// Submit all the articles requested.
		$tmpArticles = array();
		foreach ($doAction as $articleNumber => $languageArray) {
			foreach ($languageArray as $languageId => $action) {
				$tmpArticle = new Article($languageId, $articleNumber);
				$tmpArticle->setTitle($articleNames[$articleNumber][$languageId]);

				// Check if the name already exists in the destination section.
				$conflictingArticles = Article::GetByName($tmpArticle->getTitle(),
								          $f_destination_publication_id,
							 	          $f_destination_issue_number,
								          $f_destination_section_number, null, true);
				if (count($conflictingArticles) > 0) {
					$conflictingArticle = array_pop($conflictingArticles);
					$conflictingArticleLink = camp_html_article_url($conflictingArticle,
									$conflictingArticle->getLanguageId(),
									"edit.php");
    				camp_html_add_msg($translator->trans("The article could not be submitted.", array(), 'articles')." ".$translator->trans("You cannot have two articles in the same section with the same name.  The article name you specified is already in use by the article '$1'.", array(
     						'$1' => "<a href='$conflictingArticleLink'>".$conflictingArticle->getName()."</a>"), 'articles'));
     				$args = $_REQUEST;
     				unset($args["action_button"]);
					unset($args["f_article_code"]);
					$argsStr = camp_implode_keys_and_values($args, "=", "&");
					foreach ($_REQUEST["f_article_code"] as $code) {
						$argsStr .= "&f_article_code[]=$code";
					}
					$backLink = "/$ADMIN/articles/duplicate.php?$argsStr";
					camp_html_goto_page($backLink);
				} else {
					$tmpArticle->move($f_destination_publication_id,
					 	              $f_destination_issue_number,
								      $f_destination_section_number);
					$tmpArticle->setWorkflowStatus('S');

                    \Zend_Registry::get('container')->getService('dispatcher')
                        ->dispatch('article.submit', new \Newscoop\EventDispatcher\Events\GenericEvent($this, array(
                            'article' => $tmpArticle,
                        )));

					$tmpArticles[] = $tmpArticle;
				}
			}
		}
		$tmpArticle = camp_array_peek($tmpArticles);
		if ($f_mode == "single") {
			$url = camp_html_article_url($tmpArticle, $tmpArticle->getLanguageId(), "edit.php");
		} else {
			$url = $destArticleIndexUrl;
		}
		ArticleIndex::RunIndexer(3, 10, true);
		camp_html_goto_page($url);
	}
} // END perform the action


$title = "";
if (count($doAction) > 1) {
	if ($f_action == "duplicate") {
		$title = $translator->trans("Duplicate articles", array(), 'articles');
	} elseif ($f_action == "move") {
		$title = $translator->trans("Move articles", array(), 'articles');
	} elseif ($f_action == "publish") {
		$title = $translator->trans("Publish articles", array(), 'articles');
	} elseif ($f_action == "submit") {
		$title = $translator->trans("Submit articles", array(), 'articles');
	}
} else {
	if ($f_action == "duplicate") {
		$title = $translator->trans("Duplicate article", array(), 'articles');
	} elseif ($f_action == "move") {
		$title = $translator->trans("Move article", array(), 'articles');
	} elseif ($f_action == "publish") {
		$title = $translator->trans("Publish article", array(), 'articles');
	} elseif ($f_action == "submit") {
		$title = $translator->trans("Submit article", array(), 'articles');
	}
}

if ($f_publication_id > 0) {
	$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj,
					  'Section' => $sectionObj);
	if (count($articles) > 1) {
		$crumbs = array($translator->trans("Articles") => "/$ADMIN/articles/index.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id&f_language_selected=$f_language_selected");
		camp_html_content_top($title, $topArray, true, false, $crumbs);
	} elseif ($f_issue_number > 0 && $f_section_number > 0) {
		$topArray['Article'] = camp_array_peek(camp_array_peek($articles));
		camp_html_content_top($title, $topArray);
	} else {
	    $crumbs = array();
	    $crumbs[] = array($translator->trans("Pending articles", array(), 'articles'), "/$ADMIN/pending_articles");
	    $crumbs[] = array($title, "");
	    echo camp_html_breadcrumbs($crumbs);
	}
} else {
	$crumbs = array();
	$crumbs[] = array($translator->trans("Actions"), "");
	$crumbs[] = array($title, "");
	echo camp_html_breadcrumbs($crumbs);
}
?>

<?php if ($f_mode == "single") { ?>
<table cellpadding="1" cellspacing="0" class="action_buttons" style="padding-top: 10px;">
<tr>
	<td><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></td>
	<td><a href="<?php echo camp_html_article_url($article, $f_language_id, "edit.php"); ?>"><b><?php echo $translator->trans("Back to Edit Article"); ?></b></a></td>
</tr>
</table>
<?php } ?>

<?php camp_html_display_msgs(); ?>

<P>
<div class="page_title" style="padding-left: 18px;">
<?php p($title); ?>:
</div>

<FORM NAME="move_duplicate" METHOD="POST">
<?php echo SecurityToken::FormParameter(); ?>
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
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" style="margin-left: 10px;">
<TR>
	<TD>
		<TABLE cellpadding="3">
		<TR class="table_list_header">
			<?php if ($f_action != "move") { ?>
			<TD valign="top">
				<?php
				if ($f_action == "duplicate") {
					echo $translator->trans("Duplicate?", array(), 'articles');
				} elseif ($f_action == "publish") {
					echo $translator->trans("Publish?", array(), 'articles');
				}
				?>
			</TD>
			<?php } ?>
			<TD valign="top"><?php echo $translator->trans("Name"); ?></TD>
			<TD valign="top"><?php echo $translator->trans("Language"); ?></TD>
			<TD valign="top"><?php echo $translator->trans("Type"); ?></TD>
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
			echo $translator->trans("You cannot $1 the articles marked in red because the destination issue has not been translated into the appropriate language.", array('$1' => ($f_action == "move") ? $translator->trans("move") : $translator->trans("duplicate")), 'articles');
		?>
		</div>
	</td>
</tr>
</table>
<?php } ?>

<p>
<div class="page_title" style="padding-left: 18px;">
<?php echo $translator->trans("to section", array(), 'articles'); ?>:
</div>
<p>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="box_table">
<TR>
	<TD align="left">
		<TABLE align="left" border="0" width="100%">
		<TR>
			<td colspan="2" style="padding-left: 20px; padding-bottom: 5px;font-size: 12pt; font-weight: bold;"><?php echo $translator->trans("Select destination"); ?></TD>
		</TR>
		<TR>
			<td>
				<!-- BEGIN table for pub/issue/section selection -->
				<table border="0">

				<!-- PUBLICATION -->
				<tr>
					<TD VALIGN="middle" ALIGN="RIGHT" style="padding-left: 20px;"><?php echo $translator->trans('Publication'); ?>: </TD>
					<TD valign="middle" ALIGN="LEFT">
						<?php if (count($allPublications) > 1) { ?>
						<SELECT NAME="f_destination_publication_id" class="input_select" ONCHANGE="if (this.options[this.selectedIndex].value != <?php p($f_destination_publication_id); ?>) {this.form.submit();}">
						<OPTION VALUE="0"><?php echo $translator->trans('---Select publication---'); ?></option>
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
							<SELECT class="input_select" DISABLED><OPTION><?php echo $translator->trans('No publications'); ?></option></SELECT>
						<?php }	?>
					</td>
				</tr>

				<!-- ISSUE -->
				<tr>
					<TD VALIGN="middle" ALIGN="RIGHT" style="padding-left: 20px;"><?php echo $translator->trans('Issue'); ?>: </TD>
					<TD valign="middle" ALIGN="LEFT">
						<?php if (($f_destination_publication_id > 0) && (count($allIssues) > 1)) { ?>
						<SELECT NAME="f_destination_issue_number" class="input_select" ONCHANGE="if (this.options[this.selectedIndex].value != '<?php p($f_destination_issue_number.'_'.$f_destination_issue_language_id); ?>') { this.form.submit(); }">
						<OPTION VALUE="0"><?php echo $translator->trans('---Select issue---'); ?></option>
						<?php
						foreach ($allIssues as $tmpIssue) {
							camp_html_select_option($tmpIssue->getIssueNumber().'_'.$tmpIssue->getLanguageId(), $f_destination_issue_number.'_'.$f_destination_issue_language_id, $tmpIssue->getIssueNumber().". ".$tmpIssue->getName());
						}
						?>
						</SELECT>
						<?php } elseif (($f_destination_publication_id > 0) && (count($allIssues) == 1)) {
							$tmpIssue = camp_array_peek($allIssues);
							p(htmlspecialchars($tmpIssue->getName()));
							?>
							<input type="hidden" name="f_destination_issue_number" value="<?php p($f_destination_issue_number); ?>">
						<?php } else { ?>
							<SELECT class="input_select" DISABLED><OPTION><?php echo $translator->trans('No issues'); ?></SELECT>
						<?php } ?>
					</td>
				</tr>

				<!-- SECTION -->
				<tr>
					<TD VALIGN="middle" ALIGN="RIGHT" style="padding-left: 20px;"><?php echo $translator->trans('Section'); ?>: </TD>
					<TD valign="middle" ALIGN="LEFT">
						<?php if (($f_destination_issue_number > 0) && (count($allSections) > 1)) { ?>
						<SELECT NAME="f_destination_section_number" class="input_select" ONCHANGE="this.form.submit();">
						<OPTION VALUE="0"><?php echo $translator->trans('---Select section---'); ?></OPTION>
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
							<SELECT class="input_select" DISABLED><OPTION><?php echo $translator->trans('No sections'); ?></SELECT>
						<?php }	?>
					</td>
				</tr>
				</table>
				<!-- END table for pub/issue/section selection -->
			</TD>
		</tr>

		<tr>
			<td colspan="2"><?php
				if ( ($f_publication_id == $f_destination_publication_id) && ($f_issue_number == $f_destination_issue_number)
				&& ($f_section_number == $f_destination_section_number) && ($f_section_number > 0) && ($f_language_id == $f_destination_issue_language_id)) {
					echo $translator->trans("The destination section is the same as the source section.", array(), 'articles'); echo "<BR>\n";
				}
			?></td>
		</tr>

		<tr>
			<td align="center" colspan="2">
				<input type="submit" name="action_button" value="<?php p($title); ?>" <?php if (($f_destination_publication_id <= 0) || ($f_destination_issue_number <= 0) || ($f_destination_section_number <= 0)) { echo 'disabled="disabled" class="default-button disabled"'; } else { echo 'class="default-button"'; }?> >
			</td>
		</tr>
		</TABLE>
	</TD>
</TR>
</table>
</FORM>
<p>

<?php camp_html_copyright_notice(); ?>
