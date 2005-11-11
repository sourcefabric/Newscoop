<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_images");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleImage.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');

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
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;	
}

$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
$articleObj =& new Article($sLanguage, $Article);

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 
				  'Section' => $sectionObj, 'Article'=>$articleObj);
camp_html_content_top(getGS('Article Image List'), $topArray);

?>
<p>
<table class="action_buttons">
<!--<tr>
	<td>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><A HREF="../edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/back.png" BORDER="0"></A></TD>
			<TD><A HREF="../edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><B><?php putGS('Back to Edit Article'); ?></B></A></TD>
		</TR>
		</TABLE>
	</td>
</tr>-->
<?php  if ($User->hasPermission('AddImage')) { ?>
<tr>
	<td>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><A HREF="add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
			<TD><A HREF="add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><B><?php  putGS('Add New Image'); ?></B></A></TD>
		</TR>
		</TABLE>
	</td>
	<td>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><?php echo camp_html_article_link($articleObj, $Language, "images/search.php"); ?><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></a></TD>
			<TD><?php echo camp_html_article_link($articleObj, $Language, "images/search.php"); ?><B><?php  putGS('Add an Existing Image'); ?></B></A></TD>
		</TR>
		</TABLE>
	</td>
</tr>
<?php  } ?>
</table>

<P>
<?php 
$articleImages = ArticleImage::GetImagesByArticleId($articleObj->getArticleNumber());
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
	if ($articleObj->userCanModify($User)) { ?>
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
		$image = $articleImage->getImage();
	?>	
	<TR <?php  if (($imageCount%2)==0) { ?>class="list_row_odd"<?php  } else { ?>class="list_row_even"<?php  } ?>>
		<TD ALIGN="center"><?php echo $articleImage->getTemplateId(); ?></td>

		<?php  
		if ($User->hasPermission('ChangeImage')) { 	
			$imageUrl = "/$ADMIN/articles/images/edit.php?"
				."Pub=$Pub"
				."&Issue=$Issue"
				."&Section=$Section"
				."&Article=$Article"
				."&ImageId=".$image->getImageId()
				."&Language=$Language"
				."&sLanguage=$sLanguage"
				."&ImageTemplateId=".$articleImage->getTemplateId();
		}
		else {
			$imageUrl = camp_html_article_url($articleObj, $Language, "images/view.php", $_SERVER['REQUEST_URI']) .'&ImageId='.$image->getImageId();
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
	    if ($articleObj->userCanModify($User)) { ?>
			<TD ALIGN="CENTER">
				<A HREF="/<?php echo $ADMIN; ?>/articles/images/do_unlink.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php p($Article); ?>&ImageId=<?php echo $image->getImageId(); ?>&ImageTemplateId=<?php echo $articleImage->getTemplateId(); ?>&sLanguage=<?php  p($sLanguage); ?>&Language=<?php  p($Language); ?>" onclick="return confirm('<?php putGS("Are you sure you want to remove the image \\'$1\\' from the article?", camp_javascriptspecialchars($image->getDescription())); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/unlink.png" BORDER="0" ALT="<?php  putGS('Unlink'); ?>" title="<?php  putGS('Unlink'); ?>"></A>
			</TD>
		<?php
	    }
	    if ($User->hasPermission('DeleteImage')) { ?>
			<td align="center">
			<?php 
			if (count(ArticleImage::GetArticlesThatUseImage($image->getImageId())) == 1) {
				?>
				<A HREF="/<?php echo $ADMIN; ?>/articles/images/do_del.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&ImageId=<?php echo $image->getImageId(); ?>&sLanguage=<?php  p($sLanguage); ?>&Language=<?php p($Language); ?>" onclick="return confirm('<?php putGS("Are you sure you want to delete the image \\'$1\\'?", camp_javascriptspecialchars($image->getDescription())); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php  putGS('Delete'); ?>" title="<?php putGS('Delete'); ?>"></A>
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

<?php camp_html_copyright_notice(); ?>