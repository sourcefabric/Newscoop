<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Article.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Section.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");
require_once($_SERVER['DOCUMENT_ROOT']."/priv/CampsiteInterface.php");

load_common_include_files();
todefnum('TOL_UserId');
todefnum('TOL_UserKey');
list($access, $User) = check_basic_access($_REQUEST);	
if (!$access) {
	header("Location: /priv/logout.php");
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
$NumDisplayArticles=20;
$YourArticles = Article::GetArticlesByUser($User->getId(), $ArtOffs, 
	$NumDisplayArticles+1);
$NumYourArticles = count($YourArticles);

$SubmittedArticles = Article::GetSubmittedArticles($NArtOffs, $NumDisplayArticles+1);
$NumSubmittedArticles = count($SubmittedArticles);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite["website_url"] ?>/css/admin_stylesheet.css">
	<script>
	<!--
	/*
	A slightly modified version of "Break-out-of-frames script"
	By JavaScript Kit (http://javascriptkit.com)
	*/
	
	if (window != top.fmain && window != top) {
		if (top.fmenu)
			top.fmain.location.href=location.href
		else
			top.location.href=location.href
	}
	// -->
	</script>
	<TITLE><?php  putGS("Home"); ?></TITLE>
</HEAD>
<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
<TR>
	<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
	<TD>
	    <DIV STYLE="font-size: 12pt"><B><?php  putGS("Home"); ?></B></DIV>
	    <HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD><TD><A HREF="/priv/logout.php" ><B><?php  putGS("Logout");  ?></B></A></TD></TR></TABLE></TD>
</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="4" CELLPADDING="2" WIDTH="100%">
<TR>
	<TD COLSPAN="2" BGCOLOR=#D0D0B0><?php  putGS('Welcome $1!','<B>'.htmlspecialchars($User->getName()).'</B>'); ?></TD>
</TR>
<TR>
    <TD VALIGN="TOP">
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		
		<?php  if ($User->hasPermission("AddArticle")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="pub/add_article.php"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new article"); ?>"></A></TD><TD NOWRAP><A HREF="pub/add_article.php"><?php  putGS("Add new article"); ?></A></TD>
		</TR>
		<?php  } ?>
		
		<?php  if ($User->hasPermission("ManagePub")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="pub/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new publication"); ?>"></A></TD><TD NOWRAP><A HREF="pub/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new publication"); ?></A></TD>
		</TR>
		<?php  } ?>
		
		<?php  if ($User->hasPermission("ManageTempl")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="templates/upload_templ.php?Path=/look/&Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Upload new template"); ?>"></A></TD><TD NOWRAP><A HREF="templates/upload_templ.php?Path=/look/&Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Upload new template"); ?></A></TD>
		</TR>
		<?php  } ?>
		
		<?php  if ($User->hasPermission("ManageUsers")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="users/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new user account"); ?>"></A></TD><TD NOWRAP><A HREF="users/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new user account"); ?></A></TD>
		</TR>
		<?php  } ?>
		
		<?php  if ($User->hasPermission("ManageUserTypes")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="u_types/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new user type"); ?>"></A></TD><TD NOWRAP><A HREF="u_types/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new user type"); ?></A></TD>
		</TR>
		<?php  } ?>
		
		<?php  if ($User->hasPermission("ManageArticleTypes")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="a_types/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new article type"); ?>"></A></TD><TD NOWRAP><A HREF="a_types/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new article type"); ?></A></TD>
		</TR>
		<?php  } ?>
		
		<?php  if ($User->hasPermission("ManageCountries")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="country/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new country"); ?>"></A></TD><TD NOWRAP><A HREF="country/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new country"); ?></A></TD>
		</TR>
		<?php  } ?>
		
		<?php  if ($User->hasPermission("ManageLanguages")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="languages/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new language"); ?>"></A></TD><TD NOWRAP><A HREF="languages/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new language"); ?></A></TD>
		</TR>
		<?php  } ?>
		
		<?php  if ($User->hasPermission("ViewLogs")) { ?>	
		<TR>
			<TD ALIGN="RIGHT"><A HREF="logs/"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("View logs"); ?>"></A></TD><TD NOWRAP><A HREF="logs/"><?php  putGS("View logs"); ?></A></TD>
		</TR>
		<?php  } ?>	
		
		<TR>
			<TD ALIGN="RIGHT"><A HREF="users/chpwd.php"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Change your password"); ?>"></A></TD><TD NOWRAP><A HREF="users/chpwd.php"><?php  putGS("Change your password"); ?></A></TD>
		</TR>
		</TABLE>
	</TD>
	
	<TD VALIGN="TOP">
		<?php  if ($What) { ?>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><IMG SRC="/priv/img/tol.gif" BORDER="0"></TD>
			<TD><?php  putGS("Your articles"); ?></TD>
		</TR>
		</TABLE>

		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
		<TR BGCOLOR="#C0D0FF">
			<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Name<BR><SMALL>(click to edit article)</SMALL>"); ?></B></TD>
			<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="10%" ><B><?php  putGS("Language"); ?></B></TD>
			<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="10%" ><B><?php  putGS("Status"); ?></B></TD>
		</TR>

		<?php 
		foreach ($YourArticles as $YourArticle) {
			$section =& $YourArticle->getSection();
			$language =& new Language($YourArticle->getLanguageId());
			 ?>
		<TR <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php  } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php  } ?>>
			<TD>
				<?php echo CampsiteInterface::ArticleLink($YourArticle, $section->getLanguageId(), "edit.php"); ?>
				<?php p(htmlspecialchars($YourArticle->getTitle()));?>
				</A>
			</TD>
			
			<TD>
				<?php p(htmlspecialchars($language->getName())); ?>
			</TD>
			
			<TD>
				<?php 
				if ($YourArticle->getPublished() == "Y") { ?>
					<?php echo CampsiteInterface::ArticleLink($YourArticle, $section->getLanguageId(), "status.php", $REQUEST_URI);?>
					<?php  putGS('Published'); ?>
					</A>
					<?php  
				} 
				elseif ($YourArticle->getPublished() == "N") { ?>
					<?php echo CampsiteInterface::ArticleLink($YourArticle, $section->getLanguageId(), "status.php", $REQUEST_URI); ?>
					<?php  putGS('New'); ?>
					</A><?php  
				} 
				else { ?>
					<?php echo CampsiteInterface::ArticleLink($YourArticle, $section->getLanguageId(), "status.php", $REQUEST_URI); ?>
					<?php  putGS('Submitted'); ?>
					</A>
					<?php  
				} ?>		
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
				if ( $NumYourArticles < ($NumDisplayArticles+1) ) { 
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
		else { ?>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><IMG SRC="/priv/img/tol.gif" BORDER="0"></TD>
			<TD><?php putGS("Submitted articles"); ?></TD>
		</TR>
		</TABLE>
		<?php 
		    $color=0;
		?>
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
		<TR BGCOLOR="#C0D0FF">
			<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Name<BR><SMALL>(click to edit article)</SMALL>"); ?></B></TD>
			<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="10%" ><B><?php  putGS("Language"); ?></B></TD>
		</TR>
		<?php 
		foreach ($SubmittedArticles as $SubmittedArticle) {
			$section =& $SubmittedArticle->getSection();
			$language =& new Language($SubmittedArticle->getLanguageId());
			?>	
		<TR <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php  } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php  } ?>>
			<TD>
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
				<B><A HREF="home.php?NArtOffs=<?php print ($NArtOffs - $NumDisplayArticles); ?>&What=0"><?php htmlspecialchars("<< "); putGS('Previous'); ?></A></B>
				<?php  
    		}
    		if ($NumSubmittedArticles < $NumDisplayArticles+1) { ?>
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
					<TD ALIGN="RIGHT"><A HREF="home.php?What=0"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Submitted articles"); ?>"></A></TD>
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
					<TD ALIGN="RIGHT"><A HREF="home.php?What=1"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Your articles"); ?>"></A></TD>
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