<?php
require_once($GLOBALS['g_campsiteDir']."/db_connect.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Input.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Publication.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Issue.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Section.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Article.php");
require_once($GLOBALS['g_campsiteDir']."/classes/ArticlePublish.php");
require_once($GLOBALS['g_campsiteDir']."/classes/IssuePublish.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Language.php");
require_once($GLOBALS['g_campsiteDir']."/classes/SimplePager.php");
camp_load_translation_strings("home");
camp_load_translation_strings("articles");
camp_load_translation_strings("api");

if ($g_user->hasPermission('ChangeArticle') || $g_user->hasPermission('Publish')) {
	$defaultScreen = "submitted_articles";
} else {
	$defaultScreen = "your_articles";
}
$f_screen = camp_session_get("f_screen", $defaultScreen);
$f_submitted_articles_offset = camp_session_get('f_submitted_articles_offset', 0);
$f_your_articles_offset = camp_session_get('f_your_articles_offset', 0);
$f_unplaced_articles_offset = camp_session_get('f_unplaced_articles_offset', 0);
$f_popular_articles_offset = camp_session_get('f_popular_articles_offset', 0);
$NumDisplayArticles = 20;

list($YourArticles, $NumYourArticles) = Article::GetArticlesByUser($g_user->getUserId(), $f_your_articles_offset,
	$NumDisplayArticles);

list($SubmittedArticles, $NumSubmittedArticles) = Article::GetSubmittedArticles($f_submitted_articles_offset, $NumDisplayArticles);

list($unplacedArticles, $numUnplacedArticles) = Article::GetUnplacedArticles($f_unplaced_articles_offset, $NumDisplayArticles);
$popularArticlesParams = array(new ComparisonOperation('published', new Operator('is'), 'true'),
                               new ComparisonOperation('reads', new Operator('greater'), '0'));
$popularArticles = Article::GetList($popularArticlesParams, array(array('field'=>'bypopularity', 'dir'=>'desc')), $f_popular_articles_offset, $NumDisplayArticles, $popularArticlesCount, true);

$yourArticlesPager = new SimplePager($NumYourArticles, $NumDisplayArticles, "f_your_articles_offset", "home.php?f_screen=your_articles&");
$submittedArticlesPager = new SimplePager($NumSubmittedArticles, $NumDisplayArticles, 'f_submitted_articles_offset', 'home.php?f_screen=submitted_articles&');
$unplacedArticlesPager = new SimplePager($numUnplacedArticles, $NumDisplayArticles, 'f_unplaced_articles_offset', 'home.php?f_screen=unplaced_articles&');
$popularArticlesPager = new SimplePager($popularArticlesCount, $NumDisplayArticles, 'f_popular_articles_offset', 'home.php?f_screen=popular_articles&');

$recentlyPublishedArticles = Article::GetRecentArticles($NumDisplayArticles);
$recentlyModifiedArticles = Article::GetRecentlyModifiedArticles($NumDisplayArticles);

$pendingArticles = ArticlePublish::GetFutureActions($NumDisplayArticles);
$pendingIssues = IssuePublish::GetFutureActions($NumDisplayArticles);
$pendingActions = array_merge($pendingArticles, $pendingIssues);
ksort($pendingActions);
$pendingActions = array_slice($pendingActions, 0, $NumDisplayArticles);

$crumbs = array();
$crumbs[] = array(getGS("Home"), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite.js"></script>
<script>
home_page_elements = new Array("your_articles",
							   "submitted_articles",
							   "recently_published_articles",
							   "recently_modified_articles",
							   "scheduled_actions",
							   "unplaced_articles",
							   "popular_articles");
home_page_links = new Array("link_your_articles",
							"link_submitted_articles",
							"link_recently_published_articles",
							"link_recently_modified_articles",
							"link_scheduled_actions",
							"link_unplaced_articles",
							"link_popular_articles");
function on_link_click(id, home_page_links)
{
	for (i = 0; i < home_page_links.length; i++) {
		element = document.getElementById(home_page_links[i]);
		if (element) {
			if (id == home_page_links[i]) {
				element.style.backgroundColor = '#CCC';
			} else {
				element.style.backgroundColor = '#FFF';
			}
		}
	}
}
</script>

<?php
echo $breadcrumbs;

$clearCache = Input::Get('clear_cache', 'string', 'no', true);
if (CampCache::IsEnabled() && ($clearCache == 'yes')
        && $g_user->hasPermission('ClearCache')) {
    // Clear cache engine's cache
    CampCache::singleton()->clear('user');
    CampCache::singleton()->clear();
    SystemPref::DeleteSystemPrefsFromCache();

    // Clear compiled templates
    require_once($GLOBALS['g_campsiteDir']."/template_engine/classes/CampTemplate.php");
    CampTemplate::singleton()->clear_compiled_tpl();

    $actionMsg = getGS('Campsite cache was cleaned up');
    $res = 'OK';
}

$syncUsers = Input::Get('sync_users', 'string', 'no', true);
if (($syncUsers == 'yes') && $g_user->hasPermission('SyncPhorumUsers')) {
    require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/users/sync_phorum_users.php");
    $actionMsg = getGS('Campsite and Phorum users were synchronized');
    $res = 'OK';
}

?>
<?php if (!empty($actionMsg)) { ?>
<table border="0" cellpadding="0" cellspacing="0" align="center">
<tr>
<?php if ($res == 'OK') { ?>
	<td class="info_message" align="center">
<?php } else { ?>
	<td class="error_message" align="center">
<?php } ?>
		<?php echo $actionMsg; ?>
	</td>
</tr>
</table>
<?php } ?>

<?php camp_html_display_msgs("0.25em", "0.25em"); ?>

<TABLE BORDER="0" CELLSPACING="4" CELLPADDING="2" WIDTH="100%">
<TR>
	<TD VALIGN="TOP" align="left" nowrap width="1%">
		<table cellpadding="4" cellspacing="3">

		<?php if ($g_user->hasPermission('ChangeArticle') || $g_user->hasPermission('Publish')) { ?>
		<tr><td nowrap><a href="javascript: void(0);" id="link_submitted_articles" onclick="HideAll(home_page_elements); ShowElement('submitted_articles'); on_link_click('link_submitted_articles', home_page_links);"  style="font-weight: bold; color: #333; padding: 5px; <?php if ($f_screen == "submitted_articles") { echo 'background-color:#CCC;'; } ?>"><?php putGS("Submitted Articles"); ?></a></td></tr>
		<?php } ?>

		<tr><td nowrap><a href="javascript: void(0);" id="link_your_articles" onclick="HideAll(home_page_elements); ShowElement('your_articles'); on_link_click('link_your_articles', home_page_links);"  style="font-weight: bold; color: #333; padding: 5px; <?php if ($f_screen == "your_articles") { echo 'background-color:#CCC;'; } ?>"><?php putGS("Your Articles"); ?></a></td></tr>

		<tr><td nowrap><a href="javascript: void(0);" id="link_recently_published_articles" onclick="HideAll(home_page_elements); ShowElement('recently_published_articles'); on_link_click('link_recently_published_articles', home_page_links);"  style="font-weight: bold; color: #333; padding: 5px; <?php if ($f_screen == "recently_published_articles") { echo 'background-color:#CCC;'; } ?>"><?php putGS("Recently Published Articles"); ?></a></td></tr>

		<tr><td nowrap><a href="javascript: void(0);" id="link_recently_modified_articles" onclick="HideAll(home_page_elements); ShowElement('recently_modified_articles'); on_link_click('link_recently_modified_articles', home_page_links);"  style="font-weight: bold; color: #333; padding: 5px; <?php if ($f_screen == "recently_modified_articles") { echo 'background-color:#CCC;'; } ?>"><?php putGS("Recently Modified Articles"); ?></a></td></tr>

		<tr><td nowrap><a href="javascript: void(0);" id="link_scheduled_actions" onclick="HideAll(home_page_elements); ShowElement('scheduled_actions'); on_link_click('link_scheduled_actions', home_page_links);" style="font-weight: bold; color: #333; padding: 5px; <?php if ($f_screen == "scheduled_actions") { echo 'background-color:#CCC;'; } ?>"><?php putGS("Scheduled Publishing"); ?></a></td></tr>

		<tr><td nowrap><a href="javascript: void(0);" id="link_unplaced_articles" onclick="HideAll(home_page_elements); ShowElement('unplaced_articles'); on_link_click('link_unplaced_articles', home_page_links);" style="font-weight: bold; color: #333; padding: 5px; <?php if ($f_screen == "unplaced_articles") { echo 'background-color:#CCC;'; } ?>"><?php putGS("Pending Articles"); ?></a></td></tr>

        <tr><td nowrap><a href="javascript: void(0);" id="link_popular_articles" onclick="HideAll(home_page_elements); ShowElement('popular_articles'); on_link_click('link_popular_articles', home_page_links);" style="font-weight: bold; color: #333; padding: 5px; <?php if ($f_screen == "popular_articles") { echo 'background-color:#CCC;'; } ?>"><?php putGS("Most Popular Articles"); ?></a></td></tr>

		</TABLE>
	</td>

	<td valign="top" align="left" style="border-left: 1px solid black; padding-left: 10px;">

		<!-- Your articles -->
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="table_list" id="your_articles" <?php if ($f_screen != "your_articles") { echo 'style="display:none;"'; } ?>>
		<TR class="table_list_header">
			<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Your Articles"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Status"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Type"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Publication"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Issue"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Section"); ?></TD>
			<td align="center" valign="top"><?php echo str_replace(" ", "<br>", getGS("Creation date")); ?></td>
			<td align="center" valign="top"><?php putGS("Publish Schedule"); ?></td>
		</TR>

		<?php
		if (count($YourArticles) == 0) {
	        ?>
    		<TR>
			<TD colspan="7" class="list_row_odd"><?php putGS("You haven't written any articles yet."); ?></td>
	        </tr>
		    <?php
		}

		foreach ($pendingArticles as $pendingArticle) {
		  if ($pendingArticle['publish_action'] && $pendingArticle['IdUser'] == $g_user->getUserId()) {
		      $yourPendingArticles[$pendingArticle['IdLanguage']][$pendingArticle['Number']] = $pendingArticle;
		  }
		}

		$color = 0;
		foreach ($YourArticles as $tmpArticle) {
			$section = $tmpArticle->getSection();
			$language = new Language($tmpArticle->getLanguageId());
			$pub = new Publication($tmpArticle->getPublicationId());
			$issue = new Issue($tmpArticle->getPublicationId(),
								$tmpArticle->getLanguageId(),
								$tmpArticle->getIssueNumber());
			$section = new Section($tmpArticle->getPublicationId(),
									$tmpArticle->getIssueNumber(),
									$tmpArticle->getLanguageId(),
									$tmpArticle->getSectionNumber());
			camp_set_article_row_decoration($tmpArticle, $lockInfo, $rowClass, $color);
		    ?>
		<TR class="<?php echo $rowClass ?>">
			<TD valign="top">
                <?php if ($lockInfo) { ?>
	               <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/lock-16x16.png" width="16" height="16" border="0" alt="<?php  p($lockInfo); ?>" title="<?php p($lockInfo); ?>">
	            <?php } ?>
				<?php
				if ($g_user->hasPermission('ChangeArticle') || ($tmpArticle->getWorkflowStatus() == 'N')) {
					echo camp_html_article_link($tmpArticle, $section->getLanguageId(), "edit.php");
				}
				p(htmlspecialchars($tmpArticle->getTitle()." (".$language->getNativeName().")"));
				if ($g_user->hasPermission('ChangeArticle') || ($tmpArticle->getWorkflowStatus() == 'N')) {
					echo '</a>';
				}
				?>
			</TD>

			<TD align="center" valign="top">
				<?php
				echo $tmpArticle->getWorkflowDisplayString();
				?>
			</TD>

			<td align="center" valign="top">
				<?php p(htmlspecialchars($tmpArticle->getTranslateType())); ?>
			</td>

			<td valign="top">
				<?php p(htmlspecialchars($pub->getName())); ?>
			</td>

			<td valign="top">
				<?php p(htmlspecialchars($issue->getName())); ?>
			</td>

			<td valign="top">
				<?php p(htmlspecialchars($section->getName())); ?>
			</td>

			<td align="center" valign="top">
				<?php p(htmlspecialchars($tmpArticle->getCreationDate())); ?>
			</td>

			<td align="center" valign="top">
				<?php
				if ($pendingArticle = $yourPendingArticles[$tmpArticle->getLanguageId()][$tmpArticle->getArticleNumber()]) {
				    echo $pendingArticle['time_action'].'<br />';
				    if ($pendingArticle['publish_action'] == "P") {
							putGS("Publish");
						}
						if ($publishAction == "U") {
							putGS("Unpublish");
						}
				}
				?>
			</td>
		</TR>
		<?php
		} // for
    	?>

    	<TR>
    		<TD COLSPAN="2" NOWRAP style="padding-top: 10px;">
				<?php
				echo $yourArticlesPager->render();
				?>
			</TD>
		</TR>
		</TABLE>

		<!-- Submitted articles -->
		<?php if ($g_user->hasPermission('ChangeArticle') || $g_user->hasPermission('Publish')) { ?>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="table_list" id="submitted_articles" <?php if ($f_screen != "submitted_articles") { echo 'style="display:none;"'; } ?>>
		<TR class="table_list_header">
			<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Submitted Articles"); ?></TD>
			<td align="center" valign="top"><?php putGS("Publication"); ?></td>
			<td align="center" valign="top"><?php putGS("Issue"); ?></td>
			<td align="center" valign="top"><?php putGS("Section"); ?></td>
			<td align="center" valign="top"><?php putGS("Type"); ?></td>
			<td align="center" valign="top"><?php echo str_replace(" ", "<br>", getGS("Created by")); ?></td>
			<td align="center" valign="top"><?php echo str_replace(" ", "<br>", getGS("Creation date")); ?></td>
		</TR>
		<?php
	    $color=0;
	    if (count($SubmittedArticles) == 0) {
	        ?>
    		<TR>
			<TD colspan="7" class="list_row_odd"><?php putGS("There are currently no submitted articles."); ?></td>
	        </tr>
	        <?php
	    }
        $color = 0;
		foreach ($SubmittedArticles as $tmpArticle) {
			$section = $tmpArticle->getSection();
			$language = new Language($tmpArticle->getLanguageId());
			$pub = new Publication($tmpArticle->getPublicationId());
			$issue = new Issue($tmpArticle->getPublicationId(),
								$tmpArticle->getLanguageId(),
								$tmpArticle->getIssueNumber());
			$section = new Section($tmpArticle->getPublicationId(),
									$tmpArticle->getIssueNumber(),
									$tmpArticle->getLanguageId(),
									$tmpArticle->getSectionNumber());
			$creator = new User($tmpArticle->getCreatorId());
			camp_set_article_row_decoration($tmpArticle, $lockInfo, $rowClass, $color);
		    ?>
		<TR class="<?php echo $rowClass ?>">
			<TD valign="top">
                <?php if ($lockInfo) { ?>
	               <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/lock-16x16.png" width="16" height="16" border="0" alt="<?php  p($lockInfo); ?>" title="<?php p($lockInfo); ?>">
	            <?php } ?>
			<?php
			echo camp_html_article_link($tmpArticle, $section->getLanguageId(), "edit.php");
			p(htmlspecialchars($tmpArticle->getTitle()));
			p(" (".htmlspecialchars($language->getNativeName()).")</a>");
			?>
			</TD>

			<td align="center" valign="top">
				<?php p(htmlspecialchars($pub->getName())); ?>
			</td>

			<td align="center" valign="top">
				<?php p(htmlspecialchars($issue->getName())); ?>
			</td>

			<td align="center" valign="top">
				<?php p(htmlspecialchars($section->getName())); ?>
			</td>

			<td align="center" valign="top">
				<?php p(htmlspecialchars($tmpArticle->getType())); ?>
			</td>

			<td align="center" valign="top">
				<?php p(htmlspecialchars($creator->getRealName())); ?>
			</td>

			<td align="center" valign="top">
				<?php p(htmlspecialchars($tmpArticle->getCreationDate())); ?>
			</td>

		</TR>
		<?php
		} // for ($SubmittedArticles ...)
		?>

		<TR>
			<TD COLSPAN="2" NOWRAP style="padding-top: 10px;">
			<?php
			echo $submittedArticlesPager->render();
    		?>
			</TD>
		</TR>
		</TABLE>
		<?php
		} // if ($g_user->hasPermission('ChangeArticle') || $g_user->hasPermission('Publish'))
		?>

		<!-- Recently Published -->
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="table_list" id="recently_published_articles" <?php if ($f_screen != "recently_published_articles") { echo 'style="display:none;"'; } ?>>
		<TR class="table_list_header">
			<TD ALIGN="LEFT" VALIGN="TOP" ><?php  putGS("Recently Published Articles"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" nowrap><?php  putGS("Publish Date"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" nowrap><?php  putGS("Publication"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" nowrap><?php  putGS("Issue"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" nowrap><?php  putGS("Section"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" nowrap><?php  p(str_replace(" ", "<br>", getGS("On Front Page"))); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" nowrap><?php  p(str_replace(" ", "<br>", getGS("On Section Page"))); ?></TD>
		</TR>
		<?php
		if (count($recentlyPublishedArticles) == 0) {
	        ?>
    		<TR>
			<TD colspan="7" class="list_row_odd"><?php putGS("No articles have been published yet."); ?></td>
	        </tr>
		    <?php
		}
		$color = 0;
		foreach ($recentlyPublishedArticles as $tmpArticle) {
			$language = new Language($tmpArticle->getLanguageId());
			$pub = new Publication($tmpArticle->getPublicationId());
			$issue = new Issue($tmpArticle->getPublicationId(),
								$tmpArticle->getLanguageId(),
								$tmpArticle->getIssueNumber());
			$section = new Section($tmpArticle->getPublicationId(),
									$tmpArticle->getIssueNumber(),
									$tmpArticle->getLanguageId(),
									$tmpArticle->getSectionNumber());
			camp_set_article_row_decoration($tmpArticle, $lockInfo, $rowClass, $color);
		    ?>
		<TR class="<?php echo $rowClass ?>">
			<TD valign="top">
                <?php if ($lockInfo) { ?>
	               <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/lock-16x16.png" width="16" height="16" border="0" alt="<?php  p($lockInfo); ?>" title="<?php p($lockInfo); ?>">
	            <?php } ?>
				<?php
				if ($g_user->hasPermission('ChangeArticle')) {
    				echo camp_html_article_link($tmpArticle, $tmpArticle->getLanguageId(), "edit.php");
				}
				p(htmlspecialchars($tmpArticle->getTitle(). " (".$language->getNativeName().")"));
				if ($g_user->hasPermission('ChangeArticle')) {
    				echo '</a>';
				}
				?>
			</TD>
			<td nowrap valign="top"><?php echo $tmpArticle->getPublishDate(); ?></td>

			<td valign="top">
				<?php p(htmlspecialchars($pub->getName())); ?>
			</td>

			<td valign="top">
				<?php p(htmlspecialchars($issue->getName())); ?>
			</td>

			<td valign="top">
				<?php p(htmlspecialchars($section->getName())); ?>
			</td>

			<td align="center" valign="top">
				<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/<?php p($tmpArticle->onFrontPage() ? "is_shown.png" : "is_hidden.png"); ?>" border="0">
			</td>

			<td align="center" valign="top">
				<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/<?php p($tmpArticle->onSectionPage() ? "is_shown.png" : "is_hidden.png"); ?>" border="0">
			</td>
        </tr>
		<?php
		} // for
    	?>
        </table>


		<!-- Recently Modified -->
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="table_list" id="recently_modified_articles" <?php if ($f_screen != "recently_modified_articles") { echo 'style="display:none;"'; } ?>>
		<TR class="table_list_header">
			<TD ALIGN="LEFT" VALIGN="TOP" ><?php  putGS("Recently Modified Articles"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" nowrap><?php  putGS("Modification Date"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" nowrap><?php  putGS("Publication"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" nowrap><?php  putGS("Issue"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" nowrap><?php  putGS("Section"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" nowrap><?php  p(str_replace(" ", "<br>", getGS("On Front Page"))); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" nowrap><?php  p(str_replace(" ", "<br>", getGS("On Section Page"))); ?></TD>
		</TR>
		<?php
		if (count($recentlyModifiedArticles) == 0) {
	        ?>
    		<TR>
			<TD colspan="7" class="list_row_odd"><?php putGS("No articles have been modified yet."); ?></td>
	        </tr>
		    <?php
		}
		$color = 0;
		foreach ($recentlyModifiedArticles as $tmpArticle) {
			$language = new Language($tmpArticle->getLanguageId());
			$pub = new Publication($tmpArticle->getPublicationId());
			$issue = new Issue($tmpArticle->getPublicationId(),
								$tmpArticle->getLanguageId(),
								$tmpArticle->getIssueNumber());
			$section = new Section($tmpArticle->getPublicationId(),
									$tmpArticle->getIssueNumber(),
									$tmpArticle->getLanguageId(),
									$tmpArticle->getSectionNumber());
			camp_set_article_row_decoration($tmpArticle, $lockInfo, $rowClass, $color);
		    ?>
		<TR class="<?php echo $rowClass ?>">
			<TD valign="top">
                <?php if ($lockInfo) { ?>
	               <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/lock-16x16.png" width="16" height="16" border="0" alt="<?php  p($lockInfo); ?>" title="<?php p($lockInfo); ?>">
	            <?php } ?>
				<?php
				if ($g_user->hasPermission('ChangeArticle')) {
    				echo camp_html_article_link($tmpArticle, $tmpArticle->getLanguageId(), "edit.php");
				}
				p(htmlspecialchars($tmpArticle->getTitle(). " (".$language->getNativeName().")"));
				if ($g_user->hasPermission('ChangeArticle')) {
    				echo '</a>';
				}
				?>
			</TD>
			<td nowrap valign="top"><?php echo $tmpArticle->getLastModified(); ?></td>

			<td valign="top">
				<?php p(htmlspecialchars($pub->getName())); ?>
			</td>

			<td valign="top">
				<?php p(htmlspecialchars($issue->getName())); ?>
			</td>

			<td valign="top">
				<?php p(htmlspecialchars($section->getName())); ?>
			</td>

			<td align="center" valign="top">
				<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/<?php p($tmpArticle->onFrontPage() ? "is_shown.png" : "is_hidden.png"); ?>" border="0">
			</td>

			<td align="center" valign="top">
				<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/<?php p($tmpArticle->onSectionPage() ? "is_shown.png" : "is_hidden.png"); ?>" border="0">
			</td>
        </tr>
		<?php
		} // for
    	?>
        </table>


        <!-- Scheduled Publishing -->
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="table_list" id="scheduled_actions" <?php if ($f_screen != "scheduled_actions") { echo 'style="display:none;"'; } ?>>
		<TR class="table_list_header">
			<TD ALIGN="LEFT" VALIGN="TOP" ><?php putGS("Scheduled Publishing"); ?></TD>
			<TD ALIGN="LEFT" VALIGN="TOP" nowrap><?php putGS("Event(s)"); ?></TD>
			<TD ALIGN="LEFT" VALIGN="TOP" nowrap><?php putGS("Time"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" nowrap><?php putGS("Publication"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" nowrap><?php putGS("Issue"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" nowrap><?php putGS("Section"); ?></TD>
		</TR>
		<?php
		if (count($pendingActions) == 0) {
	        ?>
    		<TR>
			<TD colspan="6" class="list_row_odd"><?php putGS("There are no pending items to be published."); ?></td>
	        </tr>
	        <?php
		}
		// Warning: the next section is a big hack!
		// Hopefully will be fixed in 2.4
		$color = 0;
		foreach ($pendingActions as $action) {
		if ($action["ObjectType"] == "article") {
			$language = new Language($action["IdLanguage"]);
			$pub = new Publication($action["IdPublication"]);
			$issue = new Issue($action["IdPublication"],
								$action["IdLanguage"],
								$action["NrIssue"]);
			$section = new Section($action["IdPublication"],
									$action["NrIssue"],
									$action["IdLanguage"],
									$action["NrSection"]);
			$tmpArticle = new Article($action['IdLanguage'], $action['Number']);
			camp_set_article_row_decoration($tmpArticle, $lockInfo, $rowClass, $color);
		    ?>
		<TR class="<?php echo $rowClass ?>">
			<TD valign="top">
                <?php if ($lockInfo) { ?>
	               <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/lock-16x16.png" width="16" height="16" border="0" alt="<?php  p($lockInfo); ?>" title="<?php p($lockInfo); ?>">
	            <?php } ?>
			    <?php putGS("Article"); ?>:
    			<?PHP
				if ($g_user->hasPermission('ChangeArticle')) { ?>
                    <a href="/<?php p($ADMIN); ?>/articles/edit.php?f_publication_id=<?php p($action["IdPublication"]); ?>&f_issue_number=<?php p($action["NrIssue"]); ?>&f_section_number=<?php p($action["NrSection"]); ?>&f_article_number=<?php p($action["Number"]); ?>&f_language_id=<?php p($action["IdLanguage"]); ?>&f_language_selected=<?php p($action["IdLanguage"]); ?>">
                	<?PHP
				}
			    echo htmlspecialchars($action["Name"]." (".$language->getNativeName().")");
				if ($g_user->hasPermission('ChangeArticle')) {
    				echo "</a>";
                }
                ?>
			</TD>
			<td nowrap valign="top">
				<?PHP
				$displayActions = array();
				if ($action["publish_action"] == 'P') {
				    $displayActions[] = getGS("Publish");
				}
				if ($action["publish_action"] == 'U') {
				    $displayActions[] = getGS("Unpublish");
				}
				if ($action["publish_on_front_page"] == 'S') {
				    $displayActions[] = getGS("Show on front page");
				}
				if ($action["publish_on_front_page"] == 'R') {
				    $displayActions[] = getGS("Remove from front page");
				}
				if ($action["publish_on_section_page"] == 'S') {
				    $displayActions[] = getGS("Show on section page");
				}
				if ($action["publish_on_section_page"] == 'R') {
				    $displayActions[] = getGS("Remove from section page");
				}
				echo implode("<br>", $displayActions)
				?>
			</td>

			<td nowrap valign="top">
                <?php echo htmlspecialchars($action["time_action"]); ?>
			</td>

			<td valign="top">
				<?php p(htmlspecialchars($pub->getName())); ?>
			</td>

			<td valign="top">
				<?php p(htmlspecialchars($issue->getName())); ?>
			</td>

			<td valign="top">
				<?php p(htmlspecialchars($section->getName())); ?>
			</td>

		<?PHP
		}
		elseif ($action["ObjectType"] == "issue") {
		  ?><TR <?php if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>><?PHP
			//$language = new Language($action["IdLanguage"]);
			$pub = new Publication($action["IdPublication"]);
			?>
			<TD valign="top"><?php putGS("Issue"); ?>:
    			<?PHP
				if ($g_user->hasPermission('ManageIssue')) { ?>
                    <a href="/<?php p($ADMIN); ?>/issues/edit.php?Pub=<?php p($action["IdPublication"]); ?>&Issue=<?php p($action["Number"]); ?>&Language=<?php p($action["IdLanguage"]); ?>">
                    <?PHP
				}
				echo htmlspecialchars($action["Name"]);
				if ($g_user->hasPermission('ManageIssue')) {
				    echo "</a>";
				}
				?>
			</TD>
			<td valign="top" nowrap><?PHP
			$displayActions = array();
			if ($action["publish_action"] == 'P') {
			    $displayActions[] = getGS("Publish");
			}
			if ($action["publish_action"] == 'U') {
			    $displayActions[] = getGS("Unpublish");
			}
			if ($action["do_publish_articles"] == 'Y') {
			    $displayActions[] = getGS("Publish articles");
			}
			echo implode("<br>", $displayActions)
			?></td>
			<td nowrap valign="top">
                <?php
                if ($g_user->hasPermission("Publish")) { ?>
                    <a href="/<?php p($ADMIN); ?>/issues/autopublish.php?Pub=<?php p($action["IdPublication"]); ?>&Issue=<?php p($action["Number"]); ?>&Language=<?php p($action["IdLanguage"]); ?>&event_id=<?php p(urlencode($action["id"])); ?>">
                    <?PHP
                }
                echo htmlspecialchars($action["time_action"]);
                if ($g_user->hasPermission("Publish")) {
                    echo "</a>";
                }
                ?>
			</td>

			<td valign="top">
				<?php p(htmlspecialchars($pub->getName())); ?>
			</td>

			<td valign="top"> -----</td>
			<td valign="top"> -----</td>
            <?PHP
		}
		?>
        </tr>
		<?php
		} // for
    	?>
    	</table>

		<!-- Unplaced articles -->
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="table_list" id="unplaced_articles" <?php if ($f_screen != "unplaced_articles") { echo 'style="display:none;"'; } ?>>
		<TR class="table_list_header">
			<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Pending Articles"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Status"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Type"); ?></TD>
			<td align="center" valign="top"><?php echo str_replace(" ", "<br>", getGS("Created by")); ?></td>
			<td align="center" valign="top"><?php echo str_replace(" ", "<br>", getGS("Creation date")); ?></td>
		</TR>

		<?php
		if (count($unplacedArticles) == 0) {
	        ?>
    		<TR>
			<TD colspan="5" class="list_row_odd"><?php putGS("There are no pending articles."); ?></td>
	        </tr>
		    <?php
		}
		$color = 0;
		foreach ($unplacedArticles as $tmpArticle) {
			$creator = new User($tmpArticle->getCreatorId());
			$section = $tmpArticle->getSection();
			$language = new Language($tmpArticle->getLanguageId());
			camp_set_article_row_decoration($tmpArticle, $lockInfo, $rowClass, $color);
		    ?>
		<TR class="<?php echo $rowClass ?>">
			<TD valign="top">
                <?php if ($lockInfo) { ?>
	               <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/lock-16x16.png" width="16" height="16" border="0" alt="<?php  p($lockInfo); ?>" title="<?php p($lockInfo); ?>">
	            <?php } ?>
				<?php
				if ($g_user->hasPermission('ChangeArticle') || ($tmpArticle->getWorkflowStatus() == 'N')) {
					echo camp_html_article_link($tmpArticle, $section->getLanguageId(), "edit.php");
				}
				p(htmlspecialchars($tmpArticle->getTitle()." (".$language->getNativeName().")"));
				if ($g_user->hasPermission('ChangeArticle') || ($tmpArticle->getWorkflowStatus() == 'N')) {
					echo '</a>';
				}
				?>
			</TD>

			<TD align="center" nowrap valign="top">
				<?php
				if ($tmpArticle->getWorkflowStatus() == "Y") {
					putGS('Published');
				}
				elseif ($tmpArticle->getWorkflowStatus() == 'S') {
					putGS('Submitted');
				}
				elseif ($tmpArticle->getWorkflowStatus() == "N") {
					putGS('New');
				}
				?>
			</TD>

			<td align="center" valign="top">
				<?php p(htmlspecialchars($tmpArticle->getType())); ?>
			</td>

			<td align="center" valign="top">
				<?php p(htmlspecialchars($creator->getRealName())); ?>
			</td>

			<td align="center" valign="top">
				<?php p(htmlspecialchars($tmpArticle->getCreationDate())); ?>
			</td>

		</TR>
		<?php
		} // for
    	?>
                <TR>
    		<TD COLSPAN="2" NOWRAP style="padding-top: 10px;">
				<?php
				echo $unplacedArticlesPager->render();
				?>
			</TD>
		</TR>
		</TABLE>

        <!-- Most popular articles -->
        <?php if ($g_user->hasPermission('ChangeArticle') || $g_user->hasPermission('Publish')) { ?>
        <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="table_list" id="popular_articles" <?php if ($f_screen != "popular_articles") { echo 'style="display:none;"'; } ?>>
        <TR class="table_list_header">
            <TD ALIGN="center" VALIGN="TOP"><?php  putGS("Most Popular Articles"); ?></TD>
            <td align="center" valign="top"><?php putGS("Publication"); ?></td>
            <td align="center" valign="top"><?php putGS("Issue"); ?></td>
            <td align="center" valign="top"><?php putGS("Section"); ?></td>
            <td align="center" valign="top"><?php putGS("Type"); ?></td>
            <td align="center" valign="top"><?php echo str_replace(" ", "<br>", getGS("Created by")); ?></td>
            <td align="center" valign="top"><?php echo str_replace(" ", "<br>", getGS("Publish date")); ?></td>
            <td align="center" valign="top"><?php putGS("Reads"); ?></td>
        </TR>
        <?php
        $color=0;
        if (count($popularArticles) == 0) {
            ?>
            <TR>
            <TD colspan="8" class="list_row_odd"><?php putGS("There are currently no articles in statistics."); ?></td>
            </tr>
            <?php
        }

        foreach ($popularArticles as $tmpArticle) {
            $section = $tmpArticle->getSection();
            $language = new Language($tmpArticle->getLanguageId());
            $pub = new Publication($tmpArticle->getPublicationId());
            $issue = new Issue($tmpArticle->getPublicationId(),
                                $tmpArticle->getLanguageId(),
                                $tmpArticle->getIssueNumber());
            $section = new Section($tmpArticle->getPublicationId(),
                                    $tmpArticle->getIssueNumber(),
                                    $tmpArticle->getLanguageId(),
                                    $tmpArticle->getSectionNumber());
            $creator = new User($tmpArticle->getCreatorId());
			camp_set_article_row_decoration($tmpArticle, $lockInfo, $rowClass, $color);
		    ?>
		<TR class="<?php echo $rowClass ?>">
			<TD valign="top">
                <?php if ($lockInfo) { ?>
	               <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/lock-16x16.png" width="16" height="16" border="0" alt="<?php  p($lockInfo); ?>" title="<?php p($lockInfo); ?>">
	            <?php } ?>
            <?php
            echo camp_html_article_link($tmpArticle, $section->getLanguageId(), "edit.php");
            p(htmlspecialchars($tmpArticle->getTitle()));
            p(" (".htmlspecialchars($language->getNativeName()).")</a>");
            ?>
            </TD>

            <td align="center" valign="top">
                <?php p(htmlspecialchars($pub->getName())); ?>
            </td>

            <td align="center" valign="top">
                <?php p(htmlspecialchars($issue->getName())); ?>
            </td>

            <td align="center" valign="top">
                <?php p(htmlspecialchars($section->getName())); ?>
            </td>

            <td align="center" valign="top">
                <?php p(htmlspecialchars($tmpArticle->getType())); ?>
            </td>

            <td align="center" valign="top">
                <?php p(htmlspecialchars($creator->getRealName())); ?>
            </td>

            <td align="center" valign="top">
                <?php p(htmlspecialchars($tmpArticle->getPublishDate())); ?>
            </td>

            <td align="center" valign="top">
                <?php p(htmlspecialchars($tmpArticle->getReads())); ?>
            </td>

        </TR>
        <?php
        } // for ($popularArticles ...)
        ?>

        <TR>
            <TD COLSPAN="2" NOWRAP style="padding-top: 10px;">
            <?php
            echo $popularArticlesPager->render();
            ?>
            </TD>
        </TR>
        </TABLE>
        <?php
        } // if ($g_user->hasPermission('ChangeArticle') || $g_user->hasPermission('Publish'))
        ?>
	</td>
</tr>
</table>


<?php camp_html_copyright_notice(); ?>