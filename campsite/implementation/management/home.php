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
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");

load_common_include_files("$ADMIN_DIR");
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
list($YourArticles, $NumYourArticles) = Article::GetArticlesByUser($User->getId(), $ArtOffs, 
	$NumDisplayArticles);

list($SubmittedArticles, $NumSubmittedArticles) = Article::GetSubmittedArticles($NArtOffs, $NumDisplayArticles);

$recentlyPublishedArticles = Article::GetRecentArticles($NumDisplayArticles);

$pendingArticles = ArticlePublish::GetFutureActions($NumDisplayArticles);
$pendingIssues = IssuePublish::GetFutureActions($NumDisplayArticles);
$pendingActions = array_merge($pendingArticles, $pendingIssues);
ksort($pendingActions);
$pendingActions = array_slice($pendingActions, 0, $NumDisplayArticles);
//echo "<pre>";print_r($pendingActions);echo "</pre>";
?>
<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<TITLE><?php  putGS("Home"); ?></TITLE>
</HEAD>
<BODY>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
<TR>
	<TD class="page_title" width="1%">
	    <?php  putGS("Home"); ?>
	</TD>
</TR>
</TABLE>

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
			$section =& $YourArticle->getSection();
			$language =& new Language($YourArticle->getLanguageId());
			 ?>
		<TR <?php if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
			<TD width="98%" valign="top">
				<?php 
				if ($User->hasPermission('ChangeArticle') || ($YourArticle->getPublished() == 'N')) {
					echo CampsiteInterface::ArticleLink($YourArticle, $section->getLanguageId(), "edit.php"); 
				}
				p(htmlspecialchars($YourArticle->getTitle()));
				if ($User->hasPermission('ChangeArticle') || ($YourArticle->getPublished() == 'N')) {
					echo '</a>';
				}
				?>
			</TD>
			
			<TD width="1%" align="center" nowrap valign="top">
				<?php p(htmlspecialchars($language->getName())); ?>
			</TD>
			
			<TD width="1%" align="center" nowrap valign="top">
				<?php 
				$changeStatusLink = CampsiteInterface::ArticleLink($YourArticle, $section->getLanguageId(), "status.php", $_SERVER['REQUEST_URI']);
				if ($YourArticle->getPublished() == "Y") { 
					if ($User->hasPermission('Publish')) {
						echo $changeStatusLink;
					}
					putGS('Published'); 
					if ($User->hasPermission('Publish')) {
						echo '</a>';
					}
				} 
				elseif ($YourArticle->getPublished() == 'S') { 
					if ($User->hasPermission('Publish')) {
						echo $changeStatusLink; 
					}
					putGS('Submitted'); 
					if ($User->hasPermission('Publish')) {
						echo '</a>';
					}
				} 
				elseif ($YourArticle->getPublished() == "N") { 
					echo $changeStatusLink;
					putGS('New'); 
					echo '</A>';
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
			$section =& $SubmittedArticle->getSection();
			$language =& new Language($SubmittedArticle->getLanguageId());
			?>	
		<TR <?php if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
			<TD valign="top">
			<?php echo CampsiteInterface::ArticleLink($SubmittedArticle, $section->getLanguageId(), "edit.php"); ?>
			<?php p(htmlspecialchars($SubmittedArticle->getTitle())); ?>
			</A>
			</TD>
			
			<TD align="center" nowrap valign="top">
			<?php p(htmlspecialchars($language->getName()));?>
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
    				echo CampsiteInterface::ArticleLink($tmpArticle, $tmpArticle->getLanguageId(), "edit.php"); 
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
                	<a href="/<?php p($ADMIN); ?>/pub/issues/sections/articles/edit.php?Pub=<?php p($action["IdPublication"]); ?>&Issue=<?php p($action["NrIssue"]); ?>&Section=<?php p($action["NrSection"]); ?>&Article=<?php p($action["Number"]); ?>&Language=<?php p($action["IdLanguage"]); ?>&sLanguage=<?php p($action["IdLanguage"]); ?>">
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
			if ($action["Publish"] == 'P') {
			    $displayActions[] = getGS("Publish");
			}
			if ($action["Publish"] == 'U') {
			    $displayActions[] = getGS("Unpublish");
			}
			if ($action["FrontPage"] == 'S') {
			    $displayActions[] = getGS("Show on front page");
			}
			if ($action["FrontPage"] == 'R') {
			    $displayActions[] = getGS("Remove from front page");
			}
			if ($action["SectionPage"] == 'S') {
			    $displayActions[] = getGS("Show on section page");
			}
			if ($action["SectionPage"] == 'R') {
			    $displayActions[] = getGS("Remove from section page");
			}
			echo implode("<br>", $displayActions)
			?></td>
			<td nowrap valign="top">
                <?php 
                if ($User->hasPermission("Publish")) { ?>
                    <a href="/<?php p($ADMIN); ?>/pub/issues/sections/articles/autopublish.php?Pub=<?php p($action["IdPublication"]); ?>&Issue=<?php p($action["NrIssue"]); ?>&Section=<?php p($action["NrSection"]); ?>&Article=<?php p($action["Number"]); ?>&Language=<?php p($action["IdLanguage"]); ?>&sLanguage=<?php p($action["IdLanguage"]); ?>&publish_time=<?php p(urlencode($action["ActionTime"])); ?>">
                    <?PHP
                }
                echo htmlspecialchars($action["ActionTime"]); 
                if ($User->hasPermission("Publish")) {
                    echo "</a>";
                }
                ?>
			</td>
		<?PHP
		}
		elseif ($action["ObjectType"] == "issue") { ?>
			<TD valign="top"><?php putGS("Issue"); ?>: 
    			<?PHP
				if ($User->hasPermission('ManageIssue')) { ?>
                    <a href="/<?php p($ADMIN); ?>/pub/issues/edit.php?Pub=<?php p($action["IdPublication"]); ?>&Issue=<?php p($action["Number"]); ?>&Language=<?php p($action["IdLanguage"]); ?>">
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
			if ($action["Action"] == 'P') {
			    $displayActions[] = getGS("Publish");
			}
			if ($action["Action"] == 'U') {
			    $displayActions[] = getGS("Unpublish");
			}
			if ($action["PublishArticles"] == 'Y') {
			    $displayActions[] = getGS("Publish articles");
			}
			echo implode("<br>", $displayActions)
			?></td>
			<td nowrap valign="top">
                <?php 
                if ($User->hasPermission("Publish")) { ?>
                    <a href="/<?php p($ADMIN); ?>/pub/issues/autopublish.php?Pub=<?php p($action["IdPublication"]); ?>&Issue=<?php p($action["Number"]); ?>&Language=<?php p($action["IdLanguage"]); ?>&publish_time=<?php p(urlencode($action["ActionTime"])); ?>">
                    <?PHP
                }
                echo htmlspecialchars($action["ActionTime"]); 
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


<?php CampsiteInterface::CopyrightNotice(); ?>