<?php  
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("imagearchive");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/imagearchive/include.inc.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN_DIR/logout.php");
	exit;
}
$ImageId = Input::Get('image_id', 'int', 0);
$view = Input::Get('view', 'string', 'thumbnail', true);
if (!Input::IsValid()) {
	header('Location: index.php?'.$imageNav->getSearchLink());
	exit;	
}
$imageNav =& new ImageNav(CAMPSITE_IMAGEARCHIVE_IMAGES_PER_PAGE, $view);
$imageObj =& new Image($ImageId);
$articles =& ArticleImage::GetArticlesThatUseImage($ImageId);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("Change image information"); ?></TITLE>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
</HEAD>

<BODY>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
<TR>
	<TD class="page_title">
		<?php  
		if ($User->hasPermission('ChangeImage')) {
			putGS('Change image information'); 
		}
		else {
			putGS('View image');
		}
		?>
	</TD>
	<TD ALIGN="RIGHT">
		<A HREF="index.php?<?php echo $imageNav->getSearchLink(); ?>" class="breadcrumb"><?php  putGS("Image Archive");  ?></A>
	</TD>
</TR>
</TABLE>

<table>
<tr>
	<td>
		<img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]); ?>/back.png" border="0">
	<td>
	<td class="action_link">
		<a href="index.php?<?php p($imageNav->getSearchLink()); ?>"><?php putGS('Back to image archive'); ?></a>
	</td>
</tr>
</table>

<IMG SRC="<?php echo $imageObj->getImageUrl(); ?>" BORDER="0" ALT="<?php echo htmlspecialchars($imageObj->getDescription()); ?>" style="padding-left:15px">
<P>
<?php if ($User->hasPermission('ChangeImage')) { ?>
<FORM NAME="dialog" METHOD="POST" ACTION="do_edit.php?<?php echo $imageNav->getSearchLink(); ?>" ENCTYPE="multipart/form-data">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" ALIGN="CENTER" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Change image information"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Description"); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="cDescription" VALUE="<?php echo htmlspecialchars($imageObj->getDescription()); ?>" SIZE="32" MAXLENGTH="128" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Photographer"); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="cPhotographer" VALUE="<?php echo htmlspecialchars($imageObj->getPhotographer());?>" SIZE="32" MAXLENGTH="64" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Place"); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="cPlace" VALUE="<?php echo htmlspecialchars($imageObj->getPlace()); ?>" SIZE="32" MAXLENGTH="64" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Date"); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="cDate" VALUE="<?php echo htmlspecialchars($imageObj->getDate()); ?>" SIZE="11" MAXLENGTH="10" class="input_text"> <?php putGS('YYYY-MM-DD'); ?>
	</TD>
</TR>
<?php
if ($imageObj->getLocation() == 'remote') {
?>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("URL"); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="cURL" VALUE="<?php echo htmlspecialchars($imageObj->getUrl()); ?>" SIZE="32" class="input_text">
	</TD>
</TR>
<?php
} else {
?>
<TR>
	<TD ALIGN="RIGHT"><?php  putGS("Image"); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="cImage" SIZE="32" MAXLENGTH="64" VALUE="<?php echo basename($imageObj->getImageStorageLocation()); ?>" DISABLED class="input_text">
	</TD>
</TR>
<?php
}
?>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="HIDDEN" NAME="image_id" VALUE="<?php echo $imageObj->getImageId(); ?>">
	<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>" class="button">
	<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='index.php?<?php echo $imageNav->getSearchLink(); ?>'" class="button">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<?php
} // if ($User->hasPermission('ChangeImage'))

if (count($articles) > 0) {
	// image is in use //////////////////////////////////////////////////////////////////
	?>
	<center>
	<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" width="500px" class="table_list">
	<tr class="table_list_header">
		<td><?php putGS('Used in articles'); ?>:</td>
	</tr>
	<?php
	$color = 0;
	$previousArticleId = -1;
	foreach ($articles as $article) {
		echo '<tr ';
		if ($color) { 
			$color=0; 
			echo 'class="list_row_even"';
		} else { 
			$color=1; 
			echo 'class="list_row_odd"';
		} 
		echo '>';
		if ($article->getArticleId() == $previousArticleId) {
			echo '<td style="padding-left: 20px;">';
		}
		else {
			echo '<td>';
		}
		echo "<a href=\"/$ADMIN/articles/edit.php?Pub=".$article->getPublicationId().'&Issue='.$article->getIssueId().'&Section='.$article->getSectionId().'&Article='.$article->getArticleId().'&Language='.$article->getLanguageId().'&sLanguage='.$article->getLanguageId().'">'.htmlspecialchars($article->getTitle()).'</a></td></tr>';
		$previousArticleId = $article->getArticleId();
	}
	?>
	</table>
	</center>
<?php
}

camp_html_copyright_notice();
?>