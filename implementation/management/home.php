<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Article.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Section.php");
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
todefnum('NArtOffs');
if ($NArtOffs<0) {
	$NArtOffs=0;
}
todefnum('ArtOffs');
if ($ArtOffs < 0) {
	$ArtOffs=0; 
}
$NumDisplayArticles=15;
list($YourArticles, $NumYourArticles) = Article::GetArticlesByUser($User->getId(), $ArtOffs, 
	$NumDisplayArticles);

list($SubmittedArticles, $NumSubmittedArticles) = Article::GetSubmittedArticles($NArtOffs, $NumDisplayArticles);
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
<!--<HR NOSHADE SIZE="1" COLOR="BLACK">-->

<TABLE BORDER="0" CELLSPACING="4" CELLPADDING="2" WIDTH="100%">
<TR>
    <TD VALIGN="TOP">
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		
		<?php  if ($User->hasPermission("AddArticle")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="pub/add_article.php?Back=/<?php echo $ADMIN; ?>/home.php"><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new article"); ?>"></A></TD><TD NOWRAP><A HREF="pub/add_article.php?Back=/<?php echo $ADMIN; ?>/home.php"><?php  putGS("Add new article"); ?></A></TD>
		</TR>
		<?php  } ?>
		
		<?php  if ($User->hasPermission("ManagePub")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="pub/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new publication"); ?>"></A></TD><TD NOWRAP><A HREF="pub/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new publication"); ?></A></TD>
		</TR>
		<?php  } ?>
		
		<?php  if ($User->hasPermission("ManageTempl")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="templates/upload_templ.php?Path=/look/&Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Upload new template"); ?>"></A></TD><TD NOWRAP><A HREF="templates/upload_templ.php?Path=/look/&Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Upload new template"); ?></A></TD>
		</TR>
		<?php  } ?>
		
		<?php  if ($User->hasPermission("ManageUsers")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="users/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new user account"); ?>"></A></TD><TD NOWRAP><A HREF="users/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new user account"); ?></A></TD>
		</TR>
		<?php  } ?>
		
		<?php  if ($User->hasPermission("ManageUserTypes")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="u_types/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new user type"); ?>"></A></TD><TD NOWRAP><A HREF="u_types/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new user type"); ?></A></TD>
		</TR>
		<?php  } ?>
		
		<?php  if ($User->hasPermission("ManageArticleTypes")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="a_types/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new article type"); ?>"></A></TD><TD NOWRAP><A HREF="a_types/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new article type"); ?></A></TD>
		</TR>
		<?php  } ?>
		
		<?php  if ($User->hasPermission("ManageCountries")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="country/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new country"); ?>"></A></TD><TD NOWRAP><A HREF="country/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new country"); ?></A></TD>
		</TR>
		<?php  } ?>
		
		<?php  if ($User->hasPermission("ManageLanguages")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="languages/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new language"); ?>"></A></TD><TD NOWRAP><A HREF="languages/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new language"); ?></A></TD>
		</TR>
		<?php  } ?>
		
		<?php  if ($User->hasPermission("ViewLogs")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="logs/"><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("View logs"); ?>"></A></TD><TD NOWRAP><A HREF="logs/"><?php  putGS("View logs"); ?></A></TD>
		</TR>
		<?php  } ?>	
		
		<TR>
			<TD ALIGN="RIGHT"><A HREF="users/chpwd.php"><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Change your password"); ?>"></A></TD><TD NOWRAP><A HREF="users/chpwd.php"><?php  putGS("Change your password"); ?></A></TD>
		</TR>
		</TABLE>
	</TD>
	
	<TD VALIGN="TOP" align="right">
		<?php  if ($What) { ?>

		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3">
		<TR>
			<TD colspan="3" style="font-weight: bold; font-size: 12pt;">
				<?php  putGS("Your articles"); ?>
			</TD>
		</TR>
		<TR BGCOLOR="#C0D0FF">
			<TD ALIGN="LEFT" VALIGN="TOP" width="450px"><B><?php  putGS("Name<BR><SMALL>(click to edit article)</SMALL>"); ?></B></TD>
			<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="100px" ><B><?php  putGS("Language"); ?></B></TD>
			<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="100px" ><B><?php  putGS("Status"); ?></B></TD>
		</TR>

		<?php 
		$color = 0;
		foreach ($YourArticles as $YourArticle) {
			$section =& $YourArticle->getSection();
			$language =& new Language($YourArticle->getLanguageId());
			 ?>
		<TR <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php  } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php  } ?>>
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
			
			<TD>
				<?php p(htmlspecialchars($language->getName())); ?>
			</TD>
			
			<TD>
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
				if ($ArtOffs<=0) { 
					p(htmlspecialchars("<< ")); 
					putGS('Previous'); 
				} 
				else { ?>
					<B><A HREF="home.php?ArtOffs=<?php print ($ArtOffs - $NumDisplayArticles); ?>&What=1"><?php p(htmlspecialchars("<< ")); putGS('Previous'); ?></A></B>
					<?php  
				} 
				if ( ($ArtOffs + $NumDisplayArticles) >= $NumYourArticles ) { 
					?>
					| <?php putGS('Next'); p(htmlspecialchars(" >>"));
				} 
				else { ?>
					| <B><A HREF="home.php?ArtOffs=<?php print ($ArtOffs + $NumDisplayArticles); ?>&What=1"><?php putGS('Next'); p(htmlspecialchars(" >>")); ?></A></B>
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
			<td colspan="2" style="font-weight: bold; font-size: 12pt;">
				<?php putGS("Submitted articles"); ?>
			</td>
		</tr>
		
		<TR BGCOLOR="#C0D0FF">
			<TD ALIGN="LEFT" VALIGN="TOP" width="550px"><B><?php  putGS("Name<BR><SMALL>(click to edit article)</SMALL>"); ?></B></TD>
			<TD ALIGN="LEFT" VALIGN="TOP" width="100px"><B><?php  putGS("Language"); ?></B></TD>
		</TR>
		<?php 
	    $color=0;
		foreach ($SubmittedArticles as $SubmittedArticle) {
			$section =& $SubmittedArticle->getSection();
			$language =& new Language($SubmittedArticle->getLanguageId());
			?>	
		<TR <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php  } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php  } ?>>
			<TD width="550px">
			<?php echo CampsiteInterface::ArticleLink($SubmittedArticle, $section->getLanguageId(), "edit.php"); ?>
			<?php p(htmlspecialchars($SubmittedArticle->getTitle())); ?>
			</A>
			</TD>
			
			<TD>
			<?php p(htmlspecialchars($language->getName()));?>
			</TD>
		</TR>
		<?php 
		} // for ($SubmittedArticles ...)
		?>	

		<TR>
			<TD COLSPAN="2" NOWRAP>
			<?php 
			if ($NArtOffs <= 0) { 
				p(htmlspecialchars("<< "));  
				putGS('Previous'); 
			} 
			else { ?>
				<B><A HREF="home.php?NArtOffs=<?php print ($NArtOffs - $NumDisplayArticles); ?>&What=0"><?php p(htmlspecialchars("<< ")); putGS('Previous'); ?></A></B>
				<?php  
    		}
    		if (($NArtOffs + $NumDisplayArticles) >= $NumSubmittedArticles) { ?>
    	 		| <?php  putGS('Next'); p(htmlspecialchars(" >>")); 
    		} 
    		else { ?>
    			| <B><A HREF="home.php?NArtOffs=<?php  print ($NArtOffs + $NumDisplayArticles); ?>&What=0"><?php putGS('Next'); p(htmlspecialchars(" >>")); ?></A></B>
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
					<TD ALIGN="RIGHT"><A HREF="home.php?What=0"><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Submitted articles"); ?>"></A></TD>
					<TD NOWRAP><A HREF="home.php?What=0"><?php  putGS("Submitted articles"); ?></A></TD>
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
					<TD ALIGN="RIGHT"><A HREF="home.php?What=1"><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Your articles"); ?>"></A></TD>
					<TD NOWRAP><A HREF="home.php?What=1"><?php  putGS("Your articles"); ?></A></TD>
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