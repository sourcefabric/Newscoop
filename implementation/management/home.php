<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Input.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Publication.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Issue.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Section.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Article.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");

load_common_include_files("$ADMIN_DIR");
list($access, $User) = check_basic_access($_REQUEST);	
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

// "What" means "what to display".
// A value of "1" means "display Your Articles".
// A value of "0" means "display Submitted Articles".
if ($User->hasPermission("ChangeArticle")) {
	todefnum('What',0);
}
else {
	todefnum('What',1);
}
$NArtOffs = Input::get('NArtOffs', 'int', 0, true);
if ($NArtOffs<0) {
	$NArtOffs=0;
}
$ArtOffs = Input::get('ArtOffs', 'int', 0, true);
if ($ArtOffs < 0) {
	$ArtOffs=0; 
}
$showSections = Input::get('show_sections', 'int', 1, true);

$NumDisplayArticles=15;
list($YourArticles, $NumYourArticles) = Article::GetArticlesByUser($User->getId(), $ArtOffs, 
	$NumDisplayArticles);

list($SubmittedArticles, $NumSubmittedArticles) = Article::GetSubmittedArticles($NArtOffs, $NumDisplayArticles);

$publications =& Publication::GetAllPublications();
$issues = array();
foreach ($publications as $publication) {
	$issues[$publication->getPublicationId()] =
		Issue::GetIssuesInPublication($publication->getPublicationId(), null, 
			array('LIMIT' => 5, 'ORDER BY' => array('Number' => 'DESC')));
}
$sections = array();
//if ((count($publications) + count($issues)) <= 12) {
if ($showSections) {
	foreach ($publications as $publication) {
		foreach ($issues[$publication->getPublicationId()] as $issue) {
			$sections[$issue->getIssueId()] = 
				Section::GetSectionsInIssue($issue->getPublicationId(), $issue->getIssueId(),
					$issue->getLanguageId());
		}
	}
}
?>
<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite["website_url"] ?>/css/admin_stylesheet.css">
	<TITLE><?php  putGS("Home"); ?></TITLE>
</HEAD>
<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
<TR>
	<TD class="page_title" width="1%">
	    <?php  putGS("Home"); ?>
	</TD>
	<TD style="font-size: 9pt; padding-right: 10px; padding-top: 0px; padding-bottom: 2px;" valign="bottom" align="right"><?php  putGS('Welcome $1!','<B>'.htmlspecialchars($User->getName()).'</B>'); ?></TD>
</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="4" CELLPADDING="2" WIDTH="100%">
<TR>
    <TD VALIGN="TOP" width="40%">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list" width="100%" style="padding: 0px;">
		<tr class="table_list_header">
			<td style="padding-left: 8px;">
				<?php putGS('Publication'); ?> / <?php putGS('Issue'); ?> / <?php putGS('Section'); ?>
				<?php 
				if ($showSections) { ?>
					<a href="home.php?ArtOffs=<?php p($ArtOffs); ?>&NArtOffs=<?php  p($NArtOffs);?>&What=<?php p($What); ?>&show_sections=0" style="font-size: smaller;">(<?php putGS('Hide'); ?>)</a>
					<?php 
				}
				else { ?>
					<a href="home.php?ArtOffs=<?php p($ArtOffs); ?>&NArtOffs=<?php  p($NArtOffs);?>&What=<?php p($What); ?>&show_sections=1" style="font-size: smaller;">(<?php putGS('Show'); ?>)</a>
					<?php 							
				} ?>
			</td>
			<?php if ($showSections) { ?>
			<td align="center" nowrap width="1%">
				<?php putGS('Add article'); ?>
			</td>
			<?php } ?>
		</tr>
		<?php 
		$count = 1;
		foreach ($publications as $publication) { 
			$publicationId = $publication->getPublicationId();
			?>
			<tr <?php if (($count++%2)==1) {?> class="list_row_odd"<?php } else { ?>class="list_row_even"<?php } ?>>
				<td style="padding-left: 8px;"><a href="/<?php echo $ADMIN; ?>/pub/issues/?Pub=<?php p($publicationId); ?>"><?php p(htmlspecialchars($publication->getName())); ?></a></td> <?php if ($showSections) { ?> <td>&nbsp;</td> <?php  } ?>
			</tr>
			<?PHP
			if (isset($issues[$publicationId])) {
				foreach ($issues[$publicationId] as $issue) { ?>
					<tr <?php if (($count++%2)==1) {?> class="list_row_odd"<?php } else { ?>class="list_row_even"<?php } ?>>
						<td style="padding-left: 25px;"><a href="/<?php echo $ADMIN; ?>/pub/issues/sections/?Pub=<?php p($publicationId); ?>&Issue=<?php  p($issue->getIssueId()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><?php p(htmlspecialchars($issue->getName())); ?></a></td><?php if ($showSections) { ?><td>&nbsp;</td><?php } ?>
					</tr>
					<?php 
					if (isset($sections[$issue->getIssueId()])) {
						foreach ($sections[$issue->getIssueId()] as $section) { ?>
							<tr <?php if (($count++%2)==1) {?> class="list_row_odd"<?php } else { ?>class="list_row_even"<?php } ?>>
								<td style="padding-left: 50px;">
									<a href="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<?php p($publicationId); ?>&Issue=<?php  p($issue->getIssueId()); ?>&Section=<?php p($section->getSectionId()); ?>&Language=<?php p($section->getLanguageId()); ?>"><?php p(htmlspecialchars($section->getName())); ?></a>
								</td>
								<td align="center">
									<a href="/<?php p($ADMIN); ?>/pub/issues/sections/articles/add.php?Pub=<?php p($section->getPublicationId());?>&Issue=<?php p($section->getIssueId()); ?>&Section=<?php p($section->getSectionId()); ?>&Language=<?php p($section->getLanguageId()); ?>&Wiz=1"><img src="/<?php p($ADMIN); ?>/img/icon/add_article.png" border="0" align="middle"></a>
								</td>
							</tr>
							<?php
						} // foreach ($sections
					}
				} // foreach ($issues
			} // if (isset($issues[$publicationId]))
		} // foreach ($publications
		?>		
		</table>
	</TD>
	
	<TD VALIGN="TOP" align="right">
		<?php  if ($What) { ?>

		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3">
		<TR>
			<TD colspan="3" style="font-weight: bold; font-size: 10pt; padding-top: 0px">
				<?php  putGS("Your articles"); ?>
			</TD>
		</TR>
		<TR class="table_list_header">
			<TD ALIGN="LEFT" VALIGN="TOP" width="98%"><?php  putGS("Name<BR><SMALL>(click to edit article)</SMALL>"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" WIDTH="1%" ><?php  putGS("Language"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" WIDTH="1%" ><?php  putGS("Status"); ?></TD>
		</TR>

		<?php 
		$color = 0;
		foreach ($YourArticles as $YourArticle) {
			$section =& $YourArticle->getSection();
			$language =& new Language($YourArticle->getLanguageId());
			 ?>
		<TR <?php if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
			<TD width="450px">
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
			
			<TD width="1%" align="center">
				<?php p(htmlspecialchars($language->getName())); ?>
			</TD>
			
			<TD width="1%" align="center">
				<?php 
				$changeStatusLink = CampsiteInterface::ArticleLink($YourArticle, $section->getLanguageId(), "status.php", $REQUEST_URI);
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
					<B><A HREF="home.php?ArtOffs=<?php print ($ArtOffs - $NumDisplayArticles); ?>&What=<?php p($What); ?>&NArtOffs=<?php  p($NArtOffs);?>&show_sections=<?php p($showSections); ?>"><?php p(htmlspecialchars("<< ")); putGS('Previous'); ?></A></B>
					<?php  
				} 
				if ( ($ArtOffs + $NumDisplayArticles) < $NumYourArticles ) { ?>
					| <B><A HREF="home.php?ArtOffs=<?php print ($ArtOffs + $NumDisplayArticles); ?>&NArtOffs=<?php  p($NArtOffs);?>&What=<?php p($What); ?>&show_sections=<?php p($showSections); ?>"><?php putGS('Next'); p(htmlspecialchars(" >>")); ?></A></B>
					<?php  
				} 
				?>	
			</TD>
		</TR>
		</TABLE>
		<?php  
		} // if ($What)
		else { 
			// Submitted articles
			?>
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3">
		<tr>
			<td valign="top" colspan="2" style="font-weight: bold; font-size: 10pt; padding-top: 0px;">
				<?php putGS("Submitted articles"); ?>
			</td>
		</tr>
		
		<TR class="table_list_header">
			<TD ALIGN="left" VALIGN="TOP" width="99%"><?php  putGS("Name<BR><SMALL>(click to edit article)</SMALL>"); ?></TD>
			<TD ALIGN="center" VALIGN="TOP" width="1%"><?php  putGS("Language"); ?></TD>
		</TR>
		<?php 
	    $color=0;
		foreach ($SubmittedArticles as $SubmittedArticle) {
			$section =& $SubmittedArticle->getSection();
			$language =& new Language($SubmittedArticle->getLanguageId());
			?>	
		<TR <?php if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
			<TD>
			<?php echo CampsiteInterface::ArticleLink($SubmittedArticle, $section->getLanguageId(), "edit.php"); ?>
			<?php p(htmlspecialchars($SubmittedArticle->getTitle())); ?>
			</A>
			</TD>
			
			<TD align="center">
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
				<B><A HREF="home.php?NArtOffs=<?php p($NArtOffs - $NumDisplayArticles); ?>&What=<?php p($What); ?>&show_sections=<?php p($showSections); ?>"><?php p(htmlspecialchars("<< ")); putGS('Previous'); ?></A></B>
				<?php  
    		}
    		if (($NArtOffs + $NumDisplayArticles) < $NumSubmittedArticles) { ?>
    			| <B><A HREF="home.php?NArtOffs=<?php  p($NArtOffs + $NumDisplayArticles); ?>&What=<?php p($What); ?>&show_sections=<?php p($showSections); ?>"><?php putGS('Next'); p(htmlspecialchars(" >>")); ?></A></B>
				<?php  
    		} 
    		?>	
			</TD>
		</TR>
		</TABLE>
		<?php  
		} // if (!$What)
		?>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
		<?php 
    	if ($What) {
			if ($User->hasPermission("ChangeArticle")) { ?>	
			<TD>
				<TABLE>
				<TR>
					<TD ALIGN="RIGHT"><A HREF="home.php?ArtOffs=<?php p($ArtOffs); ?>&NArtOffs=<?php  p($NArtOffs);?>&What=0&show_sections=<?php p($showSections); ?>"><IMG SRC="/<?php p($ADMIN); ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Submitted articles"); ?>"></A></TD>
					<TD NOWRAP><A HREF="home.php?ArtOffs=<?php p($ArtOffs); ?>&NArtOffs=<?php  p($NArtOffs);?>&What=0&show_sections=<?php p($showSections); ?>"><?php  putGS("Submitted articles"); ?></A></TD>
				</TR>
				</TABLE>
			</TD>
			<?php  
			} 
    	}    
 		else { ?>	
 			<TD>
 				<TABLE>
				<TR>
					<TD ALIGN="RIGHT"><A HREF="home.php?ArtOffs=<?php p($ArtOffs); ?>&NArtOffs=<?php  p($NArtOffs);?>&What=1&show_sections=<?php p($showSections); ?>"><IMG SRC="/<?php p($ADMIN); ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Your articles"); ?>"></A></TD>
					<TD NOWRAP><A HREF="home.php?ArtOffs=<?php p($ArtOffs); ?>&NArtOffs=<?php  p($NArtOffs);?>&What=1&show_sections=<?php p($showSections); ?>"><?php  putGS("Your articles"); ?></A></TD>
				</TR>
				</TABLE>
			</TD>
			<?php  
 		} 
 		?>
		</TR>
		</TABLE>
    </TD>
</TR>
</TABLE>
<?php CampsiteInterface::CopyrightNotice(); ?>