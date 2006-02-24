<?php
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Input.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Publication.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Issue.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Section.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Article.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/ArticlePublish.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/IssuePublish.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/SimplePager.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
load_common_include_files("home");
camp_load_language("articles");
camp_load_language("api");

list($access, $User) = check_basic_access($_REQUEST);	
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
$defaultScreen = "submitted_articles";
$f_screen = camp_session_get("f_screen", $defaultScreen);
$f_submitted_articles_offset = camp_session_get('f_submitted_articles_offset', 0);
$f_your_articles_offset = camp_session_get('f_your_articles_offset', 0);
$NumDisplayArticles=20;
list($YourArticles, $NumYourArticles) = Article::GetArticlesByUser($User->getUserId(), $f_your_articles_offset, 
	$NumDisplayArticles);

list($SubmittedArticles, $NumSubmittedArticles) = Article::GetSubmittedArticles($f_submitted_articles_offset, $NumDisplayArticles);

$yourArticlesPager =& new SimplePager($NumYourArticles, $NumDisplayArticles, "f_your_articles_offset", "home.php?f_screen=your_articles&");
$submittedArticlesPager =& new SimplePager($NumSubmittedArticles, $NumDisplayArticles, 'f_submitted_articles_offset', 'home.php?f_screen=submitted_articles&');

$recentlyPublishedArticles = Article::GetRecentArticles($NumDisplayArticles);

$pendingArticles = ArticlePublish::GetFutureActions($NumDisplayArticles);
$pendingIssues = IssuePublish::GetFutureActions($NumDisplayArticles);
$pendingActions = array_merge($pendingArticles, $pendingIssues);
ksort($pendingActions);
$pendingActions = array_slice($pendingActions, 0, $NumDisplayArticles);
$crumbs = array();
$crumbs[] = array(getGS("Home"), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);
?>
<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite.js"></script>
	<TITLE><?php  putGS("Home"); ?></TITLE>
	<script>
	home_page_elements = new Array("your_articles", 
								   "submitted_articles", 
								   "recently_published_articles", 
								   "scheduled_actions");
	</script>
</HEAD>
<BODY>

<?php
echo $breadcrumbs;

$restartEngine = Input::Get('restart_engine', 'string', 'no', true);
if ($restartEngine == 'yes' && $User->hasPermission("InitializeTemplateEngine")) {
	require_once($_SERVER['DOCUMENT_ROOT']."/parser_utils.php");
	if (camp_stop_parser()) {
		$resetMsg = getGS("The template engine was (re)started.");
		$res = "OK";
	} else {
		$resetMsg = getGS("The template engine could not be restarted! Please verify if the template engine was started by other user than $1.", $Campsite['APACHE_USER']);
		$res = "ERROR";
	}
	camp_start_parser();
}
?>
<?php if (!empty($resetMsg)) { ?>
<table border="0" cellpadding="0" cellspacing="0" align="center">
<tr>
<?php if ($res == 'OK') { ?>
	<td class="info_message" align="center">
<?php } else { ?>
	<td class="error_message" align="center">
<?php } ?>
		<?php echo $resetMsg; ?>
	</td>
</tr>
</table>
<?php } ?>

<TABLE BORDER="0" CELLSPACING="4" CELLPADDING="2" WIDTH="100%">
<TR>
	<TD VALIGN="TOP" align="left" nowrap width="1%">
		<table cellpadding="3" cellspacing="3">
		<tr><td nowrap><a href="javascript: void(0);" onclick="HideAll(home_page_elements); ShowElement('submitted_articles');"  style="font-weight: bold; color: #333;"><?php putGS("Submitted articles"); ?></a></td></tr>
		<tr><td nowrap><a href="javascript: void(0);" onclick="HideAll(home_page_elements); ShowElement('your_articles');"  style="font-weight: bold; color: #333;"><?php putGS("Your articles"); ?></a></td></tr>
		<tr><td nowrap><a href="javascript: void(0);" onclick="HideAll(home_page_elements); ShowElement('recently_published_articles');"  style="font-weight: bold; color: #333;"><?php putGS("Recently Published Articles"); ?></a></td></tr>
		<tr><td nowrap><a href="javascript: void(0);" onclick="HideAll(home_page_elements); ShowElement('scheduled_actions');" style="font-weight: bold; color: #333;"><?php putGS("Scheduled Publishing"); ?></a></td></tr>
		</TABLE>
	</td>
	
	<td valign="top" align="left" style="border-left: 1px solid black; padding-left: 10px;">
	
		<!-- Your articles -->
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" id="your_articles" <?php if ($f_screen != "your_articles") { echo 'style="display:none;"'; } ?>>
		<TR class="table_list_header">
			<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Your articles"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Status"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Publication"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Issue"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Section"); ?></TD>
		</TR>

		<?php 
		if (count($YourArticles) == 0) {
	        ?>
    		<TR>
			<TD colspan="3" class="list_row_odd"><?php putGS("You haven't written any articles yet."); ?></td>
	        </tr>
		    <?php
		}
		$color = 0;
		foreach ($YourArticles as $YourArticle) {
			$section = $YourArticle->getSection();
			$language =& new Language($YourArticle->getLanguageId());
			$pub =& new Publication($YourArticle->getPublicationId());
			$issue =& new Issue($YourArticle->getPublicationId(), 
								$YourArticle->getLanguageId(), 
								$YourArticle->getIssueNumber());
			$section =& new Section($YourArticle->getPublicationId(), 
									$YourArticle->getIssueNumber(), 
									$YourArticle->getLanguageId(), 
									$YourArticle->getSectionNumber());
			 ?>
		<TR <?php if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
			<TD valign="top">
				<?php 
				if ($User->hasPermission('ChangeArticle') || ($YourArticle->getPublished() == 'N')) {
					echo camp_html_article_link($YourArticle, $section->getLanguageId(), "edit.php"); 
				}
				p(htmlspecialchars($YourArticle->getTitle()." (".$language->getNativeName().")")); 
				if ($User->hasPermission('ChangeArticle') || ($YourArticle->getPublished() == 'N')) {
					echo '</a>';
				}
				?>
			</TD>
			
			<TD align="center" nowrap valign="top">
				<?php 
				if ($YourArticle->getPublished() == "Y") { 
					putGS('Published'); 
				} 
				elseif ($YourArticle->getPublished() == 'S') { 
					putGS('Submitted'); 
				} 
				elseif ($YourArticle->getPublished() == "N") { 
					putGS('New'); 
				} 
				?>
			</TD>
			
			<td>
				<?php p(htmlspecialchars($pub->getName())); ?>
			</td>
			
			<td>
				<?php p(htmlspecialchars($issue->getName())); ?>
			</td>
			
			<td>
				<?php p(htmlspecialchars($section->getName())); ?>
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
		<?php if ($User->hasPermission('ChangeArticle') || $User->hasPermission('Publish')) { ?>
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" id="submitted_articles" <?php if ($f_screen != "submitted_articles") { echo 'style="display:none;"'; } ?>>
		<TR class="table_list_header">
			<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Submitted articles"); ?></TD>
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
			<TD colspan="2" class="list_row_odd"><?php putGS("There are currently no submitted articles."); ?></td>
	        </tr>
	        <?php
	    }
	    
		foreach ($SubmittedArticles as $SubmittedArticle) {
			$section = $SubmittedArticle->getSection();
			$language =& new Language($SubmittedArticle->getLanguageId());
			$pub =& new Publication($SubmittedArticle->getPublicationId());
			$issue =& new Issue($SubmittedArticle->getPublicationId(), 
								$SubmittedArticle->getLanguageId(), 
								$SubmittedArticle->getIssueNumber());
			$section =& new Section($SubmittedArticle->getPublicationId(), 
									$SubmittedArticle->getIssueNumber(), 
									$SubmittedArticle->getLanguageId(), 
									$SubmittedArticle->getSectionNumber());
			$creator =& new User($SubmittedArticle->getCreatorId());
			?>	
		<TR <?php if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
			<TD valign="top">
			<?php echo camp_html_article_link($SubmittedArticle, $section->getLanguageId(), "edit.php"); ?>
			<?php 
			p(htmlspecialchars($SubmittedArticle->getTitle())); 
			p(" (".htmlspecialchars($language->getNativeName()).")");
			?>
			</A>
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
				<?php p(htmlspecialchars($SubmittedArticle->getType())); ?>
			</td>

			<td align="center" valign="top">
				<?php p(htmlspecialchars($creator->getRealName())); ?>
			</td>

			<td align="center" valign="top">
				<?php p(htmlspecialchars($SubmittedArticle->getCreationDate())); ?>
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
		} // if ($User->hasPermission('ChangeArticle') || $User->hasPermission('Publish'))
		?>

		<!-- Recently Published -->
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" id="recently_published_articles" <?php if ($f_screen != "recently_published_articles") { echo 'style="display:none;"'; } ?>>
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
			<TD colspan="2" class="list_row_odd"><?php putGS("No articles have been published yet."); ?></td>
	        </tr>
		    <?php		    
		}
		$color = 0;
		foreach ($recentlyPublishedArticles as $tmpArticle) {
			$language =& new Language($tmpArticle->getLanguageId());
			$pub =& new Publication($tmpArticle->getPublicationId());
			$issue =& new Issue($tmpArticle->getPublicationId(), 
								$tmpArticle->getLanguageId(), 
								$tmpArticle->getIssueNumber());
			$section =& new Section($tmpArticle->getPublicationId(), 
									$tmpArticle->getIssueNumber(), 
									$tmpArticle->getLanguageId(), 
									$tmpArticle->getSectionNumber());
			 ?>
		<TR <?php if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
			<TD valign="top">
				<?php 
				if ($User->hasPermission('ChangeArticle')) {
    				echo camp_html_article_link($tmpArticle, $tmpArticle->getLanguageId(), "edit.php"); 
				}
				p(htmlspecialchars($tmpArticle->getTitle(). " (".$language->getNativeName().")"));
				if ($User->hasPermission('ChangeArticle')) {
    				echo '</a>';
				}
				?>
			</TD>
			<td nowrap valign="top"><?php echo $tmpArticle->getPublishDate(); ?></td>
			
			<td>
				<?php p(htmlspecialchars($pub->getName())); ?>
			</td>

			<td>
				<?php p(htmlspecialchars($issue->getName())); ?>
			</td>
			
			<td>
				<?php p(htmlspecialchars($section->getName())); ?>
			</td>
			
			<td align="center" valign="top">
				<?php p(htmlspecialchars($tmpArticle->onFrontPage() ? getGS("Yes") : getGS("No"))); ?>
			</td>

			<td align="center" valign="top">
				<?php p(htmlspecialchars($tmpArticle->onSectionPage() ? getGS("Yes") : getGS("No"))); ?>
			</td>			
        </tr>
		<?php 
		} // for
    	?>
        </table>
		
        <!-- Scheduled Publishing -->
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" id="scheduled_actions" <?php if ($f_screen != "scheduled_actions") { echo 'style="display:none;"'; } ?>>
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
			 ?>
		<TR <?php if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<?PHP
		if ($action["ObjectType"] == "article") { 
			$language =& new Language($action["IdLanguage"]);
			$pub =& new Publication($action["IdPublication"]);
			$issue =& new Issue($action["IdPublication"], 
								$action["IdLanguage"], 
								$action["NrIssue"]);
			$section =& new Section($action["IdPublication"], 
									$action["NrIssue"], 
									$action["IdLanguage"], 
									$action["NrSection"]);
			?>
			<TD valign="top"><?php putGS("Article"); ?>: 
    			<?PHP
				if ($User->hasPermission('ChangeArticle')) { ?>
                    <a href="/<?php p($ADMIN); ?>/articles/edit.php?f_publication_id=<?php p($action["IdPublication"]); ?>&f_issue_number=<?php p($action["NrIssue"]); ?>&f_section_number=<?php p($action["NrSection"]); ?>&f_article_number=<?php p($action["Number"]); ?>&f_language_id=<?php p($action["IdLanguage"]); ?>&f_language_selected=<?php p($action["IdLanguage"]); ?>">
                	<?PHP
				}
			    echo htmlspecialchars($action["Name"]." (".$language->getNativeName().")"); 
				if ($User->hasPermission('ChangeArticle')) { 
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
			//$language =& new Language($action["IdLanguage"]);
			$pub =& new Publication($action["IdPublication"]);			
			?>
			<TD valign="top"><?php putGS("Issue"); ?>: 
    			<?PHP
				if ($User->hasPermission('ManageIssue')) { ?>
                    <a href="/<?php p($ADMIN); ?>/issues/edit.php?Pub=<?php p($action["IdPublication"]); ?>&Issue=<?php p($action["Number"]); ?>&Language=<?php p($action["IdLanguage"]); ?>">
                    <?PHP
				}
				echo htmlspecialchars($action["Name"]); 
				if ($User->hasPermission('ManageIssue')) { 				
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
                if ($User->hasPermission("Publish")) { ?>
                    <a href="/<?php p($ADMIN); ?>/issues/autopublish.php?Pub=<?php p($action["IdPublication"]); ?>&Issue=<?php p($action["Number"]); ?>&Language=<?php p($action["IdLanguage"]); ?>&event_id=<?php p(urlencode($action["id"])); ?>">
                    <?PHP
                }
                echo htmlspecialchars($action["time_action"]); 
                if ($User->hasPermission("Publish")) {
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
	</td>
</tr>
</table>


<?php //camp_html_copyright_notice(); ?>