<?php
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");
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
<?php CampsiteInterface::CopyrightNotice(); ?>