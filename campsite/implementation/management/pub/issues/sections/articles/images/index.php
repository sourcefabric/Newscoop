<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files();
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleImage.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Section.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/CampsiteInterface.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header('Location: /priv/logout.php');
	exit;
}
$maxId = Image::GetMaxId();
$Pub = isset($_REQUEST['Pub'])?$_REQUEST['Pub']:0;
$Issue = isset($_REQUEST['Issue'])?$_REQUEST['Issue']:0;
$Section = isset($_REQUEST['Section'])?$_REQUEST['Section']:0;
$Language = isset($_REQUEST['Language'])?$_REQUEST['Language']:0;
$sLanguage = isset($_REQUEST['sLanguage'])?$_REQUEST['sLanguage']:0;
$Article = isset($_REQUEST['Article'])?$_REQUEST['Article']:0;

$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
$languageObj =& new Language($Language);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS('Article Image List'); ?></TITLE>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['website_url'] ?>/css/admin_stylesheet.css">
</HEAD>

<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
		<TD>
		    <DIV STYLE="font-size: 12pt"><B><?php putGS('Article Image List'); ?></B></DIV>
		    <HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR><TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>&Section=<?php p($Section); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS('Articles'); ?>"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>&Section=<?php p($Section); ?>"><B><?php putGS('Articles');  ?></B></A></TD>
<TD><A HREF="/priv/pub/issues/sections/?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php  p($Language); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS('Sections'); ?>"></A></TD><TD><A HREF="/priv/pub/issues/sections/?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>"><B><?php putGS('Sections'); ?></B></A></TD>
<TD><A HREF="/priv/pub/issues/?Pub=<?php p($Pub); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS('Issues'); ?>"></A></TD><TD><A HREF="/priv/pub/issues/?Pub=<?php p($Pub); ?>" ><B><?php putGS('Issues'); ?></B></A></TD>
<TD><A HREF="/priv/pub/" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS('Publications'); ?>"></A></TD><TD><A HREF="/priv/pub/" ><B><?php putGS('Publications'); ?></B></A></TD>
<TD><A HREF="/priv/home.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS('Home'); ?>"></A></TD><TD><A HREF="/priv/home.php" ><B><?php putGS('Home'); ?></B></A></TD>
<TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS('Logout'); ?>"></A></TD><TD><A HREF="/priv/logout.php" ><B><?php  putGS('Logout');  ?></B></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php putGS('Publication'); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php echo htmlspecialchars($publicationObj->getName()); ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php putGS('Issue'); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php echo htmlspecialchars($issueObj->getIssueId()); ?>. <?php echo htmlspecialchars($issueObj->getName()); ?> (<?php echo htmlspecialchars($languageObj->getName()); ?>)</B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php putGS('Section'); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php echo htmlspecialchars($sectionObj->getSectionId()); ?>. <?php echo htmlspecialchars($sectionObj->getName()); ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php putGS('Article'); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php echo htmlspecialchars($articleObj->getTitle()); ?></B></TD>
</TR></TABLE>

<table>
<tr>
	<td>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><A HREF="../edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD>
			<TD><A HREF="../edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><B><?php  putGS('Back to article details'); ?></B></A></TD>
		</TR>
		</TABLE>
	</td>
</tr>
<?php  if ($User->hasPermission('AddImage')) { ?>
<tr>
	<td>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><A HREF="add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD>
			<TD><A HREF="add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><B><?php  putGS('Add new image'); ?></B></A></TD>
		</TR>
		</TABLE>
	</td>
	<td>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><IMG SRC="/priv/img/tol.gif" BORDER="0"></TD>
			<TD><?php echo CampsiteInterface::ArticleLink($articleObj, $Language, "images/search.php"); ?><B><?php  putGS('Add an existing image'); ?></B></A></TD>
		</TR>
		</TABLE>
	</td>
</tr>
<?php  } ?>
</table>

<P>
<?php 
$articleImages = ArticleImage::GetImagesByArticleId($articleObj->getArticleId());
if (count($articleImages) <= 0) {
	?>
	
	<table>
	<tr><td style="padding-top: 10px; padding-bottom: 20px; padding-left: 30px;">
	There are currently no images associated with this article.<br>
	Click one of the "Add Image" links above to add one.
	</td></tr>
	</table>
	<?php
}

if (count($articleImages) > 0) {
	?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%">
	<TR BGCOLOR="#C0D0FF">
		<TD ALIGN="LEFT" VALIGN="TOP" width="1%" nobr><B><?php putGS('Number'); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" width="1%"><B><?php putGS('Thumbnail'); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS('Description'); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS('Photographer'); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS('Place'); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS('Date<BR><SMALL>(yyyy-mm-dd)</SMALL>'); ?></B></TD>
	<?php  if ($User->hasPermission('ChangeImage')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS('Edit'); ?></B></TD>
	<?php  }
	    
	if ($User->hasPermission('ChangeArticle')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php putGS('Remove Image From Article'); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php putGS('Delete'); ?></B></TD>
		<?php  
	} 
	?>
	</TR>
<?php 
	$imageCount = 0;
	foreach ($articleImages as $articleImage) {
		$image =& $articleImage->getImage();
	?>	
	<TR <?php  if (($imageCount%2)==0) { ?>BGCOLOR="#D0D0B0"<?php  } else { ?>BGCOLOR="#D0D0D0"<?php  } ?>>
		<TD ALIGN="center"><?php echo $articleImage->getTemplateId(); ?></td>
		<TD ALIGN="center">
			<a href="<?php echo CampsiteInterface::ArticleUrl($articleObj, $Language, "images/view.php") . "&ImageId=".$image->getImageId(); ?>">
			<?php if (file_exists($image->getThumbnailStorageLocation())) { ?>
				<img src="<?php echo $image->getThumbnailUrl(); ?>" border="0">
			<?php } else { ?>
				<img src="<?php echo $image->getImageUrl(); ?>" width="64" height="64" border="0">
			<?php } ?>				
			</a>
		</TD>
		<TD >
			<a href="<?php echo CampsiteInterface::ArticleUrl($articleObj, $Language, "images/view.php") .'&ImageId='.$image->getImageId(); ?>"><?php echo htmlspecialchars($image->getDescription()); ?></A>
		</TD>
		<TD >
			<?php echo htmlspecialchars($image->getPhotographer()); ?>&nbsp;
		</TD>
		<TD >
			<?php echo htmlspecialchars($image->getPlace()); ?>&nbsp;
		</TD>
		<TD >
			<?php echo htmlspecialchars($image->getDate()); ?>
		</TD>
	<?php  if ($User->hasPermission('ChangeImage')) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/priv/pub/issues/sections/articles/images/edit.php?PublicationId=<?php  p($Pub); ?>&IssueId=<?php  p($Issue); ?>&SectionId=<?php  p($Section); ?>&ArticleId=<?php  p($Article); ?>&ImageId=<?php echo $image->getImageId(); ?>&InterfaceLanguageId=<?php  p($Language); ?>&ArticleLanguageId=<?php  p($sLanguage); ?>&ImageTemplateId=<?php p($articleImage->getTemplateId()); ?>"><?php  putGS('Change');?></A>
		</TD>
	<?php  }
	    if ($User->hasPermission('ChangeArticle')) { ?>
			<TD ALIGN="CENTER">
				<A HREF="/priv/pub/issues/sections/articles/images/do_unlink.php?PublicationId=<?php  p($Pub); ?>&IssueId=<?php  p($Issue); ?>&SectionId=<?php  p($Section); ?>&ArticleId=<?php  p($Article); ?>&ImageId=<?php echo $image->getImageId(); ?>&ImageTemplateId=<?php echo $articleImage->getTemplateId(); ?>&ArticleLanguageId=<?php  p($Language); ?>&InterfaceLanguageId=<?php  p($sLanguage); ?>" onclick="return confirm('<?php putGS('Are you sure you want to remove the image \\\'$1\\\' from the article?', htmlspecialchars($image->getDescription())); ?>');"><IMG SRC="/priv/img/icon/unlink.gif" BORDER="0" ALT="<?php  putGS('Unlink image $1', $image->getDescription()); ?>"></A>
			</TD>
			<td align="center">
			<?php 
			if (count(ArticleImage::GetArticlesThatUseImage($image->getImageId())) == 1) {
				?>
				<A HREF="/priv/pub/issues/sections/articles/images/do_del.php?PublicationId=<?php  p($Pub); ?>&IssueId=<?php  p($Issue); ?>&SectionId=<?php  p($Section); ?>&ArticleId=<?php  p($Article); ?>&ImageId=<?php echo $image->getImageId(); ?>&ArticleLanguageId=<?php  p($sLanguage); ?>&InterfaceLanguageId=<?php p($Language); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the image \\\'$1\\\'?', htmlspecialchars($image->getDescription())); ?>');"><IMG SRC="/priv/img/icon/delete.gif" BORDER="0" ALT="<?php  putGS('Delete image $1', $image->getDescription()); ?>"></A>
				<?php
			}
			else {
				?>
				&nbsp;
				<?php	
			}
			?>
			</td>
			<?
	    } ?>
	</TR>
<?php 
		$imageCount++;
	} // foreach
} // if (count($articleImages) > 0)
?>	
</TABLE>

<?php CampsiteInterface::CopyrightNotice(); ?>