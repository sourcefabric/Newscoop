<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/pub/issues/sections/articles/images");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleImage.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Section.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
$maxId = Image::GetMaxId();
$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Article = Input::Get('Article', 'int', 0);

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(array('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;	
}

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
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL'] ?>/css/admin_stylesheet.css">
</HEAD>

<BODY>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
<TR>
	<TD class="page_title">
	    <?php putGS('Article Image List'); ?>
	</TD>
	<TD ALIGN="RIGHT">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>&Section=<?php p($Section); ?>" class="breadcrumb"><?php putGS('Articles');  ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>" class="breadcrumb"><?php putGS('Sections'); ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/?Pub=<?php p($Pub); ?>" class="breadcrumb"><?php putGS('Issues'); ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/" class="breadcrumb"><?php putGS('Publications'); ?></A></TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php putGS('Publication'); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php echo htmlspecialchars($publicationObj->getName()); ?></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php putGS('Issue'); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php echo htmlspecialchars($issueObj->getIssueId()); ?>. <?php echo htmlspecialchars($issueObj->getName()); ?> (<?php echo htmlspecialchars($languageObj->getName()); ?>)</TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php putGS('Section'); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php echo htmlspecialchars($sectionObj->getSectionId()); ?>. <?php echo htmlspecialchars($sectionObj->getName()); ?></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php putGS('Article'); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php echo htmlspecialchars($articleObj->getTitle()); ?></TD>
</TR>
</TABLE>

<table>
<tr>
	<td>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><A HREF="../edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/back.png" BORDER="0"></A></TD>
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
			<TD><A HREF="add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/add.png" BORDER="0"></A></TD>
			<TD><A HREF="add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><B><?php  putGS('Add new image'); ?></B></A></TD>
		</TR>
		</TABLE>
	</td>
	<td>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/add.png" BORDER="0"></TD>
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
	<tr>
		<td style="padding-top: 10px; padding-bottom: 20px; padding-left: 30px;">
			<?php putGS('There are currently no images associated with this article.'); ?><br>
			<?php 
			if ($User->hasPermission('AddImage')) {
				putGS('Click one of the "Add Image" links above to add one.');
			}
			?>
	</td></tr>
	</table>
	<?php
}

if (count($articleImages) > 0) {
	?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP" width="1%" nobr><B><?php putGS('Number'); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" width="1%"><B><?php putGS('Thumbnail'); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS('Description'); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS('Photographer'); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS('Place'); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS('Date<BR><SMALL>(yyyy-mm-dd)</SMALL>'); ?></B></TD>
	<?php    
	if ($User->hasPermission('ChangeArticle')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php putGS('Remove Image From Article'); ?></B></TD>
		<?php
	}
	if ($User->hasPermission('DeleteImage')) { ?>
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
	<TR <?php  if (($imageCount%2)==0) { ?>class="list_row_odd"<?php  } else { ?>class="list_row_even"<?php  } ?>>
		<TD ALIGN="center"><?php echo $articleImage->getTemplateId(); ?></td>

		<?php  
		if ($User->hasPermission('ChangeImage')) { 	
			$imageUrl = "/$ADMIN/pub/issues/sections/articles/images/edit.php?"
				."PublicationId=$Pub"
				."&IssueId=$Issue"
				."&SectionId=$Section"
				."&ArticleId=$Article"
				."&ImageId=".$image->getImageId()
				."&InterfaceLanguageId=$Language"
				."&ArticleLanguageId=$sLanguage"
				."&ImageTemplateId=".$articleImage->getTemplateId();
		}
		else {
			$imageUrl = CampsiteInterface::ArticleUrl($articleObj, $Language, "images/view.php", $_SERVER['REQUEST_URI']) .'&ImageId='.$image->getImageId();
		}
		?>
		<TD ALIGN="center">
			<a href="<?php echo $imageUrl; ?>">
			<?php if (file_exists($image->getThumbnailStorageLocation())) { ?>
				<img src="<?php echo $image->getThumbnailUrl(); ?>" border="0">
			<?php } else { ?>
				<img src="<?php echo $image->getImageUrl(); ?>" width="64" height="64" border="0">
			<?php } ?>				
			</a>
		</TD>
		<TD>
			<a href="<?php echo $imageUrl; ?>"><?php echo htmlspecialchars($image->getDescription()); ?></A>
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
		<?php
	    if ($User->hasPermission('ChangeArticle')) { ?>
			<TD ALIGN="CENTER">
				<A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/images/do_unlink.php?PublicationId=<?php  p($Pub); ?>&IssueId=<?php  p($Issue); ?>&SectionId=<?php  p($Section); ?>&ArticleId=<?php  p($Article); ?>&ImageId=<?php echo $image->getImageId(); ?>&ImageTemplateId=<?php echo $articleImage->getTemplateId(); ?>&ArticleLanguageId=<?php  p($Language); ?>&InterfaceLanguageId=<?php  p($sLanguage); ?>" onclick="return confirm('<?php putGS('Are you sure you want to remove the image \\\'$1\\\' from the article?', htmlspecialchars($image->getDescription())); ?>');"><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/unlink.png" BORDER="0" ALT="<?php  putGS('Unlink image $1', $image->getDescription()); ?>"></A>
			</TD>
		<?php
	    }
	    if ($User->hasPermission('DeleteImage')) { ?>
			<td align="center">
			<?php 
			if (count(ArticleImage::GetArticlesThatUseImage($image->getImageId())) == 1) {
				?>
				<A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/images/do_del.php?PublicationId=<?php  p($Pub); ?>&IssueId=<?php  p($Issue); ?>&SectionId=<?php  p($Section); ?>&ArticleId=<?php  p($Article); ?>&ImageId=<?php echo $image->getImageId(); ?>&ArticleLanguageId=<?php  p($sLanguage); ?>&InterfaceLanguageId=<?php p($Language); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the image \\\'$1\\\'?', htmlspecialchars($image->getDescription())); ?>');"><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/delete.png" BORDER="0" ALT="<?php  putGS('Delete image $1', $image->getDescription()); ?>"></A>
				<?php
			}
			else {
				?>
				&nbsp;
				<?php	
			}
			?>
			</td>
			<?php
	    } // if $User->hasPermission('DeleteImage')
	?>
	</TR>
<?php 
		$imageCount++;
	} // foreach
} // if (count($articleImages) > 0)
?>	
</TABLE>

<?php CampsiteInterface::CopyrightNotice(); ?>