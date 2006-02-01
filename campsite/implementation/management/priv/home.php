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
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

load_common_include_files("home");
list($access, $User) = check_basic_access($_REQUEST);	
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$NArtOffs = Input::Get('NArtOffs', 'int', 0, true);
if ($NArtOffs<0) {
	$NArtOffs=0;
}
$ArtOffs = Input::Get('ArtOffs', 'int', 0, true);
if ($ArtOffs < 0) {
	$ArtOffs=0; 
}
$NumDisplayArticles=15;
list($YourArticles, $NumYourArticles) = Article::GetArticlesByUser($User->getUserId(), $ArtOffs, 
	$NumDisplayArticles);

list($SubmittedArticles, $NumSubmittedArticles) = Article::GetSubmittedArticles($NArtOffs, $NumDisplayArticles);

$recentlyPublishedArticles = Article::GetRecentArticles($NumDisplayArticles);

$pendingArticles = ArticlePublish::GetFutureActions($NumDisplayArticles);
$pendingIssues = IssuePublish::GetFutureActions($NumDisplayArticles);
$pendingActions = array_merge($pendingArticles, $pendingIssues);
ksort($pendingActions);
$pendingActions = array_slice($pendingActions, 0, $NumDisplayArticles);
//echo "<pre>";print_r($pendingActions);echo "</pre>";
$crumbs = array();
$crumbs[] = array(getGS("Home"), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);
?>
<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<TITLE><?php  putGS("Home"); ?></TITLE>
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
	<TD VALIGN="TOP" align="right" width="50%">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3">
		<TR class="table_list_header">
			<TD ALIGN="LEFT" VALIGN="TOP" width="98%"><?php  putGS("Your articles"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" WIDTH="1%" ><?php  putGS("Language"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" WIDTH="1%" ><?php  putGS("Status"); ?></TD>
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
			 ?>
		<TR <?php if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
			<TD width="98%" valign="top">
				<?php 
				if ($User->hasPermission('ChangeArticle') || ($YourArticle->getPublished() == 'N')) {
					echo camp_html_article_link($YourArticle, $section->getLanguageId(), "edit.php"); 
				}
				p(htmlspecialchars($YourArticle->getTitle()));
				if ($User->hasPermission('ChangeArticle') || ($YourArticle->getPublished() == 'N')) {
					echo '</a>';
				}
				?>
			</TD>
			
			<TD width="1%" align="center" nowrap valign="top">
				<?php p(htmlspecialchars($language->getNativeName())); ?>
			</TD>
			
			<TD width="1%" align="center" nowrap valign="top">
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
		</TR>
		<?php 
		} // for
    	?>
	
    	<TR>
    		<TD COLSPAN="2" NOWRAP>
				<?php  
				if ($ArtOffs > 0) { ?>
					<B><A HREF="home.php?ArtOffs=<?php print ($ArtOffs - $NumDisplayArticles); ?>&NArtOffs=<?php  p($NArtOffs);?>"><?php p(htmlspecialchars("<< ")); putGS('Previous'); ?></A></B>
					<?php  
				} 
				if ( ($ArtOffs + $NumDisplayArticles) < $NumYourArticles ) { ?>
					| <B><A HREF="home.php?ArtOffs=<?php print ($ArtOffs + $NumDisplayArticles); ?>&NArtOffs=<?php  p($NArtOffs);?>"><?php putGS('Next'); p(htmlspecialchars(" >>")); ?></A></B>
					<?php  
				} 
				?>	
			</TD>
		</TR>
		</TABLE>
	</td>
	
	<td VALIGN="TOP" align="right" width="50%">
		<?php if ($User->hasPermission('ChangeArticle') || $User->hasPermission('Publish')) { ?>
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3">
		<TR class="table_list_header">
			<TD ALIGN="left" VALIGN="TOP" width="99%"><?php  putGS("Submitted articles"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" width="1%"><?php  putGS("Language"); ?></TD>
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
			?>	
		<TR <?php if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
			<TD valign="top">
			<?php echo camp_html_article_link($SubmittedArticle, $section->getLanguageId(), "edit.php"); ?>
			<?php p(htmlspecialchars($SubmittedArticle->getTitle())); ?>
			</A>
			</TD>
			
			<TD align="center" nowrap valign="top">
			<?php p(htmlspecialchars($language->getNativeName()));?>
			</TD>
		</TR>
		<?php 
		} // for ($SubmittedArticles ...)
		?>	

		<TR>
			<TD COLSPAN="2" NOWRAP>
			<?php 
			if ($NArtOffs > 0) { ?>
				<B><A HREF="home.php?NArtOffs=<?php p($NArtOffs - $NumDisplayArticles); ?>"><?php p(htmlspecialchars("<< ")); putGS('Previous'); ?></A></B>
				<?php  
    		}
    		if (($NArtOffs + $NumDisplayArticles) < $NumSubmittedArticles) { ?>
    			| <B><A HREF="home.php?NArtOffs=<?php  p($NArtOffs + $NumDisplayArticles); ?>"><?php putGS('Next'); p(htmlspecialchars(" >>")); ?></A></B>
				<?php  
    		} 
    		?>	
			</TD>
		</TR>
		</TABLE>
		<?php 
		} // if ($User->hasPermission('ChangeArticle') || $User->hasPermission('Publish'))
		?>
    </TD>
</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="4" CELLPADDING="2" WIDTH="100%">
<TR>
	<TD VALIGN="TOP" align="left" width="50%">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3">
		<TR class="table_list_header">
			<TD ALIGN="LEFT" VALIGN="TOP" width="98%"><?php  putGS("Recently Published Articles"); ?></TD>
			<TD ALIGN="LEFT" VALIGN="TOP" width="2%" nowrap><?php  putGS("Publish Date"); ?></TD>
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
			 ?>
		<TR <?php if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
			<TD valign="top">
				<?php 
				if ($User->hasPermission('ChangeArticle')) {
    				echo camp_html_article_link($tmpArticle, $tmpArticle->getLanguageId(), "edit.php"); 
				}
				p(htmlspecialchars($tmpArticle->getTitle()));
				if ($User->hasPermission('ChangeArticle')) {
    				echo '</a>';
				}
				?>
			</TD>
			<td nowrap valign="top"><?php echo $tmpArticle->getPublishDate(); ?></td>
        </tr>
		<?php 
		} // for
    	?>
        </table>
    </td>
    
    <td width="50%" valign="top">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3">
		<TR class="table_list_header">
			<TD ALIGN="LEFT" VALIGN="TOP" width="96%"><?php putGS("Scheduled Publishing"); ?></TD>
			<TD ALIGN="LEFT" VALIGN="TOP" width="2%" nowrap><?php putGS("Event(s)"); ?></TD>
			<TD ALIGN="LEFT" VALIGN="TOP" width="2%" nowrap><?php putGS("Time"); ?></TD>
		</TR>
		<?php 
		if (count($pendingActions) == 0) {
	        ?>
    		<TR>
			<TD colspan="3" class="list_row_odd"><?php putGS("There are no pending items to be published."); ?></td>
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
		if ($action["ObjectType"] == "article") { ?>
			<TD valign="top"><?php putGS("Article"); ?>: 
    			<?PHP
				if ($User->hasPermission('ChangeArticle')) { ?>
                    <a href="/<?php p($ADMIN); ?>/articles/edit.php?f_publication_id=<?php p($action["IdPublication"]); ?>&f_issue_number=<?php p($action["NrIssue"]); ?>&f_section_number=<?php p($action["NrSection"]); ?>&f_article_number=<?php p($action["Number"]); ?>&f_language_id=<?php p($action["IdLanguage"]); ?>&f_language_selected=<?php p($action["IdLanguage"]); ?>">
                	<?PHP
				}
			    echo htmlspecialchars($action["Name"]); 
				if ($User->hasPermission('ChangeArticle')) { 
    				echo "</a>";
                }
                ?>
			</TD>
			<td nowrap valign="top"><?PHP
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
			?></td>
			<td nowrap valign="top">
                <?php echo htmlspecialchars($action["time_action"]); ?>
			</td>
		<?PHP
		}
		elseif ($action["ObjectType"] == "issue") { ?>
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


<?php camp_html_copyright_notice(); ?>